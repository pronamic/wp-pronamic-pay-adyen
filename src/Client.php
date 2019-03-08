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
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\XML\Security;
use WP_Error;

/**
 * Adyen client
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 * @link    https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
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
	 * @param string $method Adyen API method.
	 * @param object $data   Request data.
	 * @return object
	 * @throws Exception Throws exception when error occurs.
	 */
	private function send_request( $method, $data ) {
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
				'body'    => wp_json_encode( $data ),
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
				json_last_error_msg(),
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
	 * @return PaymentResponse
	 */
	public function create_payment( PaymentRequest $request ) {
		$data = $this->send_request( 'payments', $request->get_json() );

		return PaymentResponse::from_object( $data );
	}

	/**
	 * Create payment session.
	 *
	 * @param PaymentSessionRequest $request Payment session request.
	 * @return PaymentSessionResponse
	 */
	public function create_payment_session( PaymentSessionRequest $request ) {
		$data = $this->send_request( 'paymentSession', $request->get_json() );

		return PaymentSessionResponse::from_object( $data );
	}

	/**
	 * Get payment details.
	 *
	 * @param string $payload Payload to get payment details for.
	 *
	 * @return bool|object
	 */
	public function get_payment_details( $payload ) {
		if ( empty( $payload ) ) {
			return false;
		}

		$data = array(
			'details' => array(
				'payload' => $payload,
			),
		);

		return $this->send_request( 'payments/details', $data );
	}

	/**
	 * Get payment result.
	 *
	 * @param PaymentResultRequest $request Payment result request.
	 * @return PaymentResultResponse
	 */
	public function get_payment_result( PaymentResultRequest $request ) {
		$data = $this->send_request( 'payments/result', $request->get_json() );

		return PaymentResultResponse::from_object( $data );
	}

	/**
	 * Get issuers.
	 *
	 * @param string $payment_method Payment method.
	 *
	 * @return array|bool
	 */
	public function get_issuers( $payment_method = null ) {
		// Check payment method.
		if ( empty( $payment_method ) ) {
			return false;
		}

		// Get issuers.
		$methods = $this->get_payment_methods();

		if ( false === $methods ) {
			return false;
		}

		$issuers = array();

		foreach ( $methods as $method_type => $method ) {
			if ( $payment_method !== $method_type ) {
				continue;
			}

			if ( ! isset( $method['details']['issuer'] ) ) {
				return false;
			}

			foreach ( $method['details']['issuer']->items as $issuer ) {
				$id   = Security::filter( $issuer->id );
				$name = Security::filter( $issuer->name );

				$issuers[ $id ] = $name;
			}
		}

		return $issuers;
	}

	/**
	 * Get payment methods.
	 *
	 * @return array|bool
	 */
	public function get_payment_methods() {
		$data = array(
			'merchantAccount'       => $this->config->get_merchant_account(),
			'allowedPaymentMethods' => array(),
		);

		$response = $this->send_request( 'paymentMethods', $data );

		if ( false === $response ) {
			return false;
		}

		$payment_methods = array();

		if ( isset( $response->paymentMethods ) ) {
			foreach ( $response->paymentMethods as $payment_method ) {
				$type = Security::filter( $payment_method->type );
				$name = Security::filter( $payment_method->name );

				$method = array(
					'name'    => $name,
					'details' => array(),
				);

				if ( isset( $payment_method->details ) ) {
					foreach ( $payment_method->details as $detail ) {
						$key = $detail->key;

						$method['details'][ $key ] = $detail;

						unset( $method['details'][ $key ]->key );
					}
				}

				$payment_methods[ $type ] = $method;
			}
		}

		return $payment_methods;
	}
}
