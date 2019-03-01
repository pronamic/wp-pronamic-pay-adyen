<?php
/**
 * Address test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Address test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/address
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AddressTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test address.
	 */
	public function test_address() {
		$address = new Address(
			'NL',
			'Burgemeester Wuiteweg',
			'39b',
			'9203 KA',
			'Drachten'
		);

		$this->assertEquals( 'NL', $address->get_country() );
		$this->assertEquals( 'Drachten', $address->get_city() );
		$this->assertEquals( '39b', $address->get_house_number_or_name() );
		$this->assertEquals( '9203 KA', $address->get_postal_code() );
		$this->assertEquals( 'Burgemeester Wuiteweg', $address->get_street() );
		$this->assertNull( $address->get_state_or_province() );
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
}
