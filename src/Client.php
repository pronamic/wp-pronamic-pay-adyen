<?php
/**
 * Adyen client
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use WP_Error;

/**
 * Adyen client
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Client {
	/**
	 * Config.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Constructs and initializes an Adyen client object.
	 *
	 * @param Config $config Adyen config.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Send request with the specified action and parameters
	 *
	 * @param string  $method  Adyen API method.
	 * @param Request $request Request object.
	 * @return object
	 * @throws Exception Throws exception when error occurs.
	 */
	private function send_request( $method, $request ) {
		// Request.
		$url = $this->config->get_api_url( $method );

		$response = wp_remote_request(
			$url,
			array(
				'method'  => 'POST',
				'headers' => array(
					'X-API-key'    => $this->config->get_api_key(),
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $request->get_json() ),
			)
		);

		if ( $response instanceof WP_Error ) {
			throw new Exception( $response->get_error_message() );
		}

		// Body.
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		// JSON error.
		$json_error = json_last_error();

		if ( JSON_ERROR_NONE !== $json_error ) {
			throw new Exception(
				sprintf( 'JSON: %s', json_last_error_msg() ),
				$json_error
			);
		}

		// Object.
		if ( ! is_object( $data ) ) {
			$code = wp_remote_retrieve_response_code( $response );

			throw new Exception(
				sprintf( 'Could not JSON decode Adyen response to an object (HTTP Status Code: %s).', $code ),
				intval( $code )
			);
		}

		// Error.
		if ( isset( $data->error ) ) {
			$error = Error::from_object( $data->error );

			throw $error;
		}

		// Service Exception.
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		if ( isset( $data->status, $data->errorCode, $data->message, $data->errorType ) ) {
			$service_exception = ServiceException::from_object( $data );

			throw $service_exception;
		}

		return $data;
	}

	/**
	 * Create payment.
	 *
	 * @param PaymentRequest $request Payment request.
	 *
	 * @return PaymentResponse
	 *
	 * @throws Exception Throws error if request fails.
	 */
	public function create_payment( PaymentRequest $request ) {
		$data = $this->send_request( 'payments', $request );

		return PaymentResponse::from_object( $data );
	}

	/**
	 * Create payment session.
	 *
	 * @param PaymentSessionRequest $request Payment session request.
	 *
	 * @return PaymentSessionResponse
	 *
	 * @throws Exception Throws error if request fails.
	 */
	public function create_payment_session( PaymentSessionRequest $request ) {
		$data = $this->send_request( 'paymentSession', $request );

		return PaymentSessionResponse::from_object( $data );
	}

	/**
	 * Get payment result.
	 *
	 * @param PaymentResultRequest $request Payment result request.
	 *
	 * @return PaymentResultResponse
	 *
	 * @throws Exception Throws error if request fails.
	 */
	public function get_payment_result( PaymentResultRequest $request ) {
		$data = $this->send_request( 'payments/result', $request );

		return PaymentResultResponse::from_object( $data );
	}

	/**
	 * Get payment methods.
	 *
	 * @return PaymentMethodsResponse
	 *
	 * @throws Exception Throws error if request fails.
	 */
	public function get_payment_methods() {
		$request = new PaymentMethodsRequest( $this->config->get_merchant_account() );

		$data = $this->send_request( 'paymentMethods', $request );

		return PaymentMethodsResponse::from_object( $data );
	}
}
