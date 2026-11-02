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

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Gateways\GatewaysDataStoreCPT;
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
	public function set_up(): void {
		parent::set_up();

		// Reset the gateways cache, post IDs can be reused between tests.
		\pronamic_pay_plugin()->gateways_data_store = new GatewaysDataStoreCPT();

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
		$password = wp_generate_password( 24, false );

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
	 * HMAC key used in tests.
	 *
	 * @var string
	 */
	private $hmac_key = 'DFB1EB5485895CFA84146406857104ABB4CBCABDC8AAF103A624C8F6A3EAAB00';

	/**
	 * Create Adyen configuration.
	 *
	 * @param string $hmac_key HMAC key.
	 * @return int
	 */
	private function create_config( $hmac_key ) {
		$config_id = self::factory()->post->create(
			[
				'post_type'   => 'pronamic_gateway',
				'post_status' => 'publish',
			]
		);

		\update_post_meta( $config_id, '_pronamic_gateway_id', 'adyen-test' );
		\update_post_meta( $config_id, '_pronamic_gateway_adyen_merchant_account', 'TestMerchant' );
		\update_post_meta( $config_id, '_pronamic_gateway_adyen_hmac_key', $hmac_key );

		return $config_id;
	}

	/**
	 * Create payment.
	 *
	 * @param int|null $config_id Configuration ID.
	 * @return Payment
	 */
	private function create_payment( $config_id ) {
		$payment = new Payment();

		if ( null !== $config_id ) {
			$payment->set_config_id( $config_id );
		}

		$payment->set_total_amount( new Money( '5.00', 'EUR' ) );

		$payment->save();

		return $payment;
	}

	/**
	 * Get notification JSON.
	 *
	 * @param Payment              $payment  Payment.
	 * @param string|null          $hmac_key HMAC key to sign with, `null` for no signature.
	 * @param array<string, mixed> $changes  Notification request item changes.
	 * @return string
	 */
	private function get_notification_json( Payment $payment, $hmac_key, $changes = [] ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$data = json_decode( file_get_contents( __DIR__ . '/../json/notification.json', true ) );

		$item_data = $data->notificationItems[0]->NotificationRequestItem;

		$item_data->merchantReference = (string) $payment->get_id();

		foreach ( $changes as $key => $value ) {
			$item_data->{$key} = $value;
		}

		if ( null !== $hmac_key ) {
			$item = NotificationRequestItem::from_object( $item_data );

			$item_data->additionalData->hmacSignature = NotificationHmac::calculate( $hmac_key, NotificationHmac::get_signing_string( $item ) );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		return wp_json_encode( $data );
	}

	/**
	 * Post notification.
	 *
	 * @param string      $json          JSON.
	 * @param string|null $authorization Authorization header.
	 * @return \WP_REST_Response
	 */
	private function post_notification( $json, $authorization = null ) {
		$request = new WP_REST_Request( 'POST', '/pronamic-pay/adyen/v1/notifications' );

		$request->set_header( 'Content-Type', 'application/json' );

		if ( null !== $authorization ) {
			$request->set_header( 'Authorization', $authorization );
		}

		$request->set_body( $json );

		return rest_do_request( $request );
	}

	/**
	 * Configure HTTP Basic authentication.
	 *
	 * @return string Authorization header.
	 */
	private function configure_basic_authentication() {
		$username = 'username';
		$password = wp_generate_password( 24, false );

		update_option( 'pronamic_pay_adyen_notification_authentication_username', $username );
		update_option( 'pronamic_pay_adyen_notification_authentication_password', $password );

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		return 'Basic ' . base64_encode( $username . ':' . $password );
	}

	/**
	 * Test valid notification with HMAC signature.
	 */
	public function test_valid_notification() {
		$payment = $this->create_payment( $this->create_config( $this->hmac_key ) );

		$response = $this->post_notification( $this->get_notification_json( $payment, $this->hmac_key ) );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( PaymentStatus::SUCCESS, $payment->get_status() );
		$this->assertEquals( '9313547924770610', $payment->get_transaction_id() );
	}

	/**
	 * Test notification without any authentication configured.
	 */
	public function test_notification_without_authentication() {
		$payment = $this->create_payment( $this->create_config( '' ) );

		$response = $this->post_notification( $this->get_notification_json( $payment, null ) );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( rest_authorization_required_code(), $response->get_status() );
		$this->assertNotEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test notification with invalid HMAC signature.
	 */
	public function test_invalid_hmac_signature() {
		$payment = $this->create_payment( $this->create_config( $this->hmac_key ) );

		$response = $this->post_notification( $this->get_notification_json( $payment, '44782DEF547AAA06C910C43932B1EB0C71FC68D9D0C057550C48EC2ACF6BA056' ) );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( rest_authorization_required_code(), $response->get_status() );
		$this->assertNotEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test notification with HMAC key configured and valid HTTP Basic authentication, but without HMAC signature.
	 */
	public function test_missing_hmac_signature_with_basic_authentication() {
		$authorization = $this->configure_basic_authentication();

		$payment = $this->create_payment( $this->create_config( $this->hmac_key ) );

		$response = $this->post_notification( $this->get_notification_json( $payment, null ), $authorization );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( rest_authorization_required_code(), $response->get_status() );
		$this->assertNotEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test notification with HTTP Basic authentication and without HMAC key.
	 */
	public function test_basic_authentication_without_hmac_key() {
		$authorization = $this->configure_basic_authentication();

		$payment = $this->create_payment( $this->create_config( '' ) );

		$response = $this->post_notification( $this->get_notification_json( $payment, null ), $authorization );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test notification for a payment without Adyen configuration.
	 */
	public function test_payment_without_adyen_configuration() {
		$authorization = $this->configure_basic_authentication();

		$payment = $this->create_payment( null );

		$response = $this->post_notification( $this->get_notification_json( $payment, null ), $authorization );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertNotEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Test notifications that do not match the payment.
	 *
	 * @dataProvider provider_mismatch
	 * @param array<string, mixed> $changes Notification request item changes.
	 */
	public function test_mismatch( $changes ) {
		$payment = $this->create_payment( $this->create_config( $this->hmac_key ) );

		$payment->set_transaction_id( '9313547924770610' );
		$payment->save();

		$response = $this->post_notification( $this->get_notification_json( $payment, $this->hmac_key, $changes ) );

		$payment = get_pronamic_payment( $payment->get_id() );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertNotEquals( PaymentStatus::SUCCESS, $payment->get_status() );
	}

	/**
	 * Mismatch provider.
	 *
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	public static function provider_mismatch() {
		return [
			'amount'           => [
				[
					'amount' => (object) [
						'value'    => 1,
						'currency' => 'EUR',
					],
				],
			],
			'currency'         => [
				[
					'amount' => (object) [
						'value'    => 500,
						'currency' => 'USD',
					],
				],
			],
			'merchant account' => [ [ 'merchantAccountCode' => 'OtherMerchant' ] ],
			'psp reference'    => [ [ 'pspReference' => '1234567890123456' ] ],
		];
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
