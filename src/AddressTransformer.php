<?php
/**
 * Address transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Address as Pay_Address;

/**
 * Address transformer
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AddressTransformer {
	/**
	 * Transform WordPress Pay address to Adyen address.
	 *
	 * @param Pay_Address $address WordPress Pay address to convert.
	 * @return Address
	 */
	public static function transform( Pay_Address $address ) {
		$address = new Address(
			$address->get_country_code(),
			$address->get_street_name(),
			$address->get_house_number(),
			$address->get_postal_code(),
			$address->get_city(),
			$address->get_region()
		);

		return $address;
	}
}
