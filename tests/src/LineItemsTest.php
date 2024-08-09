<?php
/**
 * Line items test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Line items test
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class LineItemsTest extends TestCase {
	/**
	 * Test line items.
	 *
	 * @dataProvider provider_line_items
	 *
	 * @param LineItem[] $items    Line items input.
	 * @param LineItem[] $expected Expected line items.
	 */
	public function test_line_items( $items, $expected ) {
		$line_items = new LineItems( $items );

		self::assertEquals( $expected, $line_items->get_line_items() );
	}

	/**
	 * Provider line items.
	 *
	 * @return array
	 */
	public function provider_line_items() {
		$line_item = new LineItem( 'Test', 1, 12399 );

		return [
			[ null, [] ],
			[ 1, [] ],
			[ '', [] ],
			[ [], [] ],
			[ [ $line_item ], [ $line_item ] ],
		];
	}

	/**
	 * Test new item.
	 */
	public function test_new_item() {
		$line_items = new LineItems();

		$line_items->new_item( 'Test', 1, 12399 );

		$expected = [
			new LineItem( 'Test', 1, 12399 ),
		];

		self::assertEquals( $expected, $line_items->get_line_items() );
	}

	/**
	 * Test JSON.
	 */
	public function test_json() {
		$line_items = new LineItems(
			[
				new LineItem( 'Test', 1, 12399 ),
				new LineItem( 'Test', 1, 12399 ),
			]
		);

		$json_file = __DIR__ . '/../json/line-items.json';

		$json_data = json_decode( file_get_contents( $json_file, true ) );

		$json_string = wp_json_encode( $line_items, JSON_PRETTY_PRINT );

		self::assertEquals( wp_json_encode( $json_data, JSON_PRETTY_PRINT ), $json_string );

		self::assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}
}
