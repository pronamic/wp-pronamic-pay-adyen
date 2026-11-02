<?php
/**
 * Notification HMAC test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_UnitTestCase;

/**
 * Notification HMAC test
 *
 * @link https://github.com/Adyen/adyen-php-api-library/blob/main/tests/Unit/Util/HmacSignatureTest.php
 */
class NotificationHmacTest extends WP_UnitTestCase {
	/**
	 * Get notification request item from Adyen's test vector.
	 *
	 * @param string $merchant_reference Merchant reference.
	 * @param string $hmac_signature     HMAC signature.
	 * @return NotificationRequestItem
	 */
	private function get_item( $merchant_reference = 'TestPayment-1407325143704', $hmac_signature = '' ) {
		$data = (object) [
			'additionalData'      => (object) [
				'hmacSignature' => $hmac_signature,
			],
			'pspReference'        => '7914073381342284',
			'merchantAccountCode' => 'TestMerchant',
			'merchantReference'   => $merchant_reference,
			'amount'              => (object) [
				'value'    => 1130,
				'currency' => 'EUR',
			],
			'eventCode'           => 'AUTHORISATION',
			'eventDate'           => '2018-01-01T01:02:01.111+02:00',
			'success'             => 'true',
		];

		return NotificationRequestItem::from_object( $data );
	}

	/**
	 * Test signing string.
	 */
	public function test_signing_string() {
		$this->assertSame(
			'7914073381342284::TestMerchant:TestPayment-1407325143704:1130:EUR:AUTHORISATION:true',
			NotificationHmac::get_signing_string( $this->get_item() )
		);
	}

	/**
	 * Test calculate with Adyen's test vector.
	 */
	public function test_calculate() {
		$key = 'DFB1EB5485895CFA84146406857104ABB4CBCABDC8AAF103A624C8F6A3EAAB00';

		$this->assertSame(
			'Ny9gS2veKo5E4w8/OL6xz1/wvT/hYkAXy1xNc/QvO4I=',
			NotificationHmac::calculate( $key, NotificationHmac::get_signing_string( $this->get_item() ) )
		);

		$this->assertSame(
			'kLZtgnXU86m/yjnpBD4aqESFgnoDcy1fGOP1Db/L3+4=',
			NotificationHmac::calculate( $key, NotificationHmac::get_signing_string( $this->get_item( ' TestPayment-1407325143704' ) ) )
		);
	}

	/**
	 * Test invalid key.
	 */
	public function test_invalid_key() {
		$this->assertSame( '', NotificationHmac::calculate( '', 'data' ) );
		$this->assertSame( '', NotificationHmac::calculate( 'not-hex', 'data' ) );
		$this->assertSame( '', NotificationHmac::calculate( 'ABC', 'data' ) );
	}

	/**
	 * Test verify.
	 */
	public function test_verify() {
		$key = 'DFB1EB5485895CFA84146406857104ABB4CBCABDC8AAF103A624C8F6A3EAAB00';

		$this->assertTrue( NotificationHmac::verify( $this->get_item( 'TestPayment-1407325143704', 'Ny9gS2veKo5E4w8/OL6xz1/wvT/hYkAXy1xNc/QvO4I=' ), $key ) );
		$this->assertFalse( NotificationHmac::verify( $this->get_item( 'TestPayment-1407325143704', 'kLZtgnXU86m/yjnpBD4aqESFgnoDcy1fGOP1Db/L3+4=' ), $key ) );
		$this->assertFalse( NotificationHmac::verify( $this->get_item(), $key ) );
		$this->assertFalse( NotificationHmac::verify( $this->get_item( 'TestPayment-1407325143704', 'Ny9gS2veKo5E4w8/OL6xz1/wvT/hYkAXy1xNc/QvO4I=' ), '' ) );
	}
}
