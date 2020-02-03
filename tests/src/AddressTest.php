<?php
/**
 * Address test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Address test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/address
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AddressTest extends TestCase {
	/**
	 * Test address Netherlands.
	 */
	public function test_nl_address() {
		$address = new Address( 'NL', 'Burgemeester Wuiteweg', '39b', '9203 KA', 'Drachten' );

		$this->assertEquals( 'NL', $address->get_country() );
		$this->assertEquals( 'Drachten', $address->get_city() );
		$this->assertEquals( '39b', $address->get_house_number_or_name() );
		$this->assertEquals( '9203 KA', $address->get_postal_code() );
		$this->assertEquals( 'Burgemeester Wuiteweg', $address->get_street() );
		$this->assertNull( $address->get_state_or_province() );
	}

	/**
	 * Test address United States.
	 *
	 * @link https://automattic.com/contact/
	 */
	public function test_address_us() {
		$address = new Address( 'US', '60 29th Street', '343', '94110', 'San Francisco', 'CA' );

		$this->assertEquals( 'US', $address->get_country() );
		$this->assertEquals( 'San Francisco', $address->get_city() );
		$this->assertEquals( '343', $address->get_house_number_or_name() );
		$this->assertEquals( '94110', $address->get_postal_code() );
		$this->assertEquals( '60 29th Street', $address->get_street() );
		$this->assertEquals( 'CA', $address->get_state_or_province() );

		$this->assertEquals(
			(object) array(
				'country'           => 'US',
				'city'              => 'San Francisco',
				'houseNumberOrName' => '343',
				'postalCode'        => '94110',
				'stateOrProvince'   => 'CA',
				'street'            => '60 29th Street',
			),
			$address->get_json()
		);
	}

	/**
	 * Test country only.
	 */
	public function test_country_only() {
		$address = new Address( 'BE' );

		$this->assertEquals( 'BE', $address->get_country() );
		$this->assertNull( $address->get_city() );
		$this->assertNull( $address->get_house_number_or_name() );
		$this->assertNull( $address->get_postal_code() );
		$this->assertNull( $address->get_street() );
		$this->assertNull( $address->get_state_or_province() );
	}

	/**
	 * Test invalid country.
	 */
	public function test_invalid_country() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'A' );
	}

	/**
	 * Test required street.
	 */
	public function test_required_street() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'NL', null, '39b' );
	}

	/**
	 * Test required street.
	 */
	public function test_required_postal_code() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'NL', 'Burgemeester Wuiteweg', '39b', null );
	}

	/**
	 * Test invalid postal code.
	 */
	public function test_invalid_postal_code() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'NL', 'Burgemeester Wuiteweg', '39b', '1234567890 to long' );
	}

	/**
	 * Test required street.
	 */
	public function test_required_city() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'NL', 'Burgemeester Wuiteweg', '39b', '9203 KA', null );
	}

	/**
	 * Test required state or province.
	 *
	 * @link https://automattic.com/contact/
	 */
	public function test_required_state_or_province() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'US', '60 29th Street', '343', '94110', 'San Francisco', null );
	}

	/**
	 * Test invalid state or province.
	 *
	 * @link https://automattic.com/contact/
	 */
	public function test_invalid_state_or_province() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Address( 'US', '60 29th Street', '343', '94110', 'San Francisco', 'CA to long' );
	}
}
