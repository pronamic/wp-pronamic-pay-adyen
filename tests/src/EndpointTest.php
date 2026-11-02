<?php
/**
 * Endpoint test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Endpoint test
 *
 * @link https://docs.adyen.com/development-resources/live-endpoints
 */
class EndpointTest extends TestCase {
	/**
	 * Test API URL for the test environment.
	 */
	public function test_api_url_test() {
		$endpoint = new Endpoint( 'test', null );

		$this->assertEquals( 'https://checkout-test.adyen.com/v41/paymentMethods', $endpoint->get_api_url( 'v41', 'paymentMethods' ) );
	}

	/**
	 * Test API URL for the live environment.
	 */
	public function test_api_url_live() {
		$endpoint = new Endpoint( 'live', '1797a841fbb37ca7-AdyenDemo' );

		$this->assertEquals( 'https://1797a841fbb37ca7-AdyenDemo-checkout-live.adyenpayments.com/checkout/v41/paymentMethods', $endpoint->get_api_url( 'v41', 'paymentMethods' ) );
	}

	/**
	 * Test API URL for the live environment without live URL prefix.
	 */
	public function test_api_url_live_without_prefix() {
		$endpoint = new Endpoint( 'live', null );

		$this->expectException( \Exception::class );

		$endpoint->get_api_url( 'v41', 'paymentMethods' );
	}

	/**
	 * Test web URL.
	 */
	public function test_web_url() {
		$endpoint = new Endpoint( 'test', null );

		$this->assertEquals( 'https://checkoutshopper-test.adyen.com/checkoutshopper/sdk/5.0.0/adyen.js', $endpoint->get_web_url( '5.0.0', 'adyen.js' ) );
	}
}
