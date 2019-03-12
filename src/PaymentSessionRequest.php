<?php
/**
 * Payment session request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment session request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentSessionRequest extends AbstractPaymentRequest {
	/**
	 * Allowed payment methods.
	 *
	 * List of payments methods to be presented to the shopper. To refer to payment methods,
	 * use their brandCode from https://docs.adyen.com/developers/payment-methods/payment-methods-overview
	 *
	 * @var array|null
	 */
	private $allowed_payment_methods;

	/**
	 * Origin.
	 *
	 * Required for the Web integration. Set this parameter to the
	 * origin URL of the page that you are loading the SDK from.
	 *
	 * @var string|null
	 */
	private $origin;

	/**
	 * SDK version.
	 *
	 * @var string|null
	 */
	private $sdk_version;

	/**
	 * Construct a payment request object.
	 *
	 * @param Amount $amount           The amount information for the transaction.
	 * @param string $merchant_account The merchant account identifier, with which you want to process the transaction.
	 * @param string $reference        The reference to uniquely identify a payment.
	 * @param string $return_url       The URL to return to.
	 * @param string $country_code     The collection that contains the type of the payment method and its specific information (e.g. idealIssuer).
	 */
	public function __construct( Amount $amount, $merchant_account, $reference, $return_url, $country_code ) {
		parent::__construct( $amount, $merchant_account, $reference, $return_url );

		$this->set_country_code( $country_code );
	}

	/**
	 * Get allowed payment methods.
	 *
	 * @return array|null
	 */
	public function get_allowed_payment_methods() {
		return $this->allowed_payment_methods;
	}

	/**
	 * Set allowed payment methods.
	 *
	 * @param array|null $allowed_payment_methods Allowed payment methods.
	 * @return void
	 */
	public function set_allowed_payment_methods( $allowed_payment_methods ) {
		$this->allowed_payment_methods = $allowed_payment_methods;
	}

	/**
	 * Get origin.
	 *
	 * @return string|null
	 */
	public function get_origin() {
		return $this->origin;
	}

	/**
	 * Set origin.
	 *
	 * @param string|null $origin Origin.
	 * @return void
	 */
	public function set_origin( $origin ) {
		$this->origin = $origin;
	}

	/**
	 * Get SDK version.
	 *
	 * @return string|null
	 */
	public function get_sdk_version() {
		return $this->sdk_version;
	}

	/**
	 * Set SDK version.
	 *
	 * @param string|null $sdk_version SDK version.
	 * @return void
	 */
	public function set_sdk_version( $sdk_version ) {
		$this->sdk_version = $sdk_version;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = parent::get_json();

		$properties = (array) $object;

		// Optional.
		$optional = Util::filter_null(
			array(
				'allowedPaymentMethods' => $this->allowed_payment_methods,
				'origin'                => $this->origin,
				'sdkVersion'            => $this->sdk_version,
			)
		);

		$properties = array_merge( $properties, $optional );

		// Return object.
		$object = (object) $properties;

		return $object;
	}
}
