<?php
/**
 * Line item test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Line item test
 *
 * @link    https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 * @version 1.0.0
 * @since   1.0.0
 */
class LineItemTest extends TestCase {
	/**
	 * Line item.
	 *
	 * @var LineItem
	 */
	protected $line_item;

	/**
	 * Setup.
	 */
	public function setUp() {
		$this->line_item = new LineItem( 'Test', 1, 12399 );

		$this->line_item->set_id( 'TEST' );
		$this->line_item->set_amount_excluding_tax( 10247 );
		$this->line_item->set_tax_amount( 2152 );
		$this->line_item->set_tax_percentage( 21 );
		$this->line_item->set_tax_category( 'High' );
	}

	/**
	 * Test line item.
	 */
	public function test_line_item() {
		self::assertEquals( 'Test', $this->line_item->get_description() );
		self::assertEquals( 1, $this->line_item->get_quantity() );
		self::assertEquals( 'TEST', $this->line_item->get_id() );
		self::assertEquals( 12399, $this->line_item->get_amount_including_tax() );
		self::assertEquals( 10247, $this->line_item->get_amount_excluding_tax() );
		self::assertEquals( 2152, $this->line_item->get_tax_amount() );
		self::assertEquals( 21, $this->line_item->get_tax_percentage() );
		self::assertEquals( 'High', $this->line_item->get_tax_category() );
	}

	/**
	 * Test JSON.
	 */
	public function test_json() {
		$json_file = __DIR__ . '/../json/line-item.json';

		$json_data = json_decode( file_get_contents( $json_file, true ) );

		$json_string = wp_json_encode( $this->line_item, JSON_PRETTY_PRINT );

		self::assertEquals( wp_json_encode( $json_data, JSON_PRETTY_PRINT ), $json_string );

		self::assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}
}
