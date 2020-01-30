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
					'payment_id'  => array(
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
					'payment_id'  => array(
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

		$gateway   = Plugin::get_gateway( $config_id );

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
		if ( ! isset( $data->paymentMethod->type ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-method',
				__( 'No payment method given.', 'pronamic_ideal' )
			);
		}

		$payment_method = PaymentMethod::from_object( $data->paymentMethod );

		try {
			$response = $gateway->create_payment( $payment, $payment_method );
		} catch ( \Exception $e ) {
			return (object) array(
				'error' => $e->getMessage(),
			);
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

		$gateway   = Plugin::get_gateway( $config_id );

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

		// Send additional details.
		if ( ! isset( $data->paymentMethod->type ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-payment-method',
				__( 'No payment method given.', 'pronamic_ideal' )
			);
		}

		// Payment details request.
		$payment_details_request = new PaymentDetailsRequest();

		$payment_details_request->set_payment_data( $data );

		try {
			$response = $gateway->send_payment_details( $payment, $payment_details_request );

			// Update payment status based on response.
			PaymentResponseHelper::update_payment( $payment, $response );
		} catch ( \Exception $e ) {
			return (object) array(
				'error' => $e->getMessage(),
			);
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
