<?php
/**
 * Util
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Util class
 */
class Util {
	/**
	 * Filter null.
	 *
	 * @param array<int|string, mixed> $value Array to filter null values from.
	 * @return array<int|string, mixed>
	 */
	public static function filter_null( $value ) {
		return array_filter( $value, [ __CLASS__, 'is_not_null' ] );
	}

	/**
	 * Check if value is not null.
	 *
	 * @param mixed $value Value.
	 * @return boolean True if value is not null, false otherwise.
	 */
	private static function is_not_null( $value ) {
		return ( null !== $value );
	}

	/**
	 * Get payment locale.
	 *
	 * @param Payment $payment Payment.
	 * @return string
	 */
	public static function get_payment_locale( Payment $payment ) {
		$locale = get_locale();

		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			$locale = $customer->get_locale();
		}

		return (string) $locale;
	}

	/**
	 * Get country code.
	 *
	 * @since 2.0.1
	 * @param Payment $payment Payment.
	 * @return string|null
	 */
	public static function get_country_code( Payment $payment ) {
		$country_code = null;

		// Billing Address.
		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$country = $billing_address->get_country_code();

			if ( null !== $country ) {
				$country_code = $country;
			}
		}

		return $country_code;
	}
}
