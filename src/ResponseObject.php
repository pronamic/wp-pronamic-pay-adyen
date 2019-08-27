<?php
/**
 * Response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;

/**
 * Response object
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
abstract class ResponseObject {
	/**
	 * Originale response object.
	 *
	 * @var object|null
	 */
	private $original_object;

	/**
	 * Get original object.
	 *
	 * @return object|null
	 */
	public function get_original_object() {
		return $this->original_object;
	}

	/**
	 * Set original object.
	 *
	 * @param object|null $object Object.
	 * @return void
	 */
	public function set_original_object( $object ) {
		$this->original_object = $object;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		if ( null !== $this->original_object ) {
			return $this->original_object;
		}

		return (object) array();
	}
}
