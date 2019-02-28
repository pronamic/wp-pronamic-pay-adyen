<?php
/**
 * Payment response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResponse {
	/**
	 * This field contains additional data, which may be required to return in a particular payment response.
	 *
	 * @var object|null
	 */
	private $additional_data;

	/**
	 * When non-empty, contains all the fields that you must submit to the `/payments/details` endpoint.
	 *
	 * @var array|null
	 */
	private $details;

	/**
	 * The fraud result properties of the payment
	 *
	 * @var object|null
	 */
	private $fraud_result;

	/**
	 * Contains the details that will be presented to the shopper
	 *
	 * @var object|null
	 */
	private $output_details;

	/**
	 * When non-empty, contains a value that you must submit to the `/payments/details` endpoint.
	 *
	 * @var string|null
	 */
	private $payment_data;

	/**
	 * Adyen's 16-character string reference associated with the transaction/request. This value is globally unique; quote it when communicating with us about this request.
	 *
	 * `pspReference` is returned only for non-redirect payment methods.
	 *
	 * @var string|null
	 */
	private $psp_reference;

	/**
	 * When the payment flow requires a redirect, this object contains information about the redirect URL.
	 *
	 * @var RedirectInformation|null
	 */
	private $redirect;

	/**
	 * If the payment's authorisation is refused or an error occurs during authorisation, this field holds Adyen's mapped reason for the refusal or a description of the error.
	 *
	 * When a transaction fails, the authorisation response includes `resultCode` and `refusalReason` values.
	 *
	 * @var string|null
	 */
	private $refusal_reason;

	/**
	 * Code that specifies the refusal reason.
	 *
	 * @var string|null
	 */
	private $refusal_reason_code;

	/**
	 * The result of the payment. 
	 *
	 * @var string
	 */
	private $result_code;

	/**
	 * Construct payment response object.
	 *
	 * @param string $result_code Result code.
	 */
	public function __construct( $result_code ) {
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
	 */
	public function set_psp_reference( $psp_reference ) {
		$this->psp_reference = $psp_reference;
	}

	/**
	 * Get redirect.
	 *
	 * @return RedirectInformation|null
	 */
	public function get_redirect() {
		return $this->redirect;
	}

	/**
	 * Set redirect.
	 *
	 * @param RedirectInformation|null $redirect Redirect information.
	 */
	public function set_redirect( RedirectInformation $redirect = null ) {
		$this->redirect = $redirect;
	}

	/**
	 * Create payment repsonse from object.
	 *
	 * @param object $object Object.
	 * @return PaymentResponse
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->resultCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `resultCode` property.' );
		}

		$payment_response = new self( $object->resultCode );

		if ( isset( $object->pspReference ) ) {
			$payment_response->set_psp_reference( $object->pspReference );
		}

		if ( isset( $object->redirect ) ) {
			$payment_response->set_redirect( RedirectInformation::from_object( $object->redirect ) );
		}

		return $payment_response;
	}
}
