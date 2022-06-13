<?php
/**
 * Util
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
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
	 * @param array<int|string, mixed> $array Array to filter null values from.
	 * @return array<int|string, mixed>
	 */
	public static function filter_null( $array ) {
		return array_filter( $array, [ __CLASS__, 'is_not_null' ] );
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

	/**
	 * Get reference for payment including network and blog IDs.
	 *
	 * @since 4.1.0
	 * @param string|int $payment_id Payment ID.
	 * @return string
	 * @throws \InvalidArgumentException Throws error on empty payment ID.
	 */
	public static function get_payment_reference( $payment_id ) : string {
		if ( empty( $payment_id ) ) {
			throw new \InvalidArgumentException( 'Payment ID cannot be empty for a unique reference to the payment.' );
		}

		return \sprintf(
			'%s-%s-%s',
			\get_current_network_id(),
			\get_current_blog_id(),
			$payment_id
		);
	}

	/**
	 * Get payment ID from merchant reference.
	 *
	 * @param string $reference Merchant reference.
	 * @return string|null
	 */
	public static function get_reference_payment_id( $reference ) : ?string {
		// Reference notation without network and blog IDs for backward compatibility.
		if ( ! \str_contains( $reference, '-' ) ) {
			return $reference;
		}

		list( $scan_network_id, $scan_blog_id, $scan_payment_id ) = \sscanf( $reference, '%d-%d-%s' );

		if ( \get_current_network_id() !== $scan_network_id ) {
			return null;
		}

		if ( \get_current_blog_id() !== $scan_blog_id ) {
			return null;
		}

		return $scan_payment_id;
	}
}
