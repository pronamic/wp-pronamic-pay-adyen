<?php
/**
 * Request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Request class
 */
abstract class Request {
	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	abstract public function get_json();
}
