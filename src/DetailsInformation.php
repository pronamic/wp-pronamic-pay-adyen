<?php
/**
 * Details information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Details information class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 */
class DetailsInformation extends ResponseObject {
	/**
	 * The value to provide in the result.
	 *
	 * @var string|null
	 */
	private $key;

	/**
	 * The type of the required input.
	 *
	 * @var string|null
	 */
	private $type;

	/**
	 * Get key.
	 *
	 * @return string|null
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Set key.
	 *
	 * @param string|null $key Key.
	 * @return void
	 */
	public function set_key( $key ) {
		$this->key = $key;
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
	 * Set type.
	 *
	 * @param string|null $type Type.
	 * @return void
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Create details information from object.
	 *
	 * @param object $value Object.
	 * @return DetailsInformation
	 * @throws \JsonSchema\Exception\ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $value ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/details.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		$details = new self();

		if ( isset( $value->key ) ) {
			$details->set_key( $value->key );
		}

		if ( isset( $value->type ) ) {
			$details->set_type( $value->type );
		}

		return $details;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			[
				'key'  => $this->get_key(),
				'type' => $this->get_type(),
			]
		);

		$value = (object) $properties;

		return $value;
	}
}
