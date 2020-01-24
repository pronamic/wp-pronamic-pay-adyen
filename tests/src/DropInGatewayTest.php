<?php
/**
 * Drop-in gateway test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Drop-in gateway test
 *
 * @author  Remco Tolsma
 * @version 1.1.0
 * @since   1.1.0
 */
class DropInGatewayTest extends TestCase {
	/**
	 * Test gateway.
	 */
	public function test_gateway() {
		$config = new Config();

		$config->mode                = Core_Gateway::MODE_TEST;
		$config->api_key             = 'JPERWpuRAAvAj4mU';
		$config->merchant_account    = 'Test';
		$config->api_live_url_prefix = '1797a841fbb37ca7-AdyenDemo';
		$config->origin_key          = 'pub.v2.8025893400271126.xIS0cDevM7BheK50ZXN8.Rm4amiQPrJ1owm3yHjmmVFG12Oa6RIIShvqrMvDIbwx';

		$gateway = new DropInGateway( $config );

		$this->assertEquals(
			array(
				PaymentMethods::ALIPAY,
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
