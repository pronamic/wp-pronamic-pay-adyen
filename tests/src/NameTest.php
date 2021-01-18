<?php
/**
 * Name test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Name test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/name
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NameTest extends TestCase {
	/**
	 * Test name.
	 */
	public function test_amount() {
		$name = new Name( 'John', 'Doe', Gender::MALE );

		$this->assertEquals( 'John', $name->get_first_name() );
		$this->assertEquals( 'Doe', $name->get_last_name() );
		$this->assertEquals( Gender::MALE, $name->get_gender() );
		$this->assertNull( $name->get_infix() );

		$name->set_infix( 'infix' );

		$this->assertEquals( 'infix', $name->get_infix() );
	}

	/**
	 * Test invalid infix.
	 */
	public function test_invalid_infix() {
		$name = new Name( 'John', 'Doe', Gender::MALE );

		$this->setExpectedException( 'InvalidArgumentException' );

		$name->set_infix( '12345678901234567890test' );
	}

	/**
	 * Test JSON.
	 */
	public function test_json() {
		$name = new Name( 'John', 'Doe', Gender::MALE );

		$object = $name->get_json();

		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../../json-schemas/name.json' ),
			)
		);

		$this->assertTrue( $validator->isValid() );
	}
}
