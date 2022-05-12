<?php
/**
 * Payment method issuer
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
 * Payment method issuer class
 */
class PaymentMethodIssuer {
	/**
	 * The unique identifier of this issuer, to submit in requests to /payments.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * A localized name of the issuer.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * A boolean value indicating whether this issuer is unavailable.
	 * Can be true whenever the issuer is offline.
	 *
	 * @var bool|null
	 */
	private $disabled;

	/**
	 * Construct a payment method issuer.
	 *
	 * @param string $id   ID.
	 * @param string $name Name.
	 */
	public function __construct( $id, $name ) {
		$this->id   = $id;
		$this->name = $name;
	}

	/**
	 * Get ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Is disabled?
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return ( true === $this->disabled );
	}

	/**
	 * Create payment method from object.
	 *
	 * @param object $object Object.
	 * @return self
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-method-issuer.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$payment_method_issuer = new self( $object->id, $object->name );

		if ( isset( $object->disabled ) ) {
			$payment_method_issuer->disabled = $object->disabled;
		}

		return $payment_method_issuer;
	}
}
