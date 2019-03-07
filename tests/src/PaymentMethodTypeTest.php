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
	 * Test transform.
	 *
	 * @param string $adyen_payment_method_type Adyen payment method type.
	 * @param string $expected_payment_method   Expectd WordPress payment method.
	 * @dataProvider payment_method_type_matrix_provider
	 */
	public function test_transform( $adyen_payment_method_type, $expected_payment_method ) {
		$payment_method = PaymentMethodType::transform_gateway_method( $adyen_payment_method_type );

		$this->assertEquals( $expected_payment_method, $payment_method );
	}

	/**
	 * Transform test provider.
	 *
	 * @return array
	 */
	public function payment_method_type_matrix_provider() {
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
			array( 'not existing result code', null ),
		);
	}
}
