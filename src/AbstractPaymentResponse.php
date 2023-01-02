<?php
/**
 * Abstract payment response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Abstract payment response class
 *
 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments__section_resParams
 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments/details__section_resParams
 */
abstract class AbstractPaymentResponse extends ResponseObject {
	/**
	 * Refusal reason.
	 *
	 * If the payment's authorisation is refused or an error occurs during authorisation,
	 * this field holds Adyen's mapped reason for the refusal or a description of the
	 * error.
	 *
	 * @var string|null
	 */
	protected $refusal_reason;

	/**
	 * The result of the payment.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments__resParam_resultCode
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments/details__resParam_resultCode
	 * @var string|null
	 */
	protected $result_code;

	/**
	 * Adyen's 16-character string reference associated with the transaction/request.
	 * This value is globally unique; quote it when communicating with us about this
	 * request.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments__resParam_pspReference
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v68/post/payments/details__resParam_pspReference
	 * @var string|null
	 */
	protected $psp_reference;

	/**
	 * Get refusal reason.
	 *
	 * @return string|null
	 */
	public function get_refusal_reason() {
		return $this->refusal_reason;
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
	 * Get PSP reference.
	 *
	 * @return string|null
	 */
	public function get_psp_reference() {
		return $this->psp_reference;
	}
}
