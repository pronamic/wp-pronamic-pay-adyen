<?php
/**
 * Drop-in gateway test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Address;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Util test
 *
 * @author  Reüel van der Steege
 * @version 2.1.0
 * @since   2.1.0
 */
class UtilTest extends TestCase {
	/**
	 * Test get country code.
	 *
	 * @param Payment     $payment  Payment.
	 * @param string|null $expected Expected country code.
	 * @return void
	 * @dataProvider provider_test_country_code
	 */
	public function test_get_country_code( $payment, $expected ) {
		$this->assertEquals( $expected, Util::get_country_code( $payment ) );
	}

	/**
	 * Data provider for country code test.
	 *
	 * @return array[]
	 */
	public function provider_test_country_code() {
		$data = [];

		// No country.
		$payment = new Payment();

		$data[] = [ $payment, null ];

		// Country code 'NL'.
		$payment = new Payment();

		$billing_address = new Address();

		$payment->set_billing_address( $billing_address );

		$billing_address->set_country_code( 'NL' );

		$data[] = [ $payment, 'NL' ];

		return $data;
	}
}
