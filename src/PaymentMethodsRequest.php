<?php
/**
 * Payment methods request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment methods request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/paymentMethods
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
	 * The shopper's country code.
	 *
	 * @var string|null
	 */
	private $country_code;

	/**
	 * The amount information for the transaction.
	 *
	 * @var Amount|null
	 */
	private $amount;

	/**
	 * Construct a payment request object.
	 *
	 * @param string $merchant_account The merchant account identifier.
	 */
	public function __construct( $merchant_account ) {
		$this->merchant_account = $merchant_account;
	}

	/**
	 * Get country code.
	 *
	 * @return string|null
	 */
	public function get_country_code() {
		return $this->country_code;
	}

	/**
	 * Set the shopper's country code.
	 *
	 * @param string|null $country_code The shopper's country code.
	 */
	public function set_country_code( $country_code ) {
		$this->country_code = $country_code;
	}

	/**
	 * Get amount.
	 *
	 * @return Amount|null
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set the amount information for the transaction.
	 *
	 * @param Amount|null $amount The amount information for the transaction.
	 */
	public function set_amount( Amount $amount = null ) {
		$this->amount = $amount;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'merchantAccount' => $this->merchant_account,
				'countryCode'     => $this->get_country_code(),
			)
		);

		if ( null !== $this->get_amount() ) {
			$properties['amount'] = $this->get_amount()->get_json();
		}

		// Return object.
		$object = (object) $properties;

		return $object;
	}
}
