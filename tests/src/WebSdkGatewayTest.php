<?php
/**
 * Web SDK gateway test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Web SDK gateway test
 *
 * @author  Remco Tolsma
 * @version 1.1.0
 * @since   1.0.0
 */
class WebSdkGatewayTest extends TestCase {
	/**
	 * Test gateway.
	 */
	public function test_gateway() {
		$config = new Config();

		$config->mode                = Core_Gateway::MODE_TEST;
		$config->api_key             = 'JPERWpuRAAvAj4mU';
		$config->merchant_account    = 'Test';
		$config->api_live_url_prefix = '1797a841fbb37ca7-AdyenDemo';

		$gateway = new WebSdkGateway( $config );

		$this->assertEquals(
			array(
				PaymentMethods::BANCONTACT,
				PaymentMethods::CREDIT_CARD,
				PaymentMethods::DIRECT_DEBIT,
				PaymentMethods::GIROPAY,
				PaymentMethods::IDEAL,
				PaymentMethods::MAESTRO,
				PaymentMethods::SOFORT,
			),
			$gateway->get_supported_payment_methods()
		);
	}
}
