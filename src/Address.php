<?php
/**
 * Address
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;

/**
 * Address
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/address
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Address {
	/**
	 * City.
	 *
	 * @var string|null
	 */
	private $city;

	/**
	 * Country.
	 *
	 * @var string
	 */
	private $country;

	/**
	 * House number or name.
	 *
	 * @var string|null
	 */
	private $house_number_or_name;

	/**
	 * Postal code.
	 *
	 * @var string|null
	 */
	private $postal_code;

	/**
	 * State or province.
	 *
	 * @var string|null
	 */
	private $state_or_province;

	/**
	 * Street.
	 *
	 * @var string|null
	 */
	private $street;

	/**
	 * Construct address.
	 *
	 * @param string $country              Country.
	 * @param string $street               Street.
	 * @param string $house_number_or_name House number or name.
	 * @param string $postal_code          Postal code.
	 * @param string $city                 City.
	 * @param string $state_or_province    State or province.
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when Adyen address requirements are not met.
	 */
	public function __construct( $country, $street = null, $house_number_or_name = null, $postal_code = null, $city = null, $state_or_province = null ) {
		/*
		 * The two-character country code of the address.
		 *
		 * The permitted country codes are defined in ISO-3166-1 alpha-2 (e.g. 'NL').
		 */
		if ( 2 !== strlen( $country ) ) {
			throw new InvalidArgumentException(
				sprintf(
					'Given country `%s` not ISO 3166-1 alpha-2 value.',
					$country
				)
			);
		}

		/*
		 * The name of the street.
		 *
		 * > The house number should not be included in this field; it should be separately provided via houseNumberOrName.
		 *
		 * Required if either `houseNumberOrName`, `city`, `postalCode`, or `stateOrProvince` are provided.
		 */
		$fields = array_filter( array( $house_number_or_name, $city, $postal_code, $state_or_province ) );

		if ( empty( $street ) && ! empty( $fields ) ) {
			throw new InvalidArgumentException(
				'The name of the street is required if either `houseNumberOrName`, `city`, `postalCode`, or `stateOrProvince` are provided.'
			);
		}

		/*
		 * The postal code.
		 *
		 * A maximum of five (5) digits for an address in the USA, or a maximum of ten (10) characters for an address in all other countries.
		 *
		 * Required if either `houseNumberOrName`, `street`, `city`, or `stateOrProvince` are provided.
		 */
		$fields = array_filter( array( $house_number_or_name, $street, $city, $state_or_province ) );

		if ( empty( $postal_code ) && ! empty( $fields ) ) {
			throw new InvalidArgumentException(
				'The postal code is required if either `houseNumberOrName`, `street`, `city`, or `stateOrProvince` are provided.'
			);
		}

		if ( ! empty( $postal_code ) ) {
			$max = ( 'US' === $country ) ? 5 : 10;

			if ( strlen( $postal_code ) > $max ) {
				throw new InvalidArgumentException(
					sprintf(
						'Given postal code `%s` is longer then `%d` digits.',
						$postal_code,
						$max
					)
				);
			}
		}

		/*
		 * The name of the city.
		 *
		 * Required if either `houseNumberOrName`, `street`, `postalCode`, or `stateOrProvince` are provided.
		 */
		$fields = array_filter( array( $house_number_or_name, $street, $postal_code, $state_or_province ) );

		if ( empty( $city ) && ! empty( $fields ) ) {
			throw new InvalidArgumentException(
				'The name of the city is required if either `houseNumberOrName`, `street`, `postalCode`, or `stateOrProvince` are provided.'
			);
		}

		/*
		 * Two (2) characters for an address in the USA or Canada, or a maximum of three (3) characters for an address in all other countries.
		 *
		 * Required for an address in the USA or Canada if either `houseNumberOrName`, `street`, `city`, or `postalCode` are provided.
		 */
		$fields = array_filter( array( $house_number_or_name, $street, $city, $postal_code ) );

		if ( empty( $state_or_province ) && in_array( $country, array( 'CA', 'US' ), true ) && ! empty( $fields ) ) {
			throw new InvalidArgumentException(
				'State or province is required for an address in the USA or Canada if either `houseNumberOrName`, `street`, `city`, or `postalCode` are provided.'
			);
		}

		if ( ! empty( $state_or_province ) ) {
			$max = in_array( $country, array( 'CA', 'US' ), true ) ? 2 : 3;

			if ( strlen( $state_or_province ) > $max ) {
				throw new InvalidArgumentException(
					sprintf(
						'Given state or province `%s` is longer then `%d` digits.',
						$state_or_province,
						$max
					)
				);
			}
		}

		// Ok.
		$this->country              = $country;
		$this->street               = $street;
		$this->house_number_or_name = $house_number_or_name;
		$this->postal_code          = $postal_code;
		$this->city                 = $city;
		$this->state_or_province    = $state_or_province;
	}

	/**
	 * Get city.
	 *
	 * @return string|null
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * Get country.
	 *
	 * @return string
	 */
	public function get_country() {
		return $this->country;
	}

	/**
	 * Get house number or name.
	 *
	 * @return string|null
	 */
	public function get_house_number_or_name() {
		return $this->house_number_or_name;
	}

	/**
	 * Get postal code.
	 *
	 * @return string|null
	 */
	public function get_postal_code() {
		return $this->postal_code;
	}

	/**
	 * Get state or province.
	 *
	 * @return string|null
	 */
	public function get_state_or_province() {
		return $this->state_or_province;
	}

	/**
	 * Get street.
	 *
	 * @return string|null
	 */
	public function get_street() {
		return $this->street;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'country'           => $this->country,
				'city'              => $this->city,
				'houseNumberOrName' => $this->house_number_or_name,
				'postalCode'        => $this->postal_code,
				'stateOrProvince'   => $this->state_or_province,
				'street'            => $this->street,
			)
		);

		$object = (object) $properties;

		return $object;
	}
}
