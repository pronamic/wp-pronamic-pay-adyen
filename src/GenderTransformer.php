<?php
/**
 * Gender transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Gender as Pay_Gender;

/**
 * Gender transformer
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class GenderTransformer {
	/**
	 * Transform WordPress Pay gender to Adyen gender.
	 *
	 * @param string|null $gender WordPress Pay gender to convert.
	 * @return string
	 */
	public static function transform( $gender ) {
		switch ( $gender ) {
			case Pay_Gender::FEMALE:
				return Gender::FEMALE;

			case Pay_Gender::MALE:
				return Gender::MALE;

			case Pay_Gender::OTHER:
			default:
				return Gender::UNKNOWN;
		}
	}
}
