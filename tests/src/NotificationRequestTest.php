<?php
/**
 * Notification request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Notification request test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationRequestTest extends \PHPUnit_Framework_TestCase {
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

		$this->assertEquals( '9313547924770610', $item->get_psp_reference() );
		$this->assertEquals( 'AUTHORISATION', $item->get_event_code() );
		$this->assertEquals( '2018-01-01T01:02:01+02:00', $item->get_event_date()->format( DATE_W3C ) );
		$this->assertEquals( 'TestMerchant', $item->get_merchant_account_code() );
		$this->assertEquals( array(
			'CANCEL',
			'CAPTURE',
			'REFUND',
		), $item->get_operations() );
		$this->assertEquals( 'YourMerchantReference1', $item->get_merchant_reference() );
		$this->assertEquals( 'visa', $item->get_payment_method() );
		$this->assertTrue( $item->is_success() );
	}
}
