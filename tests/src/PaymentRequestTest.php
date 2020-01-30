<?php
/**
 * Payment request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;

/**
 * Payment request test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentRequestTest extends TestCase {
	/**
	 * Test payment request.
	 */
	public function test_payment_request() {
		$json_file = __DIR__ . '/../json/payment-request.json';

		$payment_method = array(
			'type'   => PaymentMethodType::IDEAL,
			'issuer' => '1121',
		);

		$payment_request = new PaymentRequest(
			new Amount( 'EUR', 1000 ),
			'YOUR_MERCHANT_ACCOUNT',
			'Your order number',
			'https://your-company.com/...',
			new PaymentMethod( (object) $payment_method )
		);

		/**
		 * Application info.
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo
		 * @link https://docs.adyen.com/development-resources/building-adyen-solutions
		 */
		$application_info = new ApplicationInfo();

		$application_info->merchant_application = (object) array(
			'name'    => 'Pronamic Pay',
			'version' => '5.9.0',
		);

		$application_info->external_platform = (object) array(
			'integrator' => 'Pronamic',
			'name'       => 'WordPress',
			'version'    => '5.3.2',
		);

		$payment_request->set_application_info( $application_info );

		// JSON.
		$json_string = wp_json_encode( $payment_request->get_json(), JSON_PRETTY_PRINT );

		$this->assertJsonStringEqualsJsonFile( $json_file, $json_string );
	}
}
