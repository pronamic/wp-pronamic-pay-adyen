<?php
/**
 * Payment session response test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Payment session response test
 *
 * @link https://docs.adyen.com/api-explorer/Checkout/71/post/sessions
 */
class PaymentSessionResponseTest extends TestCase {
	/**
	 * Test payment session response.
	 */
	public function test_payment_session_response() {
		$payment_session_response = new PaymentSessionResponse( 'CSD9CAC34EBAE225DD', 'Ab02b4c0!BQABAgBfYI29...' );

		$this->assertEquals( 'CSD9CAC34EBAE225DD', $payment_session_response->get_id() );
		$this->assertEquals( 'Ab02b4c0!BQABAgBfYI29...', $payment_session_response->get_data() );
	}

	/**
	 * Test from object.
	 */
	public function test_from_object() {
		$json = \file_get_contents( __DIR__ . '/../json/payment-session-response.json', true );

		$data = \json_decode( $json );

		$payment_session_response = PaymentSessionResponse::from_object( $data );

		$this->assertEquals( 'CSD9CAC34EBAE225DD', $payment_session_response->get_id() );
		$this->assertEquals( 'Ab02b4c0!BQABAgBfYI29...', $payment_session_response->get_data() );
	}

	/**
	 * Test from object with missing required properties.
	 */
	public function test_from_object_invalid() {
		$this->expectException( ValidationException::class );

		PaymentSessionResponse::from_object( (object) [ 'id' => 'CSD9CAC34EBAE225DD' ] );
	}
}
