<?php
/**
 * Payment response test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Payment response test
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResponseTest extends TestCase {
	/**
	 * Test payment response.
	 */
	public function test_payment_response() {
		$payment_response = PaymentResponse::from_object(
			(object) [
				'resultCode'   => ResultCode::PENDING,
				'pspReference' => '8515520546807677',
			]
		);

		$this->assertEquals( ResultCode::PENDING, $payment_response->get_result_code() );
		$this->assertEquals( '8515520546807677', $payment_response->get_psp_reference() );
		$this->assertNull( $payment_response->get_refusal_reason() );
		$this->assertNull( $payment_response->get_action() );
	}

	/**
	 * Test from object.
	 *
	 * @link https://docs.adyen.com/payment-methods/ideal/api-only#make-a-payment
	 */
	public function test_from_object() {
		$json = file_get_contents( __DIR__ . '/../json/payment-response-ideal.json', true );

		$data = json_decode( $json );

		$payment_response = PaymentResponse::from_object( $data );

		$this->assertEquals( ResultCode::REDIRECT_SHOPPER, $payment_response->get_result_code() );

		$action = $payment_response->get_action();

		$this->assertInstanceOf( PaymentResponseAction::class, $action );
		$this->assertEquals( 'redirect', $action->get_type() );
		$this->assertEquals( 'https://checkoutshopper-test.adyen.com/checkoutshopper/checkoutPaymentRedirect?redirectData=X6XtfGC3%21Y3', $action->get_url() );
	}

	/**
	 * Test JSON optional.
	 */
	public function test_from_object_optional() {
		$object = (object) [
			'resultCode'   => ResultCode::PENDING,
			'pspReference' => '1234567890123456',
			'redirect'     => (object) [
				'method' => 'GET',
				'url'    => 'https://test.adyen.com/hpp/redirectIdeal.shtml',
			],
		];

		$payment_response = PaymentResponse::from_object( $object );

		$this->assertEquals( '1234567890123456', $payment_response->get_psp_reference() );
	}
}
