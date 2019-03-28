<?php
/**
 * Payment result response test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment result response test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResultResponseTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test payment result response.
	 */
	public function test_payment_session_response() {
		$payment_result_response = new PaymentResultResponse( 'YOUR_MERCHANT_ACCOUNT', PaymentMethodType::IDEAL, 'nl_NL' );

		$this->assertEquals( 'YOUR_MERCHANT_ACCOUNT', $payment_result_response->get_merchant_reference() );
		$this->assertEquals( PaymentMethodType::IDEAL, $payment_result_response->get_payment_method() );
		$this->assertEquals( 'nl_NL', $payment_result_response->get_shopper_locale() );
	}
}
