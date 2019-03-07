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
	 * Error
	 *
	 * @var WP_Error
	 */
	private $error;

	/**
	 * Constructs and initializes an Adyen client object.
	 *
	 * @param Config $config Adyen config.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Error
	 *
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Send request with the specified action and parameters
	 *
	 * @param string       $end_point              Requested endpoint.
	 * @param string       $method                 HTTP method to use.
	 * @param array|object $data                   Request data.
	 * @param int          $expected_response_code Expected response code.
	 *
	 * @return bool|object
	 */
	private function send_request( $end_point, $method = 'GET', $data = null, $expected_response_code = 200 ) {
		// Request.
		$url = $this->config->get_api_url() . $end_point;

		$response = wp_remote_request(
			$url,
			array(
				'method'  => $method,
				'headers' => array(
					'X-API-key'    => $this->config->get_api_key(),
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $data ),
			)
		);

		// Response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $expected_response_code != $response_code ) { // WPCS: loose comparison ok.
			$this->error = new WP_Error( 'adyen_error', 'Unexpected response code.' );
		}

		// Body.
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( ! is_object( $data ) ) {
			$this->error = new WP_Error( 'adyen_error', 'Could not parse response.' );

			return false;
		}

		// Adyen error.
		if ( isset( $data->errorCode, $data->message ) ) {
			$message = sprintf(
				'%1$s %2$s - %3$s',
				$data->status,
				$data->errorCode,
				$data->message
			);

			$this->error = new WP_Error( 'adyen_error', $message, $data->errorCode );

			return false;
		}

		return $data;
	}

	/**
	 * Create payment.
	 *
	 * @param PaymentRequest $request Payment request.
	 *
	 * @return bool|object
	 */
	public function create_payment( PaymentRequest $request ) {
		return $this->send_request( 'payments/', 'POST', $request->get_json(), 200 );
	}

	/**
	 * Create payment session.
	 *
	 * @param PaymentSessionRequest $request Payment session request.
	 *
	 * @return bool|object
	 */
	public function create_payment_session( PaymentSessionRequest $request ) {
		return $this->send_request( 'paymentSession', 'POST', $request->get_json(), 200 );
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

		return $this->send_request( 'payments/details', 'POST', $data );
	}

	/**
	 * Get payment result.
	 *
	 * @param string $payload Payload to get payment details for.
	 *
	 * @return bool|object
	 */
	public function get_payment_result( $payload ) {
		if ( empty( $payload ) ) {
			return false;
		}

		$data = array(
			'payload' => $payload,
		);

		return $this->send_request( 'payments/result', 'POST', $data );
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

		$response = $this->send_request( 'paymentMethods/', 'POST', $data );

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
