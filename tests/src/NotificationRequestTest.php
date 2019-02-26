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
	}
}
