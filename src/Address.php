<?php
/**
 * Address
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

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
	 * @param string $country Country.
	 */
	public function __construct( $country ) {
		$this->set_country( $country );
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
	 * Set city.
	 *
	 * @param string|null $city City.
	 */
	public function set_city( $city ) {
		$this->city = $city;
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
	 * Set country.
	 *
	 * @param string $country Country.
	 */
	public function set_country( $country ) {
		$this->country = $country;
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
	 * Set house number or name.
	 *
	 * @param string|null $house_number_or_name House number or name.
	 */
	public function set_house_number_or_name( $house_number_or_name ) {
		$this->house_number_or_name = $house_number_or_name;
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
	 * Set postal code.
	 *
	 * @param string|null $ostal_code Postal code.
	 */
	public function set_postal_code( $postal_code ) {
		$this->postal_code = $postal_code;
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
	 * Set state or province.
	 *
	 * @param string|null $state_or_province State or province.
	 */
	public function set_state_or_province( $state_or_province ) {
		$this->state_or_province = $state_or_province;
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
	 * Set street.
	 *
	 * @param string|null $street Street.
	 */
	public function set_street( $street ) {
		$this->street = $street;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		// City.
		if ( null !== $this->city ) {
			$object->city = $this->city;
		}

		// Country.
		$object->country = $this->country;

		// House number or name.
		if ( null !== $this->house_number_or_name ) {
			$object->houseNumberOrName = $this->house_number_or_name;
		}

		// Postal code.
		if ( null !== $this->postal_code ) {
			$object->postalCode = $this->postal_code;
		}

		// State or province.
		if ( null !== $this->state_or_province ) {
			$object->stateOrProvince = $this->state_or_province;
		}

		// Street.
		if ( null !== $this->street ) {
			$object->street = $this->street;
		}

		// Return object.
		return $object;
	}
}
