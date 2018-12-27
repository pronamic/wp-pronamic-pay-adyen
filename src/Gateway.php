<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
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
	public function __construct( Pronamic_WP_Pay_GatewayConfig $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );
		$this->set_slug( self::SLUG );

		$this->client = new Adyen();
		$this->client->set_payment_server_url( $config->getPaymentServerUrl() );
		$this->client->set_skin_code( $config->get_buckaroo_skin_code() );
		$this->client->set_merchant_account( $config->get_buckaroo_merchant_account() );
		$this->client->set_shared_secret( $config->get_buckaroo_shared_secret() );
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_Payment $payment
	 * @see Pronamic_WP_Pay_Gateway::start()
	 */
	public function start( Pronamic_Pay_Payment $payment ) {
		$payment->set_transaction_id( md5( time() . $payment->get_order_id() ) );
		$payment->set_action_url( $this->client->get_payment_server_url() );

		$this->client->set_merchant_reference( $payment->get_order_id() );
		$this->client->set_payment_amount( $payment->get_amount() );
		$this->client->set_currency_code( $payment->get_currency() );
		$this->client->set_ship_before_date( new DateTime( '+5 days' ) );
		$this->client->set_shopper_locale( $payment->get_locale() );
		$this->client->set_order_data( $payment->get_description() );
		$this->client->set_session_validity( new DateTime( '+1 hour' ) );
		$this->client->set_shopper_reference( $payment->get_email() );
		$this->client->set_shopper_email( $payment->get_email() );
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
