<?php
/**
 * Payment request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v40/payments
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentRequest extends AbstractPaymentRequest {
	/**
	 * The collection that contains the type of the payment method and its
	 * specific information (e.g. idealIssuer).
	 *
	 * @var PaymentMethod
	 */
	private $payment_method;

	/**
	 * Construct a payment request object.
	 *
	 * @param Amount        $amount           The amount information for the transaction.
	 * @param string        $merchant_account The merchant account identifier, with which you want to process the transaction.
	 * @param string        $reference        The reference to uniquely identify a payment.
	 * @param string        $return_url       The URL to return to.
	 * @param PaymentMethod $payment_method   The collection that contains the type of the payment method and its specific information (e.g. idealIssuer).
	 */
	public function __construct( Amount $amount, $merchant_account, $reference, $return_url, PaymentMethod $payment_method ) {
		parent::__construct( $amount, $merchant_account, $reference, $return_url );

		$this->payment_method = $payment_method;
	}

	/**
	 * Get payment method.
	 *
	 * @return PaymentMethod
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = parent::get_json();

		$properties = (array) $object;

		// Payment method.
		$properties['paymentMethod'] = $this->get_payment_method()->get_json();

		// Return object.
		$object = (object) $properties;

		return $object;
	}
}
