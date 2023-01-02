<?php
/**
 * Amount transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;
use Pronamic\WordPress\Money\Money;

/**
 * Amount transformer class
 */
class AmountTransformer {
	/**
	 * Transform Pronamic money to Adyen amount.
	 *
	 * @param Money $money Pronamic money to convert.
	 * @return Amount
	 * @throws InvalidArgumentException Throws invalid argument exception when WordPress money object does not contain a currency with an alphabetic code.
	 */
	public static function transform( Money $money ) {
		$amount = new Amount(
			$money->get_currency()->get_alphabetic_code(),
			$money->get_minor_units()->to_int()
		);

		return $amount;
	}
}
