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
	private $object;

	/**
	 * Construct object access.
	 *
	 * @param object $object Object.
	 */
	public function __construct( $object ) {
		$this->object = $object;
	}

	/**
	 * Has property.
	 *
	 * @param string $property Property.
	 * @return bool
	 */
	public function has_property( $property ) {
		return \property_exists( $this->object, $property );
	}

	/**
	 * Get property.
	 *
	 * @param string $property Property.
	 * @return mixed
	 */
	public function get_property( $property ) {
		if ( ! \property_exists( $this->object, $property ) ) {
			return null;
		}

		return $this->object->{$property};
	}
}
