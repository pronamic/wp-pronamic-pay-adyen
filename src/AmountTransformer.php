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

use InvalidArgumentException;
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
	 * @return Amount
	 * @throws InvalidArgumentException Throws invalid argument exception when WordPress money object does not contain a currency with an alphabetic code.
	 */
	public static function transform( Money $money ) {
		$currency = $money->get_currency()->get_alphabetic_code();

		if ( null === $currency ) {
			throw new InvalidArgumentException( 'Can not transform WordPress money object to Adyen amount object due to empty currency code.' );
		}

		$amount = new Amount(
			$currency,
			$money->get_minor_units()
		);

		return $amount;
	}
}
