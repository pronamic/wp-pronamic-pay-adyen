<?php
/**
 * Gender transformer test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Gender as Pay_Gender;

/**
 * Gender transformer test
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class GenderTransformerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider transform_provider
	 */
	public function test_transform( $pay_gender, $expected_adyen_gender ) {
		$adyen_gender = GenderTransformer::transform( $pay_gender );

		$this->assertEquals( $expected_adyen_gender, $adyen_gender );
	}

	/**
	 * Transform provider.
	 *
	 * @return array
	 */
	public function transform_provider() {
		return array(
			array( Pay_Gender::FEMALE, Gender::FEMALE ),
			array( Pay_Gender::MALE, Gender::MALE ),
			array( Pay_Gender::OTHER, Gender::UNKNOWN ),
			array( 'not existing result code', Gender::UNKNOWN ),
			array( null, Gender::UNKNOWN ),
		);
	}
}
