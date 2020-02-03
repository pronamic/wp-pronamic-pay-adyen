<?php
/**
 * Payments result controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_Http;
use WP_REST_Request;
use WP_UnitTestCase;

/**
 * Payments result controller test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentsResultControllerTest extends WP_UnitTestCase {
	/**
	 * Mock HTTP responses.
	 *
	 * @var array
	 */
	private $mock_http_responses;

	/**
	 * Setup.
	 *
	 * @link https://github.com/WordPress/phpunit-test-reporter/blob/master/tests/test-restapi.php
	 */
	public function setUp() {
		parent::setUp();

		$this->controller = new PaymentsResultController();
		$this->controller->setup();

		// Mock HTTP responses.
		$this->mock_http_responses = array();

		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

		// REST API init.
		do_action( 'rest_api_init' );
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
	 * Test filters.
	 *
	 * @link https://github.com/easydigitaldownloads/easy-digital-downloads/blob/2.9.11/tests/tests-filters.php
	 * @link https://github.com/woocommerce/woocommerce/blob/3.5.6/tests/unit-tests/settings/register-wp-admin-settings.php
	 * @link https://developer.wordpress.org/reference/functions/has_filter/
	 */
	public function test_filters() {
		$this->assertEquals( has_filter( 'rest_api_init', array( $this->controller, 'rest_api_init' ) ), 10 );
	}

	/**
	 * Test REST API initialize.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-includes/rest-api/class-wp-rest-server.php#L692-L776
	 */
	public function test_rest_api_init() {
		$routes = rest_get_server()->get_routes();

		$route = '/pronamic-pay/adyen/v1/payments/result/(?P<config_id>\d+)';

		$this->assertArrayHasKey( $route, $routes );
	}

	/**
	 * Test invalid gateway configuration (ID).
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_invalid_gateway_config_id() {
		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/payments/result/0' );

		$request->set_header( 'Content-Type', 'application/json' );

		$response = rest_do_request( $request );

		$this->assertEquals( 500, $response->get_status() );
	}

	/**
	 * Test no body.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_no_body() {
		$object = (object) array(
			'payload' => '123',
		);

		$post_id = self::factory()->post->create(
			array(
				'post_type'   => 'pronamic_gateway',
				'post_status' => 'publish',
				'post_title'  => 'Adyen - test',
				'meta_input'  => array(
					'_pronamic_gateway_id'            => 'adyen',
					'_pronamic_gateway_mode'          => 'test',
					'_pronamic_gateway_adyen_api_key' => 'JPERWpuRAAvAj4mU',
					'_pronamic_gateway_adyen_merchant_account' => 'Test',
				),
			)
		);

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/payments/result/' . $post_id );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( wp_json_encode( $object ) );

		$this->mock_http_response( 'https://checkout-test.adyen.com/v41/payments/result', __DIR__ . '/../http/checkout-test-adyen-com-v41-payments-result-500.http' );

		$this->expectException( Error::class );

		$response = rest_do_request( $request );

		$this->assertEquals( 500, $response->get_status() );
	}
}
