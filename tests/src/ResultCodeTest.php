<?php
/**
 * Result code test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Result code test.
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ResultCodeTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider result_code_matrix_provider
	 */
	public function test_transform( $adyen_result_code, $expected ) {
		$status = ResultCode::transform( $adyen_result_code );

		$this->assertEquals( $expected, $status );
	}

	public function result_code_matrix_provider() {
		return array(
			array( ResultCode::AUTHORIZED, \Pronamic\WordPress\Pay\Core\Statuses::SUCCESS ),
			array( ResultCode::CANCELLED, \Pronamic\WordPress\Pay\Core\Statuses::CANCELLED ),
			array( ResultCode::ERROR, \Pronamic\WordPress\Pay\Core\Statuses::FAILURE ),
			array( ResultCode::PENDING, \Pronamic\WordPress\Pay\Core\Statuses::OPEN ),
			array( ResultCode::RECEIVED, \Pronamic\WordPress\Pay\Core\Statuses::OPEN ),
			array( ResultCode::REDIRECT_SHOPPER, \Pronamic\WordPress\Pay\Core\Statuses::OPEN ),
			array( ResultCode::REFUSED, \Pronamic\WordPress\Pay\Core\Statuses::FAILURE ),
			array( 'not existing result code', null ),
		);
	}
}
