<?php
/**
 * Settings test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_UnitTestCase;

/**
 * Settings test.
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class SettingsTest extends WP_UnitTestCase {
	/**
	 * Test settings.
	 */
	public function test_settings() {
		$settings = new Settings();

		$sections = apply_filters( 'pronamic_pay_gateway_sections', array() );

		$this->assertArrayHasKey( 'adyen', $sections );
		$this->assertArrayHasKey( 'adyen_feedback', $sections );
		$this->assertCount( 2, $sections );

		$fields = apply_filters( 'pronamic_pay_gateway_fields', array() );

		$this->assertCount( 8, $fields );
	}
}
