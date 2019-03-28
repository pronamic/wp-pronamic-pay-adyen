<?php
/**
 * Payment method
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
 * Payment method
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethod extends ResponseObject {
	/**
	 * Type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Details.
	 *
	 * @var array|null
	 */
	private $details;

	/**
	 * Construct a payment method.
	 *
	 * @param string $type Adyen payment method type.
	 */
	public function __construct( $type ) {
		$this->type = $type;
	}

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get details.
	 *
	 * @return array|null
	 */
	public function get_details() {
		return $this->details;
	}

	/**
	 * Set details.
	 *
	 * @param array|null $details Details.
	 * @return void
	 */
	public function set_details( $details ) {
		$this->details = $details;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		return (object) array(
			'type' => $this->type,
		);
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

		$payment_method = new self( $object->type );

		if ( isset( $object->details ) ) {
			$payment_method->set_details( $object->details );
		}

		$payment_method->set_original_object( $object );

		return $payment_method;
	}
}
