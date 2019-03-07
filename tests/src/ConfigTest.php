<?php
/**
 * Config test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;

/**
 * Config test
 *
 * @link https://docs.adyen.com/developers/development-resources/live-endpoints
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test config.
	 */
	public function test_config() {
		$config = new Config();

		$config->mode                = Core_Gateway::MODE_TEST;
		$config->api_key             = 'JPERWpuRAAvAj4mU';
		$config->merchant_account    = 'Test';
		$config->api_live_url_prefix = '1797a841fbb37ca7-AdyenDemo';

		$this->assertEquals( 'JPERWpuRAAvAj4mU', $config->get_api_key() );
		$this->assertEquals( 'Test', $config->get_merchant_account() );
		$this->assertEquals( 'https://checkout-test.adyen.com/v41/', $config->get_api_url() );

		$config->mode = Core_Gateway::MODE_LIVE;

		$this->assertEquals( 'https://1797a841fbb37ca7-AdyenDemo-checkout-live.adyenpayments.com/checkout/v41/', $config->get_api_url() );
	}
}
