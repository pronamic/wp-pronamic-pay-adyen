<?php
/**
 * Response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Response object class
 */
abstract class ResponseObject {
	/**
	 * Original response object.
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
	 * @param object|null $value Object.
	 * @return void
	 */
	public function set_original_object( $value ) {
		$this->original_object = $value;
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

		return (object) [];
	}
}
