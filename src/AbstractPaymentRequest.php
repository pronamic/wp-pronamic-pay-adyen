<?php
/**
 * Abstract payment request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use DateTime;
use InvalidArgumentException;

/**
 * Abstract payment request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
abstract class AbstractPaymentRequest extends Request {
	/**
	 * Amount.
	 *
	 * @var Amount
	 */
	private $amount;

	/**
	 * Billing address.
	 *
	 * @var Address|null
	 */
	private $billing_address;

	/**
	 * Channel.
	 *
	 * The platform where a payment transaction takes place. This field is optional for filtering out
	 * payment methods that are only available on specific platforms. If this value is not set,
	 * then we will try to infer it from the sdkVersion or token.
	 *
	 * Possible values: Android, iOS, Web.
	 *
	 * @var string|null
	 */
	private $channel;

	/**
	 * The shopper country.
	 *
	 * Format: ISO 3166-1 alpha-2 Example: NL or DE
	 *
	 * @var string|null
	 */
	private $country_code;

	/**
	 * Date of birth.
	 *
	 * @var DateTime|null
	 */
	private $date_of_birth;

	/**
	 * The address where the purchased goods should be delivered
	 *
	 * @var Address|null
	 */
	private $delivery_address;

	/**
	 * Line items regarding the payment.
	 *
	 * @var LineItems|null
	 */
	private $line_items;

	/**
	 * The merchant account identifier, with which you want to process the transaction.
	 *
	 * @var string
	 */
	private $merchant_account;

	/**
	 * The reference to uniquely identify a payment. This reference is used in all communication
	 * with you about the payment status. We recommend using a unique value per payment;
	 * however, it is not a requirement. If you need to provide multiple references for
	 * a transaction, separate them with hyphens ("-"). Maximum length: 80 characters.
	 *
	 * @var string
	 */
	private $reference;

	/**
	 * The URL to return to.
	 *
	 * @var string
	 */
	private $return_url;

	/**
	 * The shopper's IP address.
	 *
	 * @var string|null
	 */
	private $shopper_ip;

	/**
	 * The combination of a language code and a country code to specify the language to be used in the payment.
	 *
	 * @var string|null
	 */
	private $shopper_locale;

	/**
	 * The shopper's full name and gender (if specified)
	 *
	 * @var Name|null
	 */
	private $shopper_name;

	/**
	 * The shopper's reference to uniquely identify this shopper (e.g. user ID or account ID). This field is
	 * required for recurring payments
	 *
	 * @var string|null
	 */
	private $shopper_reference;

	/**
	 * The text to appear on the shopper's bank statement.
	 *
	 * @var string|null
	 */
	private $shopper_statement;

	/**
	 * The shopper's telephone number
	 *
	 * @var string|null
	 */
	private $telephone_number;

	/**
	 * Construct a payment request object.
	 *
	 * @param Amount $amount           The amount information for the transaction.
	 * @param string $merchant_account The merchant account identifier, with which you want to process the transaction.
	 * @param string $reference        The reference to uniquely identify a payment.
	 * @param string $return_url       The URL to return to.
	 */
	public function __construct( Amount $amount, $merchant_account, $reference, $return_url ) {
		$this->amount           = $amount;
		$this->merchant_account = $merchant_account;
		$this->reference        = $reference;
		$this->return_url       = $return_url;
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
	 * Get billing address.
	 *
	 * @return Address|null
	 */
	public function get_billing_address() {
		return $this->billing_address;
	}

	/**
	 * Set billing address.
	 *
	 * @param Address|null $billing_address Billing address.
	 * @return void
	 */
	public function set_billing_address( Address $billing_address = null ) {
		$this->billing_address = $billing_address;
	}

	/**
	 * Get channel.
	 *
	 * @return string|null
	 */
	public function get_channel() {
		return $this->channel;
	}

	/**
	 * Set channel.
	 *
	 * @param string|null $channel Channel.
	 * @return void
	 */
	public function set_channel( $channel ) {
		$this->channel = $channel;
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
	 * Set country code.
	 *
	 * @param string|null $country_code Country code.
	 * @return void
	 * @throws InvalidArgumentException Throws invalid argument exception when country code is not 2 characters.
	 */
	public function set_country_code( $country_code ) {
		if ( null !== $country_code && 2 !== strlen( $country_code ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Given country code `%s` not ISO 3166-1 alpha-2 value.',
					$country_code
				)
			);
		}

		$this->country_code = $country_code;
	}

	/**
	 * Get date of birth.
	 *
	 * @return DateTime|null
	 */
	public function get_date_of_birth() {
		return $this->date_of_birth;
	}

	/**
	 * Set date of birth.
	 *
	 * @param DateTime|null $date_of_birth Date of birth.
	 * @return void
	 */
	public function set_date_of_birth( DateTime $date_of_birth = null ) {
		$this->date_of_birth = $date_of_birth;
	}

	/**
	 * Get delivery address.
	 *
	 * @return Address|null
	 */
	public function get_delivery_address() {
		return $this->delivery_address;
	}

	/**
	 * Set delivery address.
	 *
	 * @param Address|null $delivery_address Delivery address.
	 * @return void
	 */
	public function set_delivery_address( Address $delivery_address = null ) {
		$this->delivery_address = $delivery_address;
	}

	/**
	 * Get line items.
	 *
	 * @return LineItems|null
	 */
	public function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Set line items.
	 *
	 * @param LineItems|null $line_items Line items.
	 * @return void
	 */
	public function set_line_items( $line_items ) {
		$this->line_items = $line_items;
	}

	/**
	 * Create and set new line items.
	 *
	 * @return LineItems
	 */
	public function new_line_items() {
		$this->line_items = new LineItems();

		return $this->line_items;
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
	 * Get reference.
	 *
	 * @return string
	 */
	public function get_reference() {
		return $this->reference;
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
	 * Get shopper IP.
	 *
	 * @return string|null
	 */
	public function get_shopper_ip() {
		return $this->shopper_ip;
	}

	/**
	 * Set shopper IP.
	 *
	 * @param string|null $shopper_ip Shopper IP.
	 * @return void
	 */
	public function set_shopper_ip( $shopper_ip ) {
		$this->shopper_ip = $shopper_ip;
	}

	/**
	 * Get shopper locale.
	 *
	 * @return string|null
	 */
	public function get_shopper_locale() {
		return $this->shopper_locale;
	}

	/**
	 * Set shopper locale.
	 *
	 * @param string|null $shopper_locale Shopper locale.
	 * @return void
	 */
	public function set_shopper_locale( $shopper_locale ) {
		$this->shopper_locale = $shopper_locale;
	}

	/**
	 * Get shopper name.
	 *
	 * @return Name|null
	 */
	public function get_shopper_name() {
		return $this->shopper_name;
	}

	/**
	 * Set shopper name.
	 *
	 * @param Name|null $shopper_name Shopper name.
	 * @return void
	 */
	public function set_shopper_name( Name $shopper_name = null ) {
		$this->shopper_name = $shopper_name;
	}

	/**
	 * Get shopper reference.
	 *
	 * @return string|null
	 */
	public function get_shopper_reference() {
		return $this->shopper_reference;
	}

	/**
	 * Set shopper reference.
	 *
	 * @param string|null $shopper_reference Shopper reference.
	 * @return void
	 */
	public function set_shopper_reference( $shopper_reference ) {
		$this->shopper_reference = $shopper_reference;
	}

	/**
	 * Get shopper statement.
	 *
	 * @return string|null
	 */
	public function get_shopper_statement() {
		return $this->shopper_statement;
	}

	/**
	 * Set shopper statement.
	 *
	 * @param string|null $shopper_statement Shopper statement.
	 * @return void
	 */
	public function set_shopper_statement( $shopper_statement ) {
		$this->shopper_statement = $shopper_statement;
	}

	/**
	 * Get telephone number.
	 *
	 * @return string|null
	 */
	public function get_telephone_number() {
		return $this->telephone_number;
	}

	/**
	 * Set shopper statement.
	 *
	 * @param string|null $telephone_number Telephone number.
	 * @return void
	 */
	public function set_telephone_number( $telephone_number ) {
		$this->telephone_number = $telephone_number;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'amount'           => $this->get_amount()->get_json(),
				'billingAddress'   => is_null( $this->billing_address ) ? null : $this->billing_address->get_json(),
				'channel'          => $this->channel,
				'countryCode'      => $this->country_code,
				'dateOfBirth'      => is_null( $this->date_of_birth ) ? null : $this->date_of_birth->format( 'Y-m-d' ),
				'deliveryAddress'  => is_null( $this->delivery_address ) ? null : $this->delivery_address->get_json(),
				'lineItems'        => is_null( $this->line_items ) ? null : $this->line_items->get_json(),
				'merchantAccount'  => $this->get_merchant_account(),
				'reference'        => $this->get_reference(),
				'returnUrl'        => $this->get_return_url(),
				'shopperIP'        => $this->shopper_ip,
				'shopperLocale'    => $this->shopper_locale,
				'shopperName'      => is_null( $this->shopper_name ) ? null : $this->shopper_name->get_json(),
				'shopperReference' => $this->shopper_reference,
				'shopperStatement' => $this->shopper_statement,
				'telephoneNumber'  => $this->telephone_number,
			)
		);

		$object = (object) $properties;

		return $object;
	}
}
