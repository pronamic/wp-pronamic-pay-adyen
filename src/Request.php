<?php
/**
 * Request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Request
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
abstract class Request {
	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	abstract public function get_json();
}
