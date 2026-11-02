<?php
/**
 * Config test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Config test
 */
class ConfigTest extends TestCase {
	/**
	 * Test config.
	 */
	public function test_config() {
		$config = new Config();

		$config->environment              = 'test';
		$config->api_key                  = 'JPERWpuRAAvAj4mU';
		$config->client_key               = 'test_ABC123';
		$config->merchant_account         = 'Test';
		$config->merchant_order_reference = 'Order {order_id}';
		$config->country_code             = 'NL';

		$this->assertEquals( 'JPERWpuRAAvAj4mU', $config->get_api_key() );
		$this->assertEquals( 'Test', $config->get_merchant_account() );
		$this->assertEquals( 'Order {order_id}', $config->get_merchant_order_reference() );

		$this->assertEquals(
			(object) [
				'@type'            => Config::class,
				'environment'      => 'test',
				'merchant_account' => 'Test',
				'api_key'          => 'JPERWpuRAAvAj4mU',
				'client_key'       => 'test_ABC123',
				'country_code'     => 'NL',
			],
			$config->jsonSerialize()
		);
	}
}
