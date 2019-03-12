<?php
/**
 * Util
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Util
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Util {
	/**
	 * Filter null.
	 *
	 * @param array $array Array to filter null values from.
	 * @return array
	 */
	public static function filter_null( $array ) {
		return array_filter( $array, array( __CLASS__, 'is_not_null' ) );
	}

	/**
	 * Check if value is not null.
	 *
	 * @param mixed $value Value.
	 * @return boolean True if value is not null, false otherwise.
	 */
	private static function is_not_null( $value ) {
		return ( null !== $value );
	}
}
