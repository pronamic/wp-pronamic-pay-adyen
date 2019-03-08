<?php
/**
 * Payment methods request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment methods request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentMethods
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodsRequest extends Request {
	/**
	 * The merchant account identifier, with which you want to process the transaction.
	 *
	 * @var string
	 */
	private $merchant_account;

	/**
	 * Construct a payment request object.
	 *
	 * @param string $merchant_account The merchant account identifier.
	 */
	public function __construct( $merchant_account ) {
		$this->merchant_account = $merchant_account;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array(
			'merchantAccount' => $this->merchant_account,
		);

		// Return object.
		return $object;
	}
}
