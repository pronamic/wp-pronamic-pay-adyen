<?php
/**
 * Address transformer test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Address as Pay_Address;

/**
 * Address transformer test
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/address
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class AddressTransformerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 */
	public function test_transform() {
		$pay_address = new Pay_Address();

		$address = AddressTransformer::transform( $pay_address );
	}
}
