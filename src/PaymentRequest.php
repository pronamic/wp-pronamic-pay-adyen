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
	 * Get array of this Adyen payment request object.
	 *
	 * @return array
	 */
	public function get_array() {
		$array = array(
			'amount'           => array(
				'currency' => $this->currency,
				'value'    => $this->amount_value,
			),
			'merchantAccount'  => $this->merchant_account,
			'paymentMethod'    => $this->payment_method,
			'reference'        => $this->reference,
			'returnUrl'        => $this->return_url,
			'shopperIp'        => $this->shopper_ip,
			'shopperName'      => array(
				'firstName' => $this->shopper_first_name,
				'gender'    => $this->shopper_gender,
				'infix'     => $this->shopper_name_infix,
				'lastName'  => $this->shopper_last_name,
			),
			'shopperLocale'    => $this->shopper_locale,
			'shopperReference' => $this->shopper_reference,
			'shopperStatement' => $this->shopper_statement,
			'telephoneNumber'  => $this->shopper_telephone_number,
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
