<?php
/**
 * Notification request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Notification request test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationRequestTest extends TestCase {
	/**
	 * Test notification request.
	 */
	public function test_notification_request() {
		$json = file_get_contents( __DIR__ . '/../json/notification.json', true );

		$data = json_decode( $json );

		$notification_request = NotificationRequest::from_object( $data );

		$this->assertFalse( $notification_request->is_live() );

		$items = $notification_request->get_items();

		$this->assertCount( 1, $items );

		$item = array_pop( $items );

		$this->assertEquals( 'EUR', $item->get_amount()->get_currency() );
		$this->assertEquals( 500, $item->get_amount()->get_value() );
		$this->assertEquals( '9313547924770610', $item->get_psp_reference() );
		$this->assertEquals( 'AUTHORISATION', $item->get_event_code() );
		$this->assertEquals( '2018-01-01T01:02:01+02:00', $item->get_event_date()->format( DATE_W3C ) );
		$this->assertEquals( 'TestMerchant', $item->get_merchant_account_code() );
		$this->assertEquals(
			array(
				'CANCEL',
				'CAPTURE',
				'REFUND',
			),
			$item->get_operations()
		);
		$this->assertEquals( 'YourMerchantReference1', $item->get_merchant_reference() );
		$this->assertEquals( 'visa', $item->get_payment_method() );
		$this->assertTrue( $item->is_success() );
	}

	/**
	 * Test notification tests.
	 *
	 * @link https://ca-test.adyen.com/ca/ca/config/configurethirdparty.shtml
	 *
	 * @dataProvider provider_notification_tests
	 *
	 * @param string $file JSON file to test.
	 */
	public function test_notification_tests( $file ) {
		$json = file_get_contents( __DIR__ . '/../json/' . $file, true );

		$data = json_decode( $json );

		$notification_request = NotificationRequest::from_object( $data );

		$this->assertInstanceOf( __NAMESPACE__ . '\NotificationRequest', $notification_request );
	}

	/**
	 * Provider notification tests.
	 *
	 * @return array
	 */
	public function provider_notification_tests() {
		return array(
			array(
				'notification-test-1.json',
			),
			array(
				'notification-test-2.json',
			),
			array(
				'notification-test-3.json',
			),
			array(
				'notification-test-4.json',
			),
		);
	}

	/**
	 * Test from object.
	 */
	public function test_from_object() {
		$object = (object) array(
			'live'              => 'false',
			'notificationItems' => array(
				(object) array(
					'NotificationRequestItem' => (object) array(
						'additionalData'      => (object) array(
							'authCode'    => '58747',
							'cardSummary' => '1111',
							'expiryDate'  => '8/2018',
						),
						'amount'              => (object) array(
							'value'    => 500,
							'currency' => 'EUR',
						),
						'pspReference'        => '9313547924770610',
						'eventCode'           => 'AUTHORISATION',
						'eventDate'           => '2018-01-01T01:02:01.111+02:00',
						'merchantAccountCode' => 'TestMerchant',
						'operations'          => array(
							'CANCEL',
							'CAPTURE',
							'REFUND',
						),
						'merchantReference'   => 'YourMerchantReference1',
						'paymentMethod'       => 'visa',
						'reason'              => '58747:1111:12/2012',
						'success'             => 'true',
					),
				),
			),
		);

		$notification_request = NotificationRequest::from_object( $object );

		$this->assertFalse( $notification_request->is_live() );
	}

	/**
	 * Test invalid object.
	 */
	public function test_invalid_object() {
		$object = (object) array(
			'live'              => 'false',
			'notificationItems' => array(
				(object) array(
					'invalid' => (object) array(),
				),
			),
		);

		$this->setExpectedException( 'JsonSchema\Exception\ValidationException' );

		$notification_request = NotificationRequest::from_object( $object );
	}
}
