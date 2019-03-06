<?php
/**
 * Amount transformer test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Money\Money;

/**
 * Amount transformer test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/amount
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AmountTransformerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 */
	public function test_transform() {
		$money = new Money( 75.99, 'EUR' );

		$amount = AmountTransformer::transform( $money );

		$this->assertEquals( 'EUR', $amount->get_currency() );
		$this->assertEquals( 7599, $amount->get_value() );
	}
}
