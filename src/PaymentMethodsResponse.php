<?php
/**
 * Payment methods response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment methods response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodsResponse extends ResponseObject {
	/**
	 * Groups of payment methods.
	 *
	 * @var array
	 */
	private $groups;

	/**
	 * Detailed list of one-click payment methods.
	 *
	 * @var array|null
	 */
	private $one_click_payment_methods;

	/**
	 * Detailed list of payment methods required to generate payment forms.
	 *
	 * @var array
	 */
	private $payment_methods;

	/**
	 * Construct payment session response object.
	 *
	 * @param array $groups          Groups.
	 * @param array $payment_methods Payment methods.
	 */
	public function __construct( $groups, $payment_methods ) {
		$this->groups          = $groups;
		$this->payment_methods = $payment_methods;
	}

	/**
	 * Get payment methods.
	 *
	 * @return array
	 */
	public function get_payment_methods() {
		return $this->payment_methods;
	}

	/**
	 * Set one-click payment methods.
	 *
	 * @param array|null $one_click_payment_methods One-click payment methods.
	 * @return void
	 */
	public function set_one_click_payment_methods( $one_click_payment_methods ) {
		$this->one_click_payment_methods = $one_click_payment_methods;
	}

	/**
	 * Create payment methods repsonse from object.
	 *
	 * @param object $object Object.
	 * @return PaymentMethodsResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-methods-response.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$response = new self( $object->groups, $object->paymentMethods );

		if ( isset( $object->oneClickPaymentMethods ) ) {
			$response->set_one_click_payment_methods( $object->oneClickPaymentMethods );
		}

		$response->set_original_object( $object );

		return $response;
	}
}
