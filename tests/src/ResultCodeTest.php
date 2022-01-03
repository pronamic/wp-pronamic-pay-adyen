<?php
/**
 * Result code test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Result code test.
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ResultCodeTest extends TestCase {
	/**
	 * Test transform.
	 *
	 * @param string $adyen_result_code       Adyen result code.
	 * @param string $expected_payment_status Expected WordPess payment status.
	 * @dataProvider result_code_matrix_provider
	 */
	public function test_transform( $adyen_result_code, $expected_payment_status ) {
		$status = ResultCode::transform( $adyen_result_code );

		$this->assertEquals( $expected_payment_status, $status );
	}

	/**
	 * Transform test provider.
	 *
	 * @return array
	 */
	public function result_code_matrix_provider() {
		return array(
			array( ResultCode::AUTHORIZED, \Pronamic\WordPress\Pay\Payments\PaymentStatus::SUCCESS ),
			array( ResultCode::CANCELLED, \Pronamic\WordPress\Pay\Payments\PaymentStatus::CANCELLED ),
			array( ResultCode::ERROR, \Pronamic\WordPress\Pay\Payments\PaymentStatus::FAILURE ),
			array( ResultCode::PENDING, \Pronamic\WordPress\Pay\Payments\PaymentStatus::OPEN ),
			array( ResultCode::RECEIVED, \Pronamic\WordPress\Pay\Payments\PaymentStatus::OPEN ),
			array( ResultCode::REDIRECT_SHOPPER, \Pronamic\WordPress\Pay\Payments\PaymentStatus::OPEN ),
			array( ResultCode::REFUSED, \Pronamic\WordPress\Pay\Payments\PaymentStatus::FAILURE ),
			array( 'not existing result code', null ),
		);
	}
}
