<?php
/**
 * Payment methods response test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Payment methods response test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodsResponseTest extends TestCase {
	/**
	 * Test payment methods response.
	 */
	public function test_response() {
		$json = file_get_contents( __DIR__ . '/../json/payment-methods-response.json', true );

		$data = json_decode( $json );

		$payment_methods_response = PaymentMethodsResponse::from_object( $data );

		$payment_methods = $payment_methods_response->get_payment_methods();

		$this->assertCount( 19, $payment_methods );
	}
}
