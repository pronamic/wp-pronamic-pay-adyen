<?php
/**
 * Payment method test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment method test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test amount.
	 */
	public function test_amount() {
		$payment_method = new PaymentMethod( PaymentMethodType::IDEAL );

		$this->assertEquals( PaymentMethodType::IDEAL, $payment_method->get_type() );

		$this->assertEquals(
			(object) array(
				'type' => PaymentMethodType::IDEAL,
			),
			$payment_method->get_json()
		);
	}
}
