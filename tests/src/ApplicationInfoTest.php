<?php
/**
 * Application info test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Application info test
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class ApplicationInfoTest extends \PHPUnit\Framework\TestCase {
	/**
	 * Test application info.
	 */
	public function test_application_info() {
		/**
		 * Application info.
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo
		 * @link https://docs.adyen.com/development-resources/building-adyen-solutions
		 */
		$application_info = new ApplicationInfo();

		$application_info->merchant_application = (object) [
			'name'    => 'Pronamic Pay',
			'version' => '5.9.0',
		];

		$application_info->external_platform = (object) [
			'integrator' => 'Pronamic',
			'name'       => 'WordPress',
			'version'    => '5.3.2',
		];

		$json_file = __DIR__ . '/../json/application-info.json';

		$json_data = json_decode( file_get_contents( $json_file, true ) );

		$json_string = wp_json_encode( $application_info, JSON_PRETTY_PRINT );

		self::assertEquals( wp_json_encode( $json_data, JSON_PRETTY_PRINT ), $json_string );

		self::assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}
}
