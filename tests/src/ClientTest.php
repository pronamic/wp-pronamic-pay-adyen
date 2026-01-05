<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Http\Facades\Http;
use Pronamic\WordPress\Http\Factory;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use WP_Error;
use WP_Http;
use WP_UnitTestCase;

/**
 * Client test
 *
 * @link https://docs.adyen.com/developers/development-resources/live-endpoints
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.0
 */
class ClientTest extends WP_UnitTestCase {
	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->factory = new Factory();
	}

	/**
	 * Test get payment methods exception.
	 */
	public function test_get_payment_methods_exception() {
		$config = new Config();

		$client = new Client( $config );

		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Adyen API Live URL prefix is required for live configurations.' );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}

	/**
	 * Test get payment methods unauthorized.
	 */
	public function test_get_payment_methods_unauthorized() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v51/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v51-paymentMethods-unauthorized.http' );

		$this->expectException( ServiceException::class );
		$this->expectExceptionMessage( 'HTTP Status Response - Unauthorized' );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}

	/**
	 * Test get payment methods error.
	 */
	public function test_get_payment_methods_error() {
		$config = new Config();

		$config->mode             = Core_Gateway::MODE_TEST;
		$config->api_key          = 'JPERWpuRAAvAj4mU';
		$config->merchant_account = 'Test';

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v51/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v51-paymentMethods-unauthorized.http' );

		$this->expectException( ServiceException::class );
		$this->expectExceptionMessage( 'HTTP Status Response - Unauthorized' );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}

	/**
	 * Test get payment methods service exception.
	 */
	public function test_get_payment_methods_service_exception() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v64/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v64-paymentMethods-forbidden-901.http' );

		$this->expectException( ServiceException::class );
		$this->expectExceptionMessage( 'Invalid Merchant Account' );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}

	/**
	 * Test get payment methods.
	 */
	public function test_get_payment_methods() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v64/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v64-paymentMethods-ok.http' );

		$payment_methods_response = $client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );

		$this->assertCount( 10, $payment_methods_response->get_payment_methods() );
	}

	/**
	 * Test create payment.
	 */
	public function test_create_payment() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v64/payments', __DIR__ . '/../http/checkout-test-adyen-com-v64-payments-ok.http' );

		$payment_method = [
			'type'   => PaymentMethodType::IDEAL,
			'issuer' => '1121',
		];

		$payment_request = new PaymentRequest(
			new Amount( 'EUR', 1000 ),
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			new PaymentMethod( (object) $payment_method )
		);

		$payment_response = $client->create_payment( $payment_request );

		$this->assertInstanceOf( PaymentResponse::class, $payment_response );
		$this->assertEquals( ResultCode::REDIRECT_SHOPPER, $payment_response->get_result_code() );
		$this->assertEquals( 'GET', $payment_response->get_redirect()->get_method() );
	}

	/**
	 * Test create payment session.
	 */
	public function test_create_payment_session() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v41/paymentSession', __DIR__ . '/../http/checkout-test-adyen-com-v41-paymentSession-ok.http' );

		$amount = new Amount( 'EUR', 1000 );

		$payment_request = new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL'
		);

		$payment_response = $client->create_payment_session( $payment_request );

		$this->assertInstanceOf( PaymentSessionResponse::class, $payment_response );
		$this->assertStringStartsWith( 'eyJjaGVja291dHNob3BwZXJCYXNlVXJs', $payment_response->get_payment_session() );
	}

	/**
	 * Test JSON invalid.
	 */
	public function test_json_invalid() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v41/paymentMethods', __DIR__ . '/../http/json-invalid.http' );

		$this->expectException( \Exception::class );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}

	/**
	 * Test JSON array.
	 */
	public function test_json_array() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->factory->fake( 'https://checkout-test.adyen.com/v41/paymentMethods', __DIR__ . '/../http/json-array.http' );

		$this->expectException( \Exception::class );

		$client->get_payment_methods( new PaymentMethodsRequest( 'YOUR_MERCHANT_ACCOUNT' ) );
	}
}
