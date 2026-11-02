<?php
/**
 * Gateway test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Http\Factory;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use WP_UnitTestCase;

/**
 * Gateway test
 */
class GatewayTest extends WP_UnitTestCase {
	/**
	 * Test gateway payment methods.
	 */
	public function test_get_payment_methods() {
		$factory = new Factory();

		$factory->fake( 'https://checkout-test.adyen.com/v71/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v64-paymentMethods-ok.http' );

		$config = new Config();

		$config->environment      = 'test';
		$config->api_key          = 'JPERWpuRAAvAj4mU';
		$config->merchant_account = 'Test';
		$config->client_key       = 'test_GLWQIBAUGNARTF574SAGD6HX6IDDHZSV';

		$gateway = new Gateway( $config );

		$payment_methods = $gateway->get_payment_methods();

		$this->assertCount( 24, $payment_methods );

		$this->assertEquals( 'active', $gateway->get_payment_method( PaymentMethods::IDEAL )->get_status() );
		$this->assertEquals( 'active', $gateway->get_payment_method( PaymentMethods::BANCONTACT )->get_status() );
		$this->assertEquals( 'inactive', $gateway->get_payment_method( PaymentMethods::SWISH )->get_status() );
	}
}
