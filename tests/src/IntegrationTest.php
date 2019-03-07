<?php
/**
 * Integration test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Gender as Pay_Gender;

/**
 * Integration test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test integration.
	 */
	public function test_integration() {
		$integration = new Integration();

		$expected = __NAMESPACE__ . '\ConfigFactory';

		$class = $integration->get_config_factory_class();

		$this->assertEquals( $expected, $class );
		$this->assertTrue( class_exists( $class ) );
	}
}
