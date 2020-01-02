<?php
/**
 * Payment request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Payment request test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentRequestTest extends TestCase {
	/**
	 * Test payment request.
	 */
	public function test_payment_request() {
		$json_file = __DIR__ . '/../json/payment-request.json';

		$payment_method = new PaymentMethodIDeal( PaymentMethodType::IDEAL, '1121' );

		$payment_request = new PaymentRequest(
			new Amount( 'EUR', 1000 ),
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			$payment_method
		);

		$json_string = wp_json_encode( $payment_request->get_json(), JSON_PRETTY_PRINT );

		$this->assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}
}
