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
}
