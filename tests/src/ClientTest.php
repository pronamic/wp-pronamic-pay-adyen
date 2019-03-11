<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

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
 * @version 1.0.0
 * @since   1.0.0
 */
class ClientTest extends WP_UnitTestCase {
	/**
	 * Mock HTTP responses.
	 *
	 * @var array
	 */
	private $mock_http_responses;

	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->mock_http_responses = array();

		// Mock HTTP response.
		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );
	}

	/**
	 * Mock HTTP response.
	 *
	 * @param string $url  URL.
	 * @param string $file File with HTTP response.
	 */
	public function mock_http_response( $url, $file ) {
		$this->mock_http_responses[ $url ] = $file;
	}

	/**
	 * Pre HTTP request
	 *
	 * @link https://github.com/WordPress/WordPress/blob/3.9.1/wp-includes/class-http.php#L150-L164
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r       HTTP request arguments.
	 * @param string               $url     The request URL.
	 * @return array
	 */
	public function pre_http_request( $preempt, $r, $url ) {
		if ( ! isset( $this->mock_http_responses[ $url ] ) ) {
			return $preempt;
		}

		$file = $this->mock_http_responses[ $url ];

		unset( $this->mock_http_responses[ $url ] );

		$response = file_get_contents( $file, true );

		$processed_response = WP_Http::processResponse( $response );

		$processed_headers = WP_Http::processHeaders( $processed_response['headers'], $url );

		$processed_headers['body'] = $processed_response['body'];

		return $processed_headers;
	}

	/**
	 * Test get payment methods exception.
	 */
	public function test_get_payment_methods_exception() {
		$config = new Config();

		$client = new Client( $config );

		$this->setExpectedException( 'Exception' );

		$payment_methods = $client->get_payment_methods();
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

		$this->setExpectedException( __NAMESPACE__ . '\Error' );

		$payment_methods = $client->get_payment_methods();
	}

	/**
	 * Test get payment methods service exception.
	 */
	public function test_get_payment_methods_service_exception() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v41-paymentMethods-forbidden-901.http' );

		$this->setExpectedException( __NAMESPACE__ . '\ServiceException' );

		$payment_methods = $client->get_payment_methods();
	}

	/**
	 * Test get payment methods.
	 */
	public function test_get_payment_methods() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/paymentMethods', __DIR__ . '/../http/checkout-test-adyen-com-v41-paymentMethods-ok.http' );

		$payment_methods_response = $client->get_payment_methods();

		$this->assertCount( 8, $payment_methods_response->get_payment_methods() );
	}

	/**
	 * Test create payment.
	 */
	public function test_create_payment() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/payments', __DIR__ . '/../http/checkout-test-adyen-com-v41-payments-ok.http' );

		$payment_method         = new PaymentMethod( PaymentMethodType::IDEAL );
		$payment_method->issuer = '1121';

		$payment_request = new PaymentRequest(
			new Amount( 'EUR', 1000 ),
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			$payment_method
		);

		$payment_response = $client->create_payment( $payment_request );

		$this->assertInstanceOf( __NAMESPACE__ . '\PaymentResponse', $payment_response );
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

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/paymentSession', __DIR__ . '/../http/checkout-test-adyen-com-v41-paymentSession-ok.http' );

		$amount = new Amount( 'EUR', 1000 );

		$payment_request = new PaymentSessionRequest(
			$amount,
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			'NL'
		);

		$payment_response = $client->create_payment_session( $payment_request );

		$this->assertInstanceOf( __NAMESPACE__ . '\PaymentSessionResponse', $payment_response );
		$this->assertStringStartsWith( 'eyJjaGVja291dHNob3BwZXJCYXNlVXJs', $payment_response->get_payment_session() );
	}

	/**
	 * Test get payment result.
	 */
	public function test_get_payment_result() {
		$config = new Config();

		$config->mode = Core_Gateway::MODE_TEST;

		$client = new Client( $config );

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/payments/result', __DIR__ . '/../http/checkout-test-adyen-com-v41-payments-result-ok.http' );

		$request = new PaymentResultRequest( 'payload' );

		$response = $client->get_payment_result( $request );

		$this->assertInstanceOf( __NAMESPACE__ . '\PaymentResultResponse', $response );
		$this->assertStringStartsWith( '8515520546807677', $response->get_psp_reference() );
		$this->assertStringStartsWith( ResultCode::AUTHORIZED, $response->get_result_code() );
		$this->assertStringStartsWith( '791', $response->get_merchant_reference() );
		$this->assertStringStartsWith( PaymentMethodType::IDEAL, $response->get_payment_method() );
		$this->assertStringStartsWith( 'nl_NL', $response->get_shopper_locale() );
	}
}
