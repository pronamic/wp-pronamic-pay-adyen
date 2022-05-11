<?php
/**
 * Payment methods response
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
 * Payment methods response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.0
 */
class PaymentMethodsResponse extends ResponseObject {
	/**
	 * Detailed list of payment methods required to generate payment forms.
	 *
	 * @var PaymentMethod[]
	 */
	private $payment_methods;

	/**
	 * Construct payment methods response object.
	 *
	 * @param PaymentMethod[] $payment_methods Payment methods.
	 */
	public function __construct( $payment_methods ) {
		$this->payment_methods = $payment_methods;
	}

	/**
	 * Get payment methods.
	 *
	 * @return PaymentMethod[]
	 */
	public function get_payment_methods() {
		return $this->payment_methods;
	}

	/**
	 * Get payment method types.
	 *
	 * @return array<int, string>
	 */
	public function get_payment_method_types() {
		$types = [];

		// Loop payment methods.
		$payment_methods = $this->payment_methods;

		foreach ( $payment_methods as $payment_method ) {
			$type = $payment_method->get_type();

			if ( null !== $type ) {
				$types[] = $type;
			}
		}

		return $types;
	}

	/**
	 * Create payment methods response from object.
	 *
	 * @param object $object Object.
	 * @return PaymentMethodsResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-methods-response.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$payment_methods = [];

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		foreach ( $object->paymentMethods as $payment_method_object ) {
			$payment_methods[] = PaymentMethod::from_object( $payment_method_object );
		}

		$response = new self( $payment_methods );

		$response->set_original_object( $object );

		return $response;
	}
}
