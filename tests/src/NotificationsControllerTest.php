<?php
/**
 * Notifications controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_REST_Request;

/**
 * Notifications controller test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationsControllerTest extends \WP_UnitTestCase {
	/**
	 * Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->controller = new NotificationsController();
		$this->controller->setup();
	}

	/**
	 * Test controller.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_controller() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/v1/adyen/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 200, $response->get_status() );

		$this->assertEquals(
			(object) array(
				'notificationResponse' => '[accepted]',
			),
			$response->get_data()
		);
	}

	/**
	 * Test invalid notification.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_invalid_notification() {
		$json = file_get_contents( __DIR__ . '/../json/invalid-notification.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/v1/adyen/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 500, $response->get_status() );
	}
}
