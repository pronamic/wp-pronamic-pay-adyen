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
	 * Details.
	 *
	 * @var array<int, DetailsInformation>|null
	 */
	private $details;

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
	 * The action.
	 *
	 * @var ActionInformation|null
	 */
	private $action;

	/**
	 * Payment data.
	 *
	 * @var string|null
	 */
	private $payment_data;

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
	 * Get details.
	 *
	 * @return array<int, DetailsInformation>|null
	 */
	public function get_details() {
		return $this->details;
	}

	/**
	 * Set details.
	 *
	 * @param array<int, DetailsInformation>|null $details Details.
	 * @return void
	 */
	public function set_details( $details ) {
		$this->details = $details;
	}

	/**
	 * Get payment data.
	 *
	 * @return string|null
	 */
	public function get_payment_data() {
		return $this->payment_data;
	}

	/**
	 * Set payment data.
	 *
	 * @param string|null $payment_data Payment data.
	 * @return void
	 */
	public function set_payment_data( $payment_data ) {
		$this->payment_data = $payment_data;
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
	 * @return void
	 */
	public function set_redirect( RedirectInformation $redirect = null ) {
		$this->redirect = $redirect;
	}

	/**
	 * Get action.
	 *
	 * @return ActionInformation|null
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Set action.
	 *
	 * @param ActionInformation|null $action Action information.
	 * @return void
	 */
	public function set_action( ActionInformation $action = null ) {
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
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-response.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$payment_response = new self( $object->resultCode );

		if ( isset( $object->pspReference ) ) {
			$payment_response->set_psp_reference( $object->pspReference );
		}

		if ( isset( $object->action ) ) {
			$payment_response->set_action( ActionInformation::from_object( $object->action ) );
		}

		if ( isset( $object->details ) ) {
			$details = array();

			foreach ( $object->details as $detail ) {
				$details[] = DetailsInformation::from_object( $detail );
			}

			$payment_response->set_details( $details );
		}

		if ( isset( $object->refusalReason ) ) {
			$payment_response->set_refusal_reason( $object->refusalReason );
		}

		if ( isset( $object->redirect ) ) {
			$payment_response->set_redirect( RedirectInformation::from_object( $object->redirect ) );
		}

		if ( isset( $object->paymentData ) ) {
			$payment_response->set_payment_data( $object->paymentData );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$payment_response->set_original_object( $object );

		return $payment_response;
	}
}
