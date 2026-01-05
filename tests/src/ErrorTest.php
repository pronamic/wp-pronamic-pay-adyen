<?php
/**
 * Error test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Error test
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class ErrorTest extends TestCase {
	/**
	 * Test error.
	 */
	public function test_error() {
		$error = new Error( 403, 'Forbidden', '/checkout//v41/paymentMethods/' );

		$this->assertEquals( 403, $error->get_code() );
		$this->assertEquals( 'Forbidden', $error->get_message() );
		$this->assertEquals( '/checkout//v41/paymentMethods/', $error->get_requested_uri() );
	}

	/**
	 * Test from object.
	 */
	public function test_from_object() {
		$json = file_get_contents( __DIR__ . '/../json/error.json', true );

		$data = json_decode( $json );

		$error = Error::from_object( $data );

		$this->assertEquals( 403, $error->get_code() );
		$this->assertEquals( 'Forbidden', $error->get_message() );
		$this->assertEquals( '/checkout//v41/paymentMethods/', $error->get_requested_uri() );
	}
}
