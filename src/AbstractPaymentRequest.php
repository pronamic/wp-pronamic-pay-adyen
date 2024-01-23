<?php
/**
 * Abstract payment request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use DateTime;
use InvalidArgumentException;

/**
 * Abstract payment request class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 */
abstract class AbstractPaymentRequest extends Request {
	/**
	 * Additional data.
	 *
	 * @var AdditionalData|null
	 */
	private $additional_data;

	/**
	 * Amount.
	 *
	 * @var Amount
	 */
	private $amount;

	/**
	 * Information about your application.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo
	 * @var ApplicationInfo|null
	 */
	private $application_info;

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
	 * This reference allows linking multiple transactions to each other for reporting
	 * purposes (i.e. order auth-rate). The reference should be unique per billing cycle.
	 * The same merchant order reference should never be reused after the first authorised
	 * attempt. If used, this field should be supplied for all incoming authorisations.
	 *
	 * @var string|null
	 */
	private $merchant_order_reference;

	/**
	 * Metadata consists of entries, each of which includes a key and a value. Limitations: Maximum 20 key-value
	 * pairs per request. When exceeding, the "177" error occurs: "Metadata size exceeds limit".
	 *
	 * @var array<string,int|string>|null
	 */
	private $metadata;

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
	 * The shopper's email address. We recommend that you provide this data, as it is used in velocity fraud checks.
	 *
	 * @var string|null
	 */
	private $shopper_email;

	/**
	 * The shopper's reference to uniquely identify this shopper (e.g. user ID or account ID). This field is
	 * required for recurring payments
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_shopperReference
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
	 * Get additional data.
	 *
	 * @return AdditionalData|null
	 */
	public function get_additional_data() {
		return $this->additional_data;
	}

	/**
	 * Set additional data.
	 *
	 * @param AdditionalData|null $additional_data Additional data.
	 * @return void
	 */
	public function set_additional_data( $additional_data ) {
		$this->additional_data = $additional_data;
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
	 * Get application info.
	 *
	 * @return ApplicationInfo|null
	 */
	public function get_application_info() {
		return $this->application_info;
	}

	/**
	 * Set application info.
	 *
	 * @param ApplicationInfo|null $application_info Application info.
	 * @return void
	 */
	public function set_application_info( $application_info ) {
		$this->application_info = $application_info;
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
					\esc_html( $country_code )
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
	 * Get merchant order reference.
	 *
	 * @return mixed
	 */
	public function get_merchant_order_reference() {
		return $this->merchant_order_reference;
	}

	/**
	 * Set merchant order reference.
	 *
	 * @param mixed $merchant_order_reference Merchant order reference.
	 * @return void
	 */
	public function set_merchant_order_reference( $merchant_order_reference ) {
		$this->merchant_order_reference = $merchant_order_reference;
	}

	/**
	 * Get metadata.
	 *
	 * @return array<string,int|string>|null
	 */
	public function get_metadata() {
		return $this->metadata;
	}

	/**
	 * Set metadata.
	 *
	 * @param array<string,int|string>|null $metadata Metadata.
	 * @return void
	 */
	public function set_metadata( $metadata ) {
		$this->metadata = $metadata;
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
	 * Get shopper email.
	 *
	 * @return string|null
	 */
	public function get_shopper_email() {
		return $this->shopper_email;
	}

	/**
	 * Set shopper email.
	 *
	 * @param string|null $shopper_email Shopper email.
	 *
	 * @return void
	 */
	public function set_shopper_email( $shopper_email = null ) {
		$this->shopper_email = $shopper_email;
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
		$metadata = $this->get_metadata();

		$properties = Util::filter_null(
			[
				'additionalData'         => is_null( $this->additional_data ) ? null : $this->additional_data->get_json(),
				'amount'                 => $this->get_amount()->get_json(),
				'applicationInfo'        => $this->application_info,
				'billingAddress'         => is_null( $this->billing_address ) ? null : $this->billing_address->get_json(),
				'channel'                => $this->channel,
				'countryCode'            => $this->country_code,
				'dateOfBirth'            => is_null( $this->date_of_birth ) ? null : $this->date_of_birth->format( 'Y-m-d' ),
				'deliveryAddress'        => is_null( $this->delivery_address ) ? null : $this->delivery_address->get_json(),
				'lineItems'              => is_null( $this->line_items ) ? null : $this->line_items->get_json(),
				'merchantAccount'        => $this->get_merchant_account(),
				'merchantOrderReference' => $this->get_merchant_order_reference(),
				'metadata'               => empty( $metadata ) ? null : (object) $metadata,
				'reference'              => $this->get_reference(),
				'returnUrl'              => $this->get_return_url(),
				'shopperIP'              => $this->shopper_ip,
				'shopperLocale'          => $this->shopper_locale,
				'shopperName'            => is_null( $this->shopper_name ) ? null : $this->shopper_name->get_json(),
				'shopperEmail'           => $this->shopper_email,
				'shopperReference'       => $this->shopper_reference,
				'shopperStatement'       => $this->shopper_statement,
				'telephoneNumber'        => $this->telephone_number,
			]
		);

		$object = (object) $properties;

		return $object;
	}
}
