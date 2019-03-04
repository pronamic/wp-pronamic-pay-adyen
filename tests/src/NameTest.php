<?php
/**
 * Name test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Name test
 *
 * @link hhttps://docs.adyen.com/developers/api-reference/common-api/name
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NameTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test name.
	 */
	public function test_amount() {
		$name = new Name( 'John', 'Doe', Gender::MALE );

		$this->assertEquals( 'John', $name->get_first_name() );
		$this->assertEquals( 'Doe', $name->get_last_name() );
		$this->assertEquals( Gender::MALE, $name->get_gender() );
		$this->assertNull( $name->get_infix() );
	}
}
