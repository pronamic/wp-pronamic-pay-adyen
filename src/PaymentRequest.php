<?php
/**
 * Payment request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment request class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v40/payments
 */
class PaymentRequest extends AbstractPaymentRequest {
	/**
	 * The shopper's browser information.
	 * For 3D Secure 2 transactions, `browserInfo` is required for `channel` web (or `deviceChannel` browser).
	 *
	 * @var BrowserInformation|null
	 */
	private $browser_info;

	/**
	 * The collection that contains the type of the payment method and its
	 * specific information (e.g. idealIssuer).
	 *
	 * @var PaymentMethodDetails
	 */
	private $payment_method;

	/**
	 * Construct a payment request object.
	 *
	 * @param Amount               $amount           The amount information for the transaction.
	 * @param string               $merchant_account The merchant account identifier, with which you want to process the transaction.
	 * @param string               $reference        The reference to uniquely identify a payment.
	 * @param string               $return_url       The URL to return to.
	 * @param PaymentMethodDetails $payment_method   The collection that contains the type of the payment method and its specific information (e.g. idealIssuer).
	 */
	public function __construct( Amount $amount, $merchant_account, $reference, $return_url, PaymentMethodDetails $payment_method ) {
		parent::__construct( $amount, $merchant_account, $reference, $return_url );

		$this->payment_method = $payment_method;
	}

	/**
	 * Get payment method.
	 *
	 * @return PaymentMethodDetails
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Get browser info.
	 *
	 * @return BrowserInformation|null
	 */
	public function get_browser_info() {
		return $this->browser_info;
	}

	/**
	 * Set browser info.
	 *
	 * @param BrowserInformation|null $browser_info Browser info.
	 * @return void
	 */
	public function set_browser_info( $browser_info ) {
		$this->browser_info = $browser_info;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = parent::get_json();

		$properties = (array) $object;

		// Browser information.
		$browser_info = $this->get_browser_info();

		if ( null !== $browser_info ) {
			$properties['browserInfo'] = $browser_info->get_json();
		}

		// Payment method.
		$properties['paymentMethod'] = $this->get_payment_method()->jsonSerialize();

		// Return object.
		$object = (object) $properties;

		return $object;
	}
}
