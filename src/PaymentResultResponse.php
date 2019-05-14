<?php
/**
 * Payment result response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment result response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments/result
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResultResponse extends ResponseObject {
	/**
	 * A unique value that you provided in the initial `/paymentSession` request as a `reference` field.
	 *
	 * @var string
	 */
	private $merchant_reference;

	/**
	 * The payment method used in the transaction.
	 *
	 * @var string
	 */
	private $payment_method;

	/**
	 * The shopperLocale value provided in the payment request
	 *
	 * @var string
	 */
	private $shopper_locale;

	/**
	 * The result of the payment.
	 *
	 * @var string|null
	 */
	private $result_code;

	/**
	 * Adyen's 16-character string reference associated with the transaction/request. This value is globally unique; quote it when communicating with us about this request.
	 *
	 * @var string|null
	 */
	private $psp_reference;

	/**
	 * Construct payment result response object.
	 *
	 * @param string $merchant_reference Merchant reference.
	 * @param string $payment_method     Payment method.
	 * @param string $shopper_locale     Shopper locale.
	 */
	public function __construct( $merchant_reference, $payment_method, $shopper_locale ) {
		$this->merchant_reference = $merchant_reference;
		$this->payment_method     = $payment_method;
		$this->shopper_locale     = $shopper_locale;
	}

	/**
	 * Get merchant reference.
	 *
	 * @return string
	 */
	public function get_merchant_reference() {
		return $this->merchant_reference;
	}

	/**
	 * Get payment method.
	 *
	 * @return string
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Get shopper locale.
	 *
	 * @return string
	 */
	public function get_shopper_locale() {
		return $this->shopper_locale;
	}

	/**
	 * Get result code.
	 *
	 * @return string|null
	 */
	public function get_result_code() {
		return $this->result_code;
	}

	/**
	 * Set result code.
	 *
	 * @param string|null $result_code Result code.
	 * @return void
	 */
	public function set_result_code( $result_code ) {
		$this->result_code = $result_code;
	}

	/**
	 * Get PSP reference.
	 *
	 * @return string|null
	 */
	public function get_psp_reference() {
		return $this->psp_reference;
	}

	/**
	 * Set PSP reference.
	 *
	 * @param string|null $psp_reference PSP reference.
	 * @return void
	 */
	public function set_psp_reference( $psp_reference ) {
		$this->psp_reference = $psp_reference;
	}

	/**
	 * Create payment result repsonse from object.
	 *
	 * @param object $object Object.
	 * @return PaymentResultResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-result.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$response = new self(
			$object->merchantReference,
			$object->paymentMethod,
			$object->shopperLocale
		);

		if ( isset( $object->pspReference ) ) {
			$response->set_psp_reference( $object->pspReference );
		}

		if ( isset( $object->resultCode ) ) {
			$response->set_result_code( $object->resultCode );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$response->set_original_object( $object );

		return $response;
	}
}
