<?php
/**
 * Gender transformer test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Gender as Pay_Gender;

/**
 * Gender transformer test
 *
 * @link    https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 * @version 1.0.0
 * @since   1.0.0
 */
class GenderTransformerTest extends TestCase {
	/**
	 * Test transform.
	 *
	 * @param string $pay_gender            WordPress pay gender value.
	 * @param string $expected_adyen_gender Expected Adyen gender value after transformation.
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
		return [
			[ Pay_Gender::FEMALE, Gender::FEMALE ],
			[ Pay_Gender::MALE, Gender::MALE ],
			[ Pay_Gender::OTHER, Gender::UNKNOWN ],
			[ 'not existing result code', Gender::UNKNOWN ],
			[ null, Gender::UNKNOWN ],
		];
	}
}
