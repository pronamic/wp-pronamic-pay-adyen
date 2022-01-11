<?php
/**
 * Payments controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Plugin;
use WP_REST_Request;

/**
 * Payments result controller
 *
 * @link https://docs.adyen.com/developers/checkout/web-sdk/customization/logic#beforecomplete
 *
 * @author  Reüel van der Steege
 * @version 1.1.2
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
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_adyen_payments' ),
				'permission_callback' => '__return_true',
				'args'                => array(
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
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_adyen_payment_details' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'payment_id' => array(
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
					),
				),
			)
		);

		// Register REST route `/payments/applepay/merchant-validation/`.
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/payments/applepay/merchant-validation/(?P<payment_id>\d+)',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_applepay_merchant_validation' ),
				'permission_callback' => '__return_true',
				'args'                => array(
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
	 * @throws \Exception Throws exception on Adyen service exception response.
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

		if ( ! $gateway instanceof DropInGateway ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-drop-in',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Unable to handle payment `%s` because it was not processed through the Adyen drop-in integration.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
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

		try {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
			$payment_method = PaymentMethod::from_object( $data->paymentMethod );

			$response = $gateway->create_payment( $payment, $payment_method, $data );

			// Update payment status based on response.
			PaymentResponseHelper::update_payment( $payment, $response );

			return $this->get_response_result( $response );
		} catch ( \Exception $exception ) {
			$message = $exception->getMessage();
			$code    = $exception->getCode();

			if ( $exception instanceof \Pronamic\WordPress\Pay\Gateways\Adyen\ServiceException ) {
				$code = $exception->get_error_code();
			}

			if ( ! empty( $code ) ) {
				$message = sprintf(
					/* translators: 1: error message, 2: error code */
					__( '%1$s (error %2$s)', 'pronamic_ideal' ),
					$message,
					$code
				);
			}

			// Add payment note with error message.
			try {
				$payment->add_note( $message );
			} catch ( \Exception $e ) {
				$message = \sprintf(
					'%1$s. %2$s',
					$message,
					__( 'Error message could not be logged.', 'pronamic_ideal' )
				);
			}

			return new \WP_Error( 'pronamic-pay-adyen-create-payment-failed', $message );
		}
	}

	/**
	 * REST API Adyen payment details handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception on Adyen service exception response.
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

		if ( ! $gateway instanceof DropInGateway ) {
			return new \WP_Error(
				'pronamic-pay-adyen-no-drop-in',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Unable to handle payment `%s` because it was not processed through the Adyen drop-in integration.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
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

		// Send additional payment details.
		$payment_details_request = new PaymentDetailsRequest();

		$payment_details_request->set_details( $data->details );

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		$payment_details_request->set_payment_data( $data->paymentData );

		try {
			$response = $gateway->send_payment_details( $payment_details_request );

			// Update payment status based on response.
			PaymentResponseHelper::update_payment( $payment, $response );

			return $this->get_response_result( $response );
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'pronamic-pay-adyen-exception',
				$e->getMessage(),
				$e
			);
		}
	}

	/**
	 * REST API Apple Pay merchant validation handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception on merchant identity files problems.
	 */
	public function rest_api_applepay_merchant_validation( WP_REST_Request $request ) {
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

		// Merchant identifier.
		$integration = new Integration();

		$config = $integration->get_config( $config_id );

		$merchant_identifier = $config->get_apple_pay_merchant_id();

		if ( empty( $merchant_identifier ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-applepay-no-merchant-identifier',
				__( 'Apple Pay merchant identifier not configured in gateway settings.', 'pronamic_ideal' )
			);
		}

		if ( ! isset( $data->validation_url ) ) {
			return new \WP_Error(
				'pronamic-pay-adyen-applepay-no-validation-url',
				__( 'No Apple Pay merchant validation URL given.', 'pronamic_ideal' )
			);
		}

		/*
		 * Request an Apple Pay payment session.
		 *
		 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaysession/1778021-onvalidatemerchant
		 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/apple_pay_js_api/requesting_an_apple_pay_payment_session
		 * @link https://docs.adyen.com/payment-methods/apple-pay/web-drop-in#show-apple-pay-in-your-payment-form
		 */
		$request = array(
			'merchantIdentifier' => $merchant_identifier,
			'displayName'        => \get_bloginfo( 'name' ),
			'initiative'         => 'web',
			'initiativeContext'  => Server::get( 'HTTP_HOST', FILTER_SANITIZE_STRING ),
		);

		try {
			add_action( 'http_api_curl', array( $this, 'http_curl_applepay_merchant_identity' ), 10, 2 );

			$certificate = $config->get_apple_pay_merchant_id_certificate();
			$private_key = $config->get_apple_pay_merchant_id_private_key();

			if ( empty( $certificate ) || empty( $private_key ) ) {
				throw new \Exception( __( 'Invalid Apple Pay Merchant Identity configuration.', 'pronamic_ideal' ) );
			}

			// Create temporary files for merchant validation.
			$certificate_file = \tmpfile();
			$private_key_file = \tmpfile();

			if ( false === $certificate_file || false === $private_key_file ) {
				throw new \Exception( __( 'Error creating merchant identity files.', 'pronamic_ideal' ) );
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite -- Temporary files.
			\fwrite( $certificate_file, $certificate );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite -- Temporary files.
			\fwrite( $private_key_file, $private_key );

			// Validate merchant.
			$response = \wp_remote_request(
				$data->validation_url,
				array(
					'method'                           => 'POST',
					'headers'                          => array(
						'Content-Type' => 'application/json',
					),
					'body'                             => \wp_json_encode( (object) $request ),
					'adyen_applepay_merchant_identity' => array(
						'certificate_path'     => stream_get_meta_data( $certificate_file )['uri'],
						'private_key_path'     => stream_get_meta_data( $private_key_file )['uri'],
						'private_key_password' => $config->get_apple_pay_merchant_id_private_key_password(),
					),
				)
			);

			// Remove temporary files.
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose -- Temporary files.
			\fclose( $certificate_file );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose -- Temporary files.
			\fclose( $private_key_file );

			$body = \wp_remote_retrieve_body( $response );

			$result = \json_decode( $body );
		} catch ( \Exception $e ) {
			$error = $e->getMessage();

			$error_code = $e->getCode();

			if ( ! empty( $error_code ) ) {
				$error = sprintf( '%s - %s', $error_code, $e->getMessage() );
			}

			return (object) array( 'error' => $error );
		}

		return (object) $result;
	}

	/**
	 * HTTP CURL options for Apple Pay merchant validation.
	 *
	 * @param resource             $handle      CURL handle.
	 * @param array<array<string>> $parsed_args Parsed arguments.
	 * @return void
	 * @throws \Exception Throws exception on error while reading temporary files.
	 */
	public function http_curl_applepay_merchant_identity( $handle, $parsed_args ) {
		if ( ! isset( $parsed_args['adyen_applepay_merchant_identity'] ) ) {
			return;
		}

		$merchant_identity = $parsed_args['adyen_applepay_merchant_identity'];

		$certificate_path     = $merchant_identity['certificate_path'];
		$private_key_path     = $merchant_identity['private_key_path'];
		$private_key_password = $merchant_identity['private_key_password'];

		// Check temporary files existence.
		if ( ! \is_readable( $certificate_path ) || ! \is_readable( $private_key_path ) ) {
			throw new \Exception( __( 'Error reading merchant identity files.', 'pronamic_ideal' ) );
		}

		// Set merchant identity certificate and private key SSL options.
		// phpcs:disable WordPress.WP.AlternativeFunctions.curl_curl_setopt
		\curl_setopt( $handle, CURLOPT_SSLCERT, $certificate_path );
		\curl_setopt( $handle, CURLOPT_SSLKEY, $private_key_path );

		// Set merchant identity private key password.
		if ( ! empty( $private_key_password ) ) {
			\curl_setopt( $handle, CURLOPT_SSLKEYPASSWD, $private_key_password );
		}

		// phpcs:enable WordPress.WP.AlternativeFunctions.curl_curl_setopt
	}

	/**
	 * Get payment response result for drop-in to handle.
	 *
	 * @param PaymentResponse $response Response.
	 * @return object
	 */
	private function get_response_result( PaymentResponse $response ) {
		$result = array(
			'resultCode' => $response->get_result_code(),
		);

		// Set action.
		$action = $response->get_action();

		if ( null !== $action ) {
			$result['action'] = $action->get_json();
		}

		// Set refusal reason.
		$refusal_reason = $response->get_refusal_reason();

		if ( null !== $refusal_reason ) {
			$result['refusalReason'] = $refusal_reason;
		}

		return (object) $result;
	}
}
