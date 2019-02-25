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
 * Title: Adyen payment request
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v40/payments
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentRequest {
	/**
	 * Allowed payment methods.
	 *
	 * List of payments methods to be presented to the shopper. To refer to payment methods,
	 * use their brandCode from https://docs.adyen.com/developers/payment-methods/payment-methods-overview
	 *
	 * @var array
	 */
	public $allowed_payment_methods;

	/**
	 * The transaction amount needs to be represented in minor units according to the table below.
	 *
	 * Some currencies do not have decimal points, such as JPY, and some have 3 decimal points, such as BHD.
	 * For example, 10 GBP is submitted as 1000, whereas 10 JPY is submitted as 10.
	 *
	 * @link https://docs.adyen.com/developers/development-resources/currency-codes
	 *
	 * @var int
	 */
	public $amount_value;

	/**
	 * Channel.
	 *
	 * The platform where a payment transaction takes place. This field is optional for filtering out
	 * payment methods that are only available on specific platforms. If this value is not set,
	 * then we will try to infer it from the sdkVersion or token.
	 *
	 * Possible values: Android, iOS, Web.
	 *
	 * @var string
	 */
	public $channel;

	/**
	 * Country code (ISO 3166-1 alpha-2).
	 *
	 * @var string
	 */
	public $country_code;

	/**
	 * Currency code.
	 *
	 * @link https://docs.adyen.com/developers/development-resources/currency-codes
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * The merchant account identifier, with which you want to process the transaction.
	 *
	 * @var string
	 */
	public $merchant_account;

	/**
	 * Origin URL.
	 *
	 * Required for the Web integration. Set this parameter to the
	 * origin URL of the page that you are loading the SDK from.
	 *
	 * @var string
	 */
	public $origin_url;

	/**
	 * The collection that contains the type of the payment method and its
	 * specific information (e.g. idealIssuer).
	 *
	 * @var array
	 */
	public $payment_method;

	/**
	 * The reference to uniquely identify a payment. This reference is used in all communication
	 * with you about the payment status. We recommend using a unique value per payment;
	 * however, it is not a requirement. If you need to provide multiple references for
	 * a transaction, separate them with hyphens ("-"). Maximum length: 80 characters.
	 *
	 * @var string
	 */
	public $reference;

	/**
	 * The URL to return to.
	 *
	 * @var string
	 */
	public $return_url;

	/**
	 * SDK version.
	 *
	 * @var string
	 */
	public $sdk_version;

	/**
	 * The shopper IP.
	 *
	 * @var string
	 */
	public $shopper_ip;

	/**
	 * The shopper gender.
	 *
	 * @var string
	 */
	public $shopper_gender;

	/**
	 * The shopper first name.
	 *
	 * @var string
	 */
	public $shopper_first_name;

	/**
	 * The name's infix, if applicable. A maximum length of twenty (20) characters is imposed.
	 *
	 * @var string
	 */
	public $shopper_name_infix;

	/**
	 * The shopper last name.
	 *
	 * @var string
	 */
	public $shopper_last_name;

	/**
	 * The combination of a language code and a country code to specify the language to be used in the payment.
	 *
	 * @var string
	 */
	public $shopper_locale;

	/**
	 * The shopper's reference to uniquely identify this shopper (e.g. user ID or account ID). This field is
	 * required for recurring payments
	 *
	 * @var string
	 */
	public $shopper_reference;

	/**
	 * The text to appear on the shopper's bank statement.
	 *
	 * @var string
	 */
	public $shopper_statement;

	/**
	 * The shopper's telephone number.
	 *
	 * @var string
	 */
	public $shopper_telephone_number;

	/**
	 * Construct a payment request object.
	 *
	 * @param Amount        $amount           The amount information for the transaction.
	 * @param string        $merchant_account The merchant account identifier, with which you want to process the transaction
	 * @param PaymentMethod $payment_method   The collection that contains the type of the payment method and its specific information (e.g. idealIssuer).
	 * @param string        $reference        The reference to uniquely identify a payment.
	 * @param string        $return_url       The URL to return to.
	 */
	public function __construct( Amount $amount, $merchant_account, PaymentMethod $payment_method, $reference, $return_url ) {
		$this->set_amount( $amount );
		$this->set_merchant_account( $merchant_account );
		$this->set_payment_method( $payment_method );
		$this->set_reference( $reference );
		$this->set_return_url( $return_url );
	}

	/**
	 * Get amount.
	 *
	 * @return Amount
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set amount.
	 *
	 * @param Amount $amount Amount.
	 */
	public function set_amount( Amount $amount ) {
		$this->amount = $amount;
	}

	/**
	 * Get merchant account.
	 *
	 * @return string
	 */
	public function get_merchant_account() {
		return $this->merchant_account;
	}

	/**
	 * Set merchant account.
	 *
	 * @param string $merchant_account Merchant account.
	 */
	public function set_merchant_account( $merchant_account ) {
		$this->merchant_account = $merchant_account;
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
	 * Set payment method.
	 *
	 * @param PaymentMethod $payment_method Payment method.
	 */
	public function set_payment_method( PaymentMethod $payment_method ) {
		$this->payment_method = $payment_method;
	}

	/**
	 * Get reference.
	 *
	 * @return string
	 */
	public function get_reference() {
		return $this->reference;
	}

	/**
	 * Set reference.
	 *
	 * @param string $reference Reference.
	 */
	public function set_reference( $reference ) {
		$this->reference = $reference;
	}

	/**
	 * Get return URL.
	 *
	 * @return string
	 */
	public function get_return_url() {
		return $this->return_url;
	}

	/**
	 * Set return URL.
	 *
	 * @param string $return_url Return URL.
	 */
	public function set_return_url( $return_url ) {
		$this->return_url = $return_url;
	}

	/**
	 * Get shopper name.
	 *
	 * @return ShopperName|null
	 */
	public function get_shopper_name() {
		return $this->shopper_name;
	}

	/**
	 * Set shopper name.
	 *
	 * @param ShopperName|null $shopper_name Shopper name.
	 */
	public function set_shopper_name( ShopperName $shopper_name = null ) {
		$this->shopper_name = $shopper_name;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		// Amount.
		$object->amount = $this->get_amount()->get_json();

		// Merchant account.
		$object->merchantAccount = $this->get_merchant_account();

		// Payment method.
		$object->paymentMethod = $this->get_payment_method()->get_json();

		// Reference.
		$object->reference = $this->get_reference();

		// Return URL.
		$object->returnUrl = $this->get_return_url();

		// Shopper name.
		$shopper_name = $this->get_shopper_name();

		if ( null !== $shopper_name ) {
			$object->shopperName = $shopper_name->get_json();
		}

		// Return object.
		return $object;

		$array = array(
			'amount'                => array(
				'currency' => $this->currency,
				'value'    => $this->amount_value,
			),
			'allowedPaymentMethods' => $this->allowed_payment_methods,
			'channel'               => $this->channel,
			'countryCode'           => $this->country_code,
			'merchantAccount'       => $this->merchant_account,
			'origin'                => $this->origin_url,
			'paymentMethod'         => $this->payment_method,
			'reference'             => $this->reference,
			'returnUrl'             => $this->return_url,
			'sdkVersion'            => $this->sdk_version,
			'shopperIp'             => $this->shopper_ip,
			'shopperName'           => array(
				'firstName' => $this->shopper_first_name,
				'gender'    => $this->shopper_gender,
				'infix'     => $this->shopper_name_infix,
				'lastName'  => $this->shopper_last_name,
			),
			'shopperLocale'         => $this->shopper_locale,
			'shopperReference'      => $this->shopper_reference,
			'shopperStatement'      => $this->shopper_statement,
			'telephoneNumber'       => $this->shopper_telephone_number,
		);

		/*
		 * Array filter will remove values NULL, FALSE and empty strings ('')
		 */
		$array['paymentMethod'] = (object) array_filter( $array['paymentMethod'] );
		$array['shopperName']   = (object) array_filter( $array['shopperName'] );
		$array                  = array_filter( $array );

		return $array;
	}
}
