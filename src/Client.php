<?php
/**
 * Adyen client
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Http\Facades\Http;

/**
 * Adyen client class
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 */
class Client {
	/**
	 * Config.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Endpoint.
	 *
	 * @var Endpoint
	 */
	private $endpoint;

	/**
	 * Constructs and initializes an Adyen client object.
	 *
	 * @param Config $config Adyen config.
	 */
	public function __construct( Config $config ) {
		$this->config   = $config;
		$this->endpoint = new Endpoint( $config->environment, $config->api_live_url_prefix );
	}

	/**
	 * Send request with the specified action and parameters
	 *
	 * @param string  $method  Adyen API method.
	 * @param Request $request Request object.
	 * @return object
	 * @throws \Exception Throws exception when error occurs.
	 */
	private function send_request( $method, $request ) {
		// Request.
		$url = $this->endpoint->get_api_url( 'v71', $method );

		$response = Http::request(
			$url,
			[
				'method'  => 'POST',
				'headers' => [
					'X-API-key'    => $this->config->get_api_key(),
					'Content-Type' => 'application/json',
				],
				'body'    => \wp_json_encode( $request->get_json() ),
			]
		);

		$data = $response->json();

		// Object.
		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Could not JSON decode Adyen response to an object, HTTP response: "%s", HTTP body: "%s".',
					\esc_html( (string) $response->status() ),
					\esc_html( $response->body() )
				),
				\intval( $response->status() )
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
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments
	 * @param PaymentRequest $request Payment request.
	 * @return PaymentResponse
	 * @throws \Exception Throws error if request fails.
	 */
	public function create_payment( PaymentRequest $request ) {
		$data = $this->send_request( 'payments', $request );

		return PaymentResponse::from_object( $data );
	}

	/**
	 * Submit additional payment details.
	 *
	 * @param PaymentDetailsRequest $request Payment request.
	 * @return PaymentDetailsResponse
	 * @throws \Exception Throws error if request fails.
	 */
	public function request_payment_details( PaymentDetailsRequest $request ) {
		$data = $this->send_request( 'payments/details', $request );

		return PaymentDetailsResponse::from_object( $data );
	}

	/**
	 * Create payment session.
	 *
	 * @param PaymentSessionRequest $request Payment session request.
	 * @return PaymentSessionResponse
	 * @throws \Exception Throws error if request fails.
	 */
	public function create_payment_session( PaymentSessionRequest $request ) {
		$data = $this->send_request( 'sessions', $request );

		return PaymentSessionResponse::from_object( $data );
	}

	/**
	 * Get payment methods.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/paymentMethods
	 * @param PaymentMethodsRequest $request Payment methods request.
	 * @return PaymentMethodsResponse
	 * @throws \Exception Throws error if request fails.
	 */
	public function get_payment_methods( PaymentMethodsRequest $request ) {
		$data = $this->send_request( 'paymentMethods', $request );

		return PaymentMethodsResponse::from_object( $data );
	}
}
