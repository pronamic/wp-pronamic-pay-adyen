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
	 * @return string
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

		$payment_methods = $client->get_payment_methods();

		$this->assertCount( 8, $payment_methods );
	}
}
