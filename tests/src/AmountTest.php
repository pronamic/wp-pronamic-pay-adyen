<?php
/**
 * Amount test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use stdClass;

/**
 * Amount test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/amount
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AmountTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test amount.
	 */
	public function test_amount() {
		$amount = new Amount( 'EUR', 12375 );

		$this->assertEquals( 'EUR', $amount->get_currency() );
		$this->assertEquals( 12375, $amount->get_value() );
	}

	/**
	 * Test from object.
	 *
	 * @dataProvider provider_from_object
	 *
	 * @param object $object Object to create Amount from.
	 */
	public function test_from_object( $object ) {
		if ( ! isset( $object->currency, $object->value ) ) {
			$this->setExpectedException( 'JsonSchema\Exception\ValidationException' );
		}

		$amount = Amount::from_object( $object );

		self::assertEquals( 'EUR', $amount->get_currency() );
		self::assertEquals( 12375, $amount->get_value() );
	}

	/**
	 * Provider for test from object.
	 *
	 * @return array
	 */
	public function provider_from_object() {
		$data = array();

		// No currency, no value.
		$data[] = array( new stdClass() );

		// Currency only.
		$object           = new stdClass();
		$object->currency = 'EUR';

		$data[] = array( $object );

		// Currency and value.
		$object           = new stdClass();
		$object->currency = 'EUR';
		$object->value    = 12375;

		$data[] = array( $object );

		return $data;
	}
}
