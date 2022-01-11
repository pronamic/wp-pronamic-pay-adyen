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
	 * Details.
	 *
	 * @var array<int, object>|null
	 */
	private $details;

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
	 * Get details.
	 *
	 * @return array<int, object>|null
	 */
	public function get_details() {
		return $this->details;
	}

	/**
	 * Set details.
	 *
	 * @param array<int, object> $details Details.
	 * @return void
	 */
	public function set_details( $details ) {
		$this->details = $details;
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
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-method.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$payment_method = new self( $object );

		if ( isset( $object->details ) ) {
			$payment_method->set_details( $object->details );
		}

		$payment_method->set_original_object( $object );

		return $payment_method;
	}
}
