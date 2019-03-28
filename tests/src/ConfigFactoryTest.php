<?php
/**
 * Config factory test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_UnitTestCase;

/**
 * Config factory test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ConfigFactoryTest extends WP_UnitTestCase {
	/**
	 * Test config factory.
	 *
	 * @link https://make.wordpress.org/core/handbook/testing/automated-testing/writing-phpunit-tests/#fixtures-and-factories
	 * @link https://pippinsplugins.com/unit-tests-for-wordpress-plugins-the-factory/
	 * @link https://core.trac.wordpress.org/browser/trunk/tests/phpunit/includes/factory/class-wp-unittest-factory-for-post.php
	 * @link https://github.com/woocommerce/woocommerce/search?q=%22factory-%3Epost%22&unscoped_q=%22factory-%3Epost%22
	 */
	public function test_config_factory() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'  => 'pronamic_gateway',
				'post_title' => 'Adyen - test',
				'meta_input' => array(
					'_pronamic_gateway_id'            => 'adyen',
					'_pronamic_gateway_mode'          => 'test',
					'_pronamic_gateway_adyen_api_key' => 'JPERWpuRAAvAj4mU',
					'_pronamic_gateway_adyen_merchant_account' => 'Test',
				),
			)
		);

		$factory = new ConfigFactory();

		$config = $factory->get_config( $post_id );

		$this->assertEquals( 'JPERWpuRAAvAj4mU', $config->get_api_key() );
		$this->assertEquals( 'Test', $config->get_merchant_account() );
		$this->assertEquals( 'https://checkout-test.adyen.com/v41/paymentSession', $config->get_api_url( 'paymentSession' ) );
	}
}
