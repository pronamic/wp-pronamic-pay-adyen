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
		$payment_response = new PaymentResponse( ResultCode::PENDING );

		$payment_response->set_psp_reference( '8515520546807677' );

		$this->assertEquals( ResultCode::PENDING, $payment_response->get_result_code() );
		$this->assertEquals( '8515520546807677', $payment_response->get_psp_reference() );
	}

	/**
	 * Test from object.
	 */
	public function test_from_object() {
		$json = file_get_contents( __DIR__ . '/../json/payment-response-ideal.json', true );

		$data = json_decode( $json );

		$payment_response = PaymentResponse::from_object( $data );

		$this->assertEquals( ResultCode::REDIRECT_SHOPPER, $payment_response->get_result_code() );

		$this->assertEquals( 'GET', $payment_response->get_redirect()->get_method() );
		$this->assertEquals( 'https://test.adyen.com/hpp/redirectIdeal.shtml?brandCode=ideal&currencyCode=EUR&issuerId=1121&merchantAccount=APIDocsMerchant1&merchantReference=Your+order+number&merchantReturnData=8515517245928960&merchantSig=71bXHiJnCAPdsTN%2BZaeSQ1%2B%2BXzDAMvpZVzZ%2B64QgYzk%3D&paymentAmount=1000&resURL=https%3A%2F%2Fcheckoutshopper-test.adyen.com%2Fcheckoutshopper%2Fservices%2FPaymentIncomingRedirect%2Fv1%2FlocalPaymentMethod%3FmerchantAccount%3DAPIDocsMerchant1%26returnURL%3Dhttps%253A%252F%252Fyour-company.com%252F...&sessionValidity=2019-03-04T19%3A36%3A32Z&skinCode=pub.v2.7814286629520534.UAZe-M3BLHmzRXxgqAENPg3jw3Rmxh8oz_e8KIf_OJY', $payment_response->get_redirect()->get_url() );
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
