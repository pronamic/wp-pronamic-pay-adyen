<?php
/**
 * Object access
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Object access class
 */
class ObjectAccess {
	/**
	 * Object.
	 *
	 * @var object
	 */
	private $value;

	/**
	 * Construct object access.
	 *
	 * @param object $value Object.
	 */
	public function __construct( $value ) {
		$this->value = $value;
	}

	/**
	 * Has property.
	 *
	 * @param string $property Property.
	 * @return bool
	 */
	public function has_property( $property ) {
		return \property_exists( $this->value, $property );
	}

	/**
	 * Get property.
	 *
	 * @param string $property Property.
	 * @return mixed
	 */
	public function get_property( $property ) {
		if ( ! \property_exists( $this->value, $property ) ) {
			return null;
		}

		return $this->value->{$property};
	}
}
