<?php
/**
 * Amount transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Money\Money;

/**
 * Amount transformer
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class AmountTransformer {
	/**
	 * Transform Pronamic money to Adyen amount.
	 *
	 * @param Money $money Pronamic money to convert.
	 *
	 * @return Amount
	 */
	public static function transform( Money $money ) {
		$amount = new Amount(
			strval( $money->get_currency()->get_alphabetic_code() ),
			intval( $money->get_minor_units() )
		);

		return $amount;
	}
}
