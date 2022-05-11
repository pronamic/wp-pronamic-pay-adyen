<?php
/**
 * Payment response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResponse extends ResponseObject {
	/**
	 * Adyen's 16-character string reference associated with the transaction/request. This value is globally unique; quote it when communicating with us about this request.
	 *
	 * `pspReference` is returned only for non-redirect payment methods.
	 *
	 * @var string|null
	 */
	private $psp_reference;

	/**
	 * The action.
	 *
	 * @var PaymentResponseAction|null
	 */
	private $action;

	/**
	 * Refusal reason.
	 *
	 * @var string|null
	 */
	private $refusal_reason;

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
	 * Get result code.
	 *
	 * @return string
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
	 * Get refusal reason.
	 *
	 * @return string|null
	 */
	public function get_refusal_reason() {
		return $this->refusal_reason;
	}

	/**
	 * Set refusal reason.
	 *
	 * @param string|null $refusal_reason Refusal reason.
	 * @return void
	 */
	public function set_refusal_reason( $refusal_reason ) {
		$this->refusal_reason = $refusal_reason;
	}

	/**
	 * Get action.
	 *
	 * @return PaymentResponseAction|null
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Set action.
	 *
	 * @param PaymentResponseAction|null $action Action information.
	 * @return void
	 */
	public function set_action( PaymentResponseAction $action = null ) {
		$this->action = $action;
	}

	/**
	 * Create payment response from object.
	 *
	 * @param object $object Object.
	 * @return PaymentResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-response.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$payment_response = new self( $object->resultCode );

		if ( isset( $object->pspReference ) ) {
			$payment_response->set_psp_reference( $object->pspReference );
		}

		if ( isset( $object->action ) ) {
			$payment_response->set_action( PaymentResponseAction::from_object( $object->action ) );
		}

		if ( isset( $object->refusalReason ) ) {
			$payment_response->set_refusal_reason( $object->refusalReason );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$payment_response->set_original_object( $object );

		return $payment_response;
	}
}
