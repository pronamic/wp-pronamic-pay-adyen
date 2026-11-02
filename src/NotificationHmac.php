<?php
/**
 * Notification HMAC
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Notification HMAC class
 *
 * @link https://docs.adyen.com/development-resources/webhooks/verify-hmac-signatures
 * @link https://github.com/Adyen/adyen-php-api-library/blob/main/src/Adyen/Util/HmacSignature.php
 */
class NotificationHmac {
	/**
	 * Get the data to sign for a notification request item.
	 *
	 * @param NotificationRequestItem $item Notification request item.
	 * @return string
	 */
	public static function get_signing_string( NotificationRequestItem $item ) {
		$amount = $item->get_amount();

		$data = [
			$item->get_psp_reference(),
			(string) $item->get_original_reference(),
			$item->get_merchant_account_code(),
			$item->get_merchant_reference(),
			(string) $amount->get_value(),
			$amount->get_currency(),
			$item->get_event_code(),
			$item->is_success() ? 'true' : 'false',
		];

		return \implode( ':', $data );
	}

	/**
	 * Calculate the HMAC signature.
	 *
	 * @param string $hex_key HMAC key in hexadecimal format, as shown in the Adyen Customer Area.
	 * @param string $data    Data to sign.
	 * @return string Base64 encoded signature, or an empty string for an invalid key.
	 */
	public static function calculate( $hex_key, $data ) {
		$hex_key = \trim( $hex_key );

		if ( '' === $hex_key || 0 !== \strlen( $hex_key ) % 2 || ! \ctype_xdigit( $hex_key ) ) {
			return '';
		}

		$key = \hex2bin( $hex_key );

		if ( false === $key ) {
			return '';
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Adyen HMAC signatures are Base64 encoded.
		return \base64_encode( \hash_hmac( 'sha256', $data, $key, true ) );
	}

	/**
	 * Verify the HMAC signature of a notification request item.
	 *
	 * @param NotificationRequestItem $item    Notification request item.
	 * @param string                  $hex_key HMAC key in hexadecimal format.
	 * @return bool
	 */
	public static function verify( NotificationRequestItem $item, $hex_key ) {
		$signature = $item->get_hmac_signature();

		if ( null === $signature || '' === $signature ) {
			return false;
		}

		$expected = self::calculate( $hex_key, self::get_signing_string( $item ) );

		if ( '' === $expected ) {
			return false;
		}

		return \hash_equals( $expected, $signature );
	}
}
