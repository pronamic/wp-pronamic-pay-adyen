<?php
/**
 * Amount test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Amount test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/amount
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AmountTest extends TestCase {
	/**
	 * Test amount.
	 */
	public function test_amount() {
		$amount = new Amount( 'EUR', 12375 );

		$this->assertEquals( 'EUR', $amount->get_currency() );
		$this->assertEquals( 12375, $amount->get_value() );
		$this->assertEquals( '{"currency":"EUR","value":12375}', wp_json_encode( $amount ) );
	}

	/**
	 * Test invalid currency.
	 */
	public function test_invalid_currency() {
		$this->expectException( 'InvalidArgumentException' );

		new Amount( 'TE', 12375 );
	}

	/**
	 * Test from object.
	 *
	 * @dataProvider provider_from_object
	 *
	 * @param object $value Object to create Amount from.
	 */
	public function test_from_object( $value ) {
		if ( ! isset( $value->currency, $value->value ) ) {
			$this->expectException( 'JsonSchema\Exception\ValidationException' );
		}

		$amount = Amount::from_object( $value );

		self::assertEquals( 'EUR', $amount->get_currency() );
		self::assertEquals( 12375, $amount->get_value() );
	}

	/**
	 * Provider for test from object.
	 *
	 * @return array
	 */
	public function provider_from_object() {
		return [
			[
				(object) [],
			],
			[
				(object) [
					'currency' => 'EUR',
				],
			],
			[
				(object) [
					'currency' => 'EUR',
					'value'    => 12375,
				],
			],
		];
	}
}
