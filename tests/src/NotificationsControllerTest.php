<?php
/**
 * Notifications controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\Payment;
use WP_REST_Request;
use WP_UnitTestCase;

/**
 * Notifications controller test
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationsControllerTest extends WP_UnitTestCase {
	/**
	 * Setup.
	 *
	 * @link https://github.com/WordPress/phpunit-test-reporter/blob/master/tests/test-restapi.php
	 */
	public function setUp() {
		parent::setUp();

		$this->rest_server = \rest_get_server();

		$this->controller = new NotificationsController();
		$this->controller->setup();

		// REST API init.
		do_action( 'rest_api_init' );
	}

	/**
	 * Test controller.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_controller() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 200, $response->get_status() );

		$this->assertEquals(
			(object) [
				'notificationResponse' => '[accepted]',
			],
			$response->get_data()
		);
	}

	/**
	 * Test controller authentication.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_controller_authentication() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		$username = 'username';
		$password = wp_generate_password();

		update_option( 'pronamic_pay_adyen_notification_authentication_username', $username );
		update_option( 'pronamic_pay_adyen_notification_authentication_password', $password );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		$request->set_header( 'Authorization', 'Basic ' . base64_encode( $username . ':' . $password ) );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 200, $response->get_status() );

		$this->assertEquals(
			(object) [
				'notificationResponse' => '[accepted]',
			],
			$response->get_data()
		);
	}

	/**
	 * Test controller unauthorized.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_controller_unauthorized() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		$username = 'username';

		update_option( 'pronamic_pay_adyen_notification_authentication_username', $username );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		$request->set_header( 'Authorization', 'Basic ' . base64_encode( $username ) );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( rest_authorization_required_code(), $response->get_status() );
	}

	/**
	 * Test valid notification.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_valid_notification() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		// Create payment.
		$payment = new Payment();
		$payment->save();

		$payment_id = $payment->get_id();

		$json = str_replace( 'YourMerchantReference1', $payment_id, $json );

		// REST request.
		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$payment = get_pronamic_payment( $payment_id );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test invalid notification request.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_invalid_notification_request() {
		$json = file_get_contents( __DIR__ . '/../json/invalid-notification-request.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 500, $response->get_status() );
	}

	/**
	 * Test invalid notification.
	 *
	 * @link https://torquemag.io/2017/01/testing-api-endpoints/
	 * @link https://github.com/WordPress/wordpress-develop/blob/5.1.0/tests/phpunit/tests/rest-api/rest-blocks-controller.php#L127-L136
	 */
	public function test_invalid_notification() {
		$json = file_get_contents( __DIR__ . '/../json/invalid-notification.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json );

		$response = rest_do_request( $request );

		$this->assertEquals( 500, $response->get_status() );
	}
}
