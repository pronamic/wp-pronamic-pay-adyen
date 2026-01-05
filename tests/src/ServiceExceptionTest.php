<?php
/**
 * Service exception test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Service exception test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ServiceExceptionTest extends TestCase {
	/**
	 * Test service exception.
	 */
	public function test_service_exception() {
		$json = file_get_contents( __DIR__ . '/../json/service-exception.json', true );

		$data = json_decode( $json );

		$service_exception = ServiceException::from_object( $data );

		$this->assertEquals( 422, $service_exception->get_status() );
		$this->assertEquals( '174', $service_exception->get_error_code() );
		$this->assertEquals( 'Unable to decrypt data', $service_exception->get_message() );
		$this->assertEquals( ErrorType::VALIDATION, $service_exception->get_error_type() );
	}
}
