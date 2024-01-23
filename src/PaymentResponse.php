<?php
/**
 * Payment response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment response class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 */
class PaymentResponse extends AbstractPaymentResponse {
	/**
	 * The action.
	 *
	 * @var PaymentResponseAction|null
	 */
	private $action;

	/**
	 * Get action.
	 *
	 * @return PaymentResponseAction|null
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Create payment response from object.
	 *
	 * @param object $value Object.
	 * @return self
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $value ) {
		$validator = new Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-response.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$data = new ObjectAccess( $value );

		$payment_response = new self();

		$payment_response->result_code    = $data->get_property( 'resultCode' );
		$payment_response->refusal_reason = $data->get_property( 'refusalReason' );
		$payment_response->psp_reference  = $data->get_property( 'pspReference' );

		if ( $data->has_property( 'action' ) ) {
			$payment_response->action = PaymentResponseAction::from_object( $data->get_property( 'action' ) );
		}

		$payment_response->set_original_object( $value );

		return $payment_response;
	}
}
