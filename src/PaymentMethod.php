<?php
/**
 * Payment method
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
 * Payment method
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.0
 */
class PaymentMethod extends ResponseObject {
	/**
	 * Type.
	 *
	 * @var string|null
	 */
	private $type;

	/**
	 * A list of issuers for this payment method.
	 *
	 * @var PaymentMethodIssuer[]|null
	 */
	private $issuers;

	/**
	 * Construct a payment method.
	 *
	 * @param object $payment_method_object Original object.
	 */
	public function __construct( $payment_method_object ) {
		// Set type.
		if ( isset( $payment_method_object->type ) ) {
			$this->type = $payment_method_object->type;
		}

		$this->set_original_object( $payment_method_object );
	}

	/**
	 * Get type.
	 *
	 * @return string|null
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get issuers.
	 *
	 * @return PaymentMethodIssuer[]|null
	 */
	public function get_issuers() {
		return $this->issuers;
	}

	/**
	 * Create payment method from object.
	 *
	 * @param object $object Object.
	 * @return PaymentMethod
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-method.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$payment_method = new self( $object );

		if ( \property_exists( $object, 'issuers' ) ) {
			$payment_method->issuers = [];

			foreach ( $object->issuers as $issuer_object ) {
				$payment_method->issuers[] = PaymentMethodIssuer::from_object( $issuer_object );
			}
		}

		$payment_method->set_original_object( $object );

		return $payment_method;
	}
}
