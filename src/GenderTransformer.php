<?php
/**
 * Gender transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Gender as Pay_Gender;

/**
 * Gender transformer class
 */
class GenderTransformer {
	/**
	 * Transform WordPress Pay gender to Adyen gender.
	 *
	 * @param string|null $gender WordPress Pay gender to convert.
	 * @return string
	 */
	public static function transform( $gender ) {
		return match ( $gender ) {
			Pay_Gender::FEMALE => Gender::FEMALE,
			Pay_Gender::MALE => Gender::MALE,
			default => Gender::UNKNOWN,
		};
	}
}
