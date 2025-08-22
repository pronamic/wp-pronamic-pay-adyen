<?php
/**
 * Address transformer test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2025 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Address as Pay_Address;
use Pronamic\WordPress\Pay\Country;
use Pronamic\WordPress\Pay\HouseNumber;
use Pronamic\WordPress\Pay\Region;

/**
 * Address transformer test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/address
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AddressTransformerTest extends TestCase {
	/**
	 * Test empty address.
	 */
	public function test_transform() {
		$pay_address = new Pay_Address();

		$address = AddressTransformer::transform( $pay_address );

		$this->assertNull( $address );
	}

	/**
	 * Test address with country.
	 */
	public function test_address_country() {
		$country = new Country();
		$country->set_code( 'NL' );

		$address = new Pay_Address();
		$address->set_country( $country );

		$adyen_address = AddressTransformer::transform( $address );

		$this->assertEquals( 'NL', $adyen_address->get_country() );
	}

	/**
	 * Test address without country code.
	 */
	public function test_address_without_country_cde() {
		$country = new Country();

		$address = new Pay_Address();
		$address->set_country( $country );

		$adyen_address = AddressTransformer::transform( $address );

		$this->assertNull( $adyen_address );
	}

	/**
	 * Test address United States.
	 */
	public function test_address_us() {
		$country = new Country();
		$country->set_code( 'US' );
		$country->set_name( 'United States' );

		$region = new Region();
		$region->set_code( 'CA' );
		$region->set_name( 'California' );

		$address = new Pay_Address();
		$address->set_country( $country );
		$address->set_region( $region );
		$address->set_street_name( '60 29th Street' );
		$address->set_house_number( new HouseNumber( '343' ) );
		$address->set_postal_code( '94110' );
		$address->set_city( 'San Francisco' );

		$adyen_address = AddressTransformer::transform( $address );

		$this->assertEquals( 'US', $adyen_address->get_country() );
		$this->assertEquals( 'San Francisco', $adyen_address->get_city() );
		$this->assertEquals( '343', $adyen_address->get_house_number_or_name() );
		$this->assertEquals( '94110', $adyen_address->get_postal_code() );
		$this->assertEquals( '60 29th Street', $adyen_address->get_street() );
		$this->assertEquals( 'CA', $adyen_address->get_state_or_province() );
	}

	/**
	 * Test incomplete address.
	 */
	public function test_address_incomplete_address() {
		$country = new Country();
		$country->set_code( 'NL' );

		$address = new Pay_Address();
		$address->set_country( $country );
		$address->set_street_name( '60 29th Street' );

		$adyen_address = AddressTransformer::transform( $address );

		$this->assertNull( $adyen_address );
	}
}
