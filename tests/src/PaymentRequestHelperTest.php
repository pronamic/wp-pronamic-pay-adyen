<?php
/**
 * Payment request helper test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Customer;

/**
 * Payment request helper test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentRequestHelperTest extends TestCase {
	/**
	 * Test complement.
	 */
	public function test_complement() {
		/*
		 * Payment.
		 */
		$payment              = new Payment();
		$payment->description = 'Test';

		$customer = new Customer();
		$customer->set_ip_address( '127.0.0.1' );
		$customer->set_locale( 'nl_NL' );
		$customer->set_phone( '085 40 11 580' );

		$payment->set_customer( $customer );

		/**
		 * Payment request.
		 */
		$payment_method = array (
			'type' => PaymentMethodType::IDEAL,
			'issuer' => '1121'
		);

		$payment_request = new PaymentRequest(
			new Amount( 'EUR', 1000 ),
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			new PaymentMethod( (object) $payment_method )
		);

		/*
		 * Complement.
		 */
		PaymentRequestHelper::complement( $payment, $payment_request );

		/*
		 * Asserts.
		 */
		$this->assertEquals( Channel::WEB, $payment_request->get_channel() );
		$this->assertEquals( 'Test', $payment_request->get_shopper_statement() );
		$this->assertEquals( '127.0.0.1', $payment_request->get_shopper_ip() );
		$this->assertEquals( 'nl_NL', $payment_request->get_shopper_locale() );
		$this->assertEquals( '085 40 11 580', $payment_request->get_telephone_number() );
	}
}
