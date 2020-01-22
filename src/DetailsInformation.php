<?php
/**
 * Details information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Details information
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class DetailsInformation extends ResponseObject {
	/**
	 * The value to provide in the result.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The type of the required input.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Construct action information.
	 */
	public function __construct() {
	}

	/**
	 * Get key.
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Set key.
	 *
	 * @param string $key Key.
	 */
	public function set_key( $key ) {
		$this->key = $key;
	}

	/**
	 * Get type.
	 *
	 * @return mixed
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set type.
	 *
	 * @param mixed $type Type.
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Create details information from object.
	 *
	 * @param object $object Object.
	 * @return DetailsInformation
	 * @throws \JsonSchema\Exception\ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/details.json' ),
			),
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		$details = new self();

		if ( isset( $object->key ) ) {
			$details->set_key( $object->key );
		}

		if ( isset( $object->type ) ) {
			$details->set_type( $object->type );
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
			array(
				'key' => $this->get_key(),
				'type'    => $this->get_type(),
			)
		);

		$object = (object) $properties;

		return $object;
	}
}
