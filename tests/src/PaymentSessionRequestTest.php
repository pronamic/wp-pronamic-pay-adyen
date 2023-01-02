<?php
/**
 * Payment session request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Payment session request test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentSessionRequestTest extends TestCase {
	/**
	 * Test payment request.
	 */
	public function test_payment_request() {
		$amount = new Amount( 'EUR', 1000 );

		$payment_request = new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL'
		);

		$payment_request->set_billing_address( new Address( 'NL' ) );
		$payment_request->set_channel( Channel::WEB );
		$payment_request->set_date_of_birth( new DateTime( '05-05-2005' ) );
		$payment_request->set_delivery_address( new Address( 'US' ) );
		$payment_request->set_line_items( null );
		$payment_request->set_shopper_ip( '127.0.0.1' );
		$payment_request->set_shopper_locale( 'nl_NL' );
		$payment_request->set_shopper_name( null );
		$payment_request->set_shopper_reference( '123' );
		$payment_request->set_shopper_statement( 'The text to appear on the shopper\'s bank statement.' );
		$payment_request->set_telephone_number( '085 40 11 580' );

		$payment_request->set_allowed_payment_methods(
			[
				PaymentMethodType::DIRECT_EBANKING,
				PaymentMethodType::IDEAL,
			]
		);

		$payment_request->set_origin( 'https://www.pronamic.eu/' );
		$payment_request->set_sdk_version( '1.9.4' );

		$this->assertEquals( $amount, $payment_request->get_amount() );
		$this->assertEquals( 'YOUR_MERCHANT_ACCOUNT', $payment_request->get_merchant_account() );
		$this->assertEquals( 'Your order number', $payment_request->get_reference() );
		$this->assertEquals( 'https://your-company.com/...', $payment_request->get_return_url() );
		$this->assertEquals( 'NL', $payment_request->get_country_code() );
		$this->assertEquals(
			[
				PaymentMethodType::DIRECT_EBANKING,
				PaymentMethodType::IDEAL,
			],
			$payment_request->get_allowed_payment_methods()
		);
		$this->assertEquals( 'https://www.pronamic.eu/', $payment_request->get_origin() );
		$this->assertEquals( '1.9.4', $payment_request->get_sdk_version() );
		$this->assertEquals( 'NL', $payment_request->get_billing_address()->get_country() );
		$this->assertEquals( Channel::WEB, $payment_request->get_channel() );
		$this->assertEquals( '05-05-2005', $payment_request->get_date_of_birth()->format( 'd-m-Y' ) );
		$this->assertEquals( 'US', $payment_request->get_delivery_address()->get_country() );
		$this->assertNull( $payment_request->get_line_items() );
		$this->assertEquals( '127.0.0.1', $payment_request->get_shopper_ip() );
		$this->assertEquals( 'nl_NL', $payment_request->get_shopper_locale() );
		$this->assertNull( $payment_request->get_shopper_name() );
		$this->assertEquals( '123', $payment_request->get_shopper_reference() );
		$this->assertEquals( 'The text to appear on the shopper\'s bank statement.', $payment_request->get_shopper_statement() );
		$this->assertEquals( '085 40 11 580', $payment_request->get_telephone_number() );

		$line_items = $payment_request->new_line_items();

		$this->assertInstanceOf( __NAMESPACE__ . '\LineItems', $line_items );
	}

	/**
	 * Test invalid country.
	 */
	public function test_invalid_country() {
		$amount = new Amount( 'EUR', 1000 );

		$this->expectException( 'InvalidArgumentException' );

		new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL invalid'
		);
	}

	/**
	 * Test JSON.
	 */
	public function test_json() {
		$json_file = __DIR__ . '/../json/payment-session-request.json';

		$amount = new Amount( 'EUR', 1000 );

		$payment_request = new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL'
		);

		$this->assertEquals( $amount, $payment_request->get_amount() );
		$this->assertEquals( 'YOUR_MERCHANT_ACCOUNT', $payment_request->get_merchant_account() );
		$this->assertEquals( 'Your order number', $payment_request->get_reference() );
		$this->assertEquals( 'https://your-company.com/...', $payment_request->get_return_url() );
		$this->assertEquals( 'NL', $payment_request->get_country_code() );

		$json_string = wp_json_encode( $payment_request->get_json(), JSON_PRETTY_PRINT );

		$this->assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}

	/**
	 * Test JSON optional.
	 */
	public function test_json_optional() {
		$amount = new Amount( 'EUR', 1000 );

		$payment_request = new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL'
		);

		$payment_request->set_allowed_payment_methods( [ PaymentMethodType::ALIPAY ] );
		$payment_request->set_sdk_version( '1.9.4' );
		$payment_request->set_origin( 'https://www.pronamic.eu/' );

		$object = $payment_request->get_json();

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		$this->assertEquals( [ 'alipay' ], $object->allowedPaymentMethods );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		$this->assertEquals( '1.9.4', $object->sdkVersion );
		$this->assertEquals( 'https://www.pronamic.eu/', $object->origin );
	}
}
