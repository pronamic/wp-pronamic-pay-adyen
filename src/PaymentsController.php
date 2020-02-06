<?php
/**
 * Payments controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Exception\ValidationException;
use Pronamic\WordPress\Pay\Plugin;
use WP_REST_Request;

/**
 * Payments result controller
 *
 * @link https://docs.adyen.com/developers/checkout/web-sdk/customization/logic#beforecomplete
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class PaymentsController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 *
	 * @return void
	 */
	public function rest_api_init() {
		// Register REST route `/payments//{payment_id}`.
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/payments/(?P<payment_id>\d+)',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'rest_api_adyen_payments' ),
				'args'     => array(
					'payment_id' => array(
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
					),
				),
			)
		);

		// Register REST route `/payments/details/{payment_id}`.
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/payments/details/(?P<payment_id>\d+)',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'rest_api_adyen_payment_details' ),
				'args'     => array(
					'payment_id' => array(
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
					),
				),
			)
		);
	}

	/**
	 * REST API Adyen payments handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_payments( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		// Payment ID.
		if ( null === $payment_id ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-id',
				__( 'No payment ID given in `payment_id` parameter.', 'pronamic_ideal' )
			);
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new \WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
					/* translators: %s: payment ID */
					__( 'Could not find payment with ID `%s`.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
			);
		}

		// State data.
		$data = \json_decode( $request->get_body() );

		if ( null === $data ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-data',
				__( 'No data given in request body.', 'pronamic_ideal' )
			);
		}

		// Gateway.
		$config_id = $payment->get_config_id();

		if ( null === $config_id ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-config',
				__( 'No gateway configuration ID given in payment.', 'pronamic_ideal' )
			);
		}

		$gateway = Plugin::get_gateway( $config_id );

		if ( empty( $gateway ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-gateway-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not find gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		if ( ! isset( $gateway->client ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-client-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not find client in gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		// Create payment.
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		if ( ! isset( $data->paymentMethod->type ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-method',
				__( 'No payment method given.', 'pronamic_ideal' )
			);
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		$payment_method = PaymentMethod::from_object( $data->paymentMethod );

		try {
			if ( ! \is_callable( array( $gateway, 'create_payment' ) ) ) {
				return (object) array(
					'error' => __( 'Gateway does not support method to create payment.', 'pronamic_ideal' ),
				);
			}

			try {
				$response = $gateway->create_payment( $payment, $payment_method );
			} catch ( \Pronamic\WordPress\Pay\Gateways\Adyen\ServiceException $service_exception ) {
				$message = $service_exception->getMessage();

				$error_code = $service_exception->get_error_code();

				if ( ! empty( $error_code ) ) {
					$message = sprintf(
						/* translators: 1: error message, 2: error code */
						__( '%1$s (error %2$s)', 'pronamic_ideal' ),
						$service_exception->getMessage(),
						$error_code
					);
				}

				throw new \Exception( $message );
			}
		} catch ( \Exception $e ) {
			$error = $e->getMessage();

			$error_code = $e->getCode();

			if ( ! empty( $error_code ) ) {
				$error = sprintf( '%s - %s', $error_code, $e->getMessage() );
			}

			return (object) array( 'error' => $error );
		}

		// Update payment status based on response.
		PaymentResponseHelper::update_payment( $payment, $response );

		$result = array(
			'resultCode' => $response->get_result_code(),
		);

		// Return action if available.
		$action = $response->get_action();

		if ( null !== $action ) {
			$result['action'] = $action->get_json();
		}

		// Return refusal reason if available.
		$refusal_reason = $response->get_refusal_reason();

		if ( null !== $refusal_reason ) {
			$result['refusalReason'] = $refusal_reason;
		}

		return (object) $result;
	}

	/**
	 * REST API Adyen payment details handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_payment_details( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		// Payment ID.
		if ( null === $payment_id ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-id',
				__( 'No payment ID given in `payment_id` parameter.', 'pronamic_ideal' )
			);
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new \WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
					/* translators: %s: payment ID */
					__( 'Could not find payment with ID `%s`.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
			);
		}

		// State data.
		$data = \json_decode( $request->get_body() );

		if ( null === $data ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-data',
				__( 'No data given in request body.', 'pronamic_ideal' )
			);
		}

		// Gateway.
		$config_id = $payment->get_config_id();

		if ( null === $config_id ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-config',
				__( 'No gateway configuration ID given in payment.', 'pronamic_ideal' )
			);
		}

		$gateway = Plugin::get_gateway( $config_id );

		if ( empty( $gateway ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-gateway-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not find gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		if ( ! isset( $gateway->client ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-client-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not find client in gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		if ( ! isset( $data->paymentMethod->type ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-method',
				__( 'No payment method given.', 'pronamic_ideal' )
			);
		}

		// Send additional payment details.
		$payment_details_request = new PaymentDetailsRequest();

		// Set payment data from original payment response.
		$payment_response = $payment->get_meta( 'adyen_payment_response' );

		if ( is_string( $payment_response ) && '' !== $payment_response ) {
			$payment_response = \json_decode( $payment_response );

			$payment_response = PaymentResponse::from_object( $payment_response );

			$payment_data = $payment_response->get_payment_data();

			$payment_details_request->set_payment_data( $payment_data );
		}

		try {
			if ( ! \is_callable( array( $gateway, 'send_payment_details' ) ) ) {
				return (object) array(
					'error' => __( 'Gateway does not support sending additional payment details.', 'pronamic_ideal' ),
				);
			}

			try {
				$response = $gateway->send_payment_details( $payment_details_request );
			} catch ( \Pronamic\WordPress\Pay\Gateways\Adyen\ServiceException $service_exception ) {
				$message = $service_exception->getMessage();

				$error_code = $service_exception->get_error_code();

				if ( ! empty( $error_code ) ) {
					$message = sprintf(
					/* translators: 1: error message, 2: error code */
						__( '%1$s (error %2$s)', 'pronamic_ideal' ),
						$service_exception->getMessage(),
						$error_code
					);
				}

				throw new \Exception( $message );
			}

			// Update payment status based on response.
			PaymentResponseHelper::update_payment( $payment, $response );
		} catch ( \Exception $e ) {
			$error = $e->getMessage();

			$error_code = $e->getCode();

			if ( ! empty( $error_code ) ) {
				$error = sprintf( '%s - %s', $error_code, $e->getMessage() );
			}

			return (object) array( 'error' => $error );
		}

		$result = array(
			'resultCode' => $response->get_result_code(),
		);

		// Return action if available.
		$action = $response->get_action();

		if ( null !== $action ) {
			$result['action'] = $action->get_json();
		}

		// Return refusal reason if available.
		$refusal_reason = $response->get_refusal_reason();

		if ( null !== $refusal_reason ) {
			$result['refusalReason'] = $refusal_reason;
		}

		return (object) $result;
	}
}
