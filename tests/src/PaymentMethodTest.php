<?php
/**
 * Payment method test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Payment method test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodTest extends TestCase {
	/**
	 * Test payment method.
	 */
	public function test_payment_method() {
		$payment_method = new PaymentMethod(
			(object) array(
				'type' => PaymentMethodType::IDEAL,
			)
		);

		$this->assertEquals( PaymentMethodType::IDEAL, $payment_method->get_type() );

		$this->assertEquals(
			(object) array(
				'type' => PaymentMethodType::IDEAL,
			),
			$payment_method->get_json()
		);
	}

	/**
	 * Test payment method.
	 */
	public function test_payment_method_details() {
		$payment_method = new PaymentMethod(
			(object) array(
				'type' => PaymentMethodType::IDEAL,
			)
		);

		$payment_method->set_details( array() );

		$this->assertEquals( PaymentMethodType::IDEAL, $payment_method->get_type() );
		$this->assertEquals( array(), $payment_method->get_details() );
	}
}
