<?php
/**
 * Line item test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Line item test
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class LineItemTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test line item.
	 */
	public function test_line_item() {
		$line_item = new LineItem( 'Test', 1, 12399 );

		$this->assertEquals( 'Test', $line_item->get_description() );
		$this->assertEquals( 1, $line_item->get_quantity() );
		$this->assertEquals( 12399, $line_item->get_amount_including_tax() );
	}
}
