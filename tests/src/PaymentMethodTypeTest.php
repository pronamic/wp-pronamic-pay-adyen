<?php
/**
 * Payment method type test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment method type test.
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodTypeTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform to WordPress.
	 *
	 * @param string $adyen_payment_method_type Adyen payment method type.
	 * @param string $wp_payment_method         WordPress payment method.
	 * @dataProvider transform_test_provider
	 */
	public function test_to_wp( $adyen_payment_method_type, $wp_payment_method ) {
		$result = PaymentMethodType::to_wp( $adyen_payment_method_type );

		$this->assertEquals( $wp_payment_method, $result );
	}

	/**
	 * Test transform to Adyen.
	 *
	 * @param string $adyen_payment_method_type Adyen payment method type.
	 * @param string $wp_payment_method         WordPress payment method.
	 * @dataProvider transform_test_provider
	 */
	public function test_to_adyen( $adyen_payment_method_type, $wp_payment_method ) {
		$result = PaymentMethodType::transform( $wp_payment_method );

		$this->assertEquals( $adyen_payment_method_type, $result );
	}

	/**
	 * Transform test provider.
	 *
	 * @return array
	 */
	public function transform_test_provider() {
		return array(
			array( PaymentMethodType::AFTERPAY, PaymentMethods::AFTERPAY ),
			array( PaymentMethodType::ALIPAY, PaymentMethods::ALIPAY ),
			array( PaymentMethodType::BANCONTACT, PaymentMethods::BANCONTACT ),
			array( PaymentMethodType::SCHEME, PaymentMethods::CREDIT_CARD ),
			array( PaymentMethodType::SEPA_DIRECT_DEBIT, PaymentMethods::DIRECT_DEBIT ),
			array( PaymentMethodType::GIROPAY, PaymentMethods::GIROPAY ),
			array( PaymentMethodType::IDEAL, PaymentMethods::IDEAL ),
			array( PaymentMethodType::KLARNA, PaymentMethods::KLARNA_PAY_LATER ),
			array( PaymentMethodType::MAESTRO, PaymentMethods::MAESTRO ),
			array( PaymentMethodType::PAYPAL, PaymentMethods::PAYPAL ),
			array( PaymentMethodType::DIRECT_EBANKING, PaymentMethods::SOFORT ),
			array( null, null ),
		);
	}
}
