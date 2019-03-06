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

use InvalidArgumentException;
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
	 * @return Address|null
	 */
	public static function transform( Pay_Address $address ) {
		$country = $address->get_country();

		if ( null === $country ) {
			return null;
		}

		$country_code = $country->get_code();

		if ( null === $country_code ) {
			return null;
		}

		$state_or_province = null;

		$region = $address->get_region();

		if ( null !== $region ) {
			$state_or_province = $region->get_code();
		}

		try {
			$address = new Address(
				$country_code,
				$address->get_street_name(),
				strval( $address->get_house_number() ),
				$address->get_postal_code(),
				$address->get_city(),
				$state_or_province
			);
		} catch ( InvalidArgumentException $exception ) {
			return null;
		}

		return $address;
	}
}
