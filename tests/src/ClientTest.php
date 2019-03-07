<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;

/**
 * Client test
 *
 * @link https://docs.adyen.com/developers/development-resources/live-endpoints
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ClientTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test client.
	 */
	public function test_client() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$payment_methods = $client->get_payment_methods();
	}
}
