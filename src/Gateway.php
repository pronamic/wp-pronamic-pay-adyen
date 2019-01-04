<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 * @link    https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 */
class Gateway extends Core_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'adyen';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an InternetKassa gateway
	 *
	 * @param Pronamic_WP_Pay_GatewayConfig $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );
		$this->set_slug( self::SLUG );

		$this->client = new Adyen();
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_Payment $payment
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Payment $payment ) {
		$url = 'https://checkout-test.adyen.com/v40/paymentMethods';

		$data = (object) array(
			'merchantAccount'       => $this->config->merchant_account,
			'allowedPaymentMethods' => array(
				// 'ideal',
			),
		);

		$response = wp_remote_post( $url, array(
			'headers' => array(
				'X-API-key'    => $this->config->api_key,
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( $data ),
		) );

		$body = wp_remote_retrieve_body( $response );

		$result = json_decode( $body );

		$payment_methods = $result->paymentMethods;
		$payment_method  = reset( $payment_methods );

		$url = 'https://checkout-test.adyen.com/v40/payments';

		$data = (object) array(
			'amount'          => (object) array(
				'currency' => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
				'value'    => $payment->get_total_amount()->get_cents(),
			),
			'reference'       => $payment->get_id(),
			'paymentMethod'   => (object) array(
				'type' => 'ideal',
			),
			'returnUrl'       => $payment->get_return_url(),
			'merchantAccount' => $this->config->merchant_account,
		);

		$response = wp_remote_post( $url, array(
			'headers' => array(
				'X-API-key'    => $this->config->api_key,
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( $data ),
		) );

		if ( '200' !== strval( wp_remote_retrieve_response_code( $response ) ) ) {
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		$result = json_decode( $body );

		if ( isset( $result->redirect, $result->redirect->url ) ) {
			$payment->set_action_url( $result->redirect->url );
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get output HTML
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_html() {
		return $this->client->get_html_fields();
	}
}
