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
use Pronamic\WordPress\Pay\Plugin;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 * @link    https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 */
class Gateway extends Core_Gateway {
	/**
	 * Slug of this gateway.
	 *
	 * @var string
	 */
	const SLUG = 'adyen';

	/**
	 * Constructs and initializes an Adyen gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );
		$this->set_slug( self::SLUG );

		$this->client = new Client( $config->api_key, $config->api_live_url_prefix );
		$this->client->set_merchant_account( $config->merchant_account );
		$this->client->set_mode( $config->mode );
	}

	/**
	 * Start.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @see Plugin::start()
	 */
	public function start( Payment $payment ) {
		// Payment request.
		$request = new PaymentRequest();

		$request->merchant_account = $this->config->merchant_account;
		$request->return_url       = $payment->get_return_url();
		$request->reference        = $payment->get_id();

		// Amount.
		$request->currency     = $payment->get_total_amount()->get_currency()->get_alphabetic_code();
		$request->amount_value = $payment->get_total_amount()->get_cents(); // @todo Money +get_minor_units().

		// Payment method. Take leap of faith for unknown payment method types.
		$request->payment_method = array(
			'type' => PaymentMethodType::transform( $payment->get_method(), $payment->get_method() ),
		);

		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
				$request->payment_method['issuer'] = $payment->get_issuer();

				break;
		}

		// Shopper.
		$request->shopper_statement = $payment->get_description();

		if ( null !== $payment->get_customer() ) {
			$request->shopper_ip               = $payment->get_customer()->get_ip_address();
			$request->shopper_gender           = $payment->get_customer()->get_gender();
			$request->shopper_locale           = $payment->get_customer()->get_locale();
			$request->shopper_reference        = $payment->get_customer()->get_user_id();
			$request->shopper_telephone_number = $payment->get_customer()->get_phone();

			if ( null !== $payment->get_customer()->get_name() ) {
				$request->shopper_first_name = $payment->get_customer()->get_name()->get_first_name();
				$request->shopper_name_infix = $payment->get_customer()->get_name()->get_middle_name();
				$request->shopper_last_name  = $payment->get_customer()->get_name()->get_last_name();
			}
		}

		// Create payment.
		$result = $this->client->create_payment( $request );

		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return;
		}

		// Set transaction ID.
		if ( isset( $result->pspReference ) ) {
			$payment->set_transaction_id( $result->pspReference );
		}

		// Set redirect URL.
		if ( isset( $result->redirect->url ) ) {
			$payment->set_action_url( $result->redirect->url );
		}
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 */
	public function update_status( Payment $payment ) {
		if ( ! filter_has_var( INPUT_GET, 'payload' ) ) {
			return;
		}

		$payload = filter_input( INPUT_GET, 'payload', FILTER_SANITIZE_STRING );

		$payment_details = $this->client->get_payment_details( $payload );

		if ( ! $payment_details ) {
			$payment->set_status( Core_Statuses::FAILURE );

			$this->error = $this->client->get_error();

			return;
		}

		$payment->set_status( Statuses::transform( $payment_details->resultCode ) );

		if ( isset( $payment_details->pspReference ) ) {
			$payment->set_transaction_id( $payment_details->pspReference );
		}
	}

	/**
	 * Is payment method required to start transaction?
	 *
	 * @see Core_Gateway::payment_method_is_required()
	 */
	public function payment_method_is_required() {
		return true;
	}

	/**
	 * Get supported payment methods
	 *
	 * @see Core_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::IDEAL,
			PaymentMethods::SOFORT,
		);
	}

	/**
	 * Get issuers.
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$payment_method = PaymentMethodType::transform( PaymentMethods::IDEAL );

		$result = $this->client->get_issuers( $payment_method );

		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return $groups;
		}

		$groups[] = array(
			'options' => $result,
		);

		return $groups;
	}
}
