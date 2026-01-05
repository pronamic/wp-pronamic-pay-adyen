<?php
/**
 * Payment method type test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use PHPUnit\Framework\TestCase;
use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment method type test.
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodTypeTest extends TestCase {
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
		return [
			[ PaymentMethodType::AFTERPAY, PaymentMethods::AFTERPAY_COM ],
			[ PaymentMethodType::ALIPAY, PaymentMethods::ALIPAY ],
			[ PaymentMethodType::BANCONTACT, PaymentMethods::BANCONTACT ],
			[ PaymentMethodType::SCHEME, PaymentMethods::CREDIT_CARD ],
			[ PaymentMethodType::SEPA_DIRECT_DEBIT, PaymentMethods::DIRECT_DEBIT ],
			[ PaymentMethodType::GIROPAY, PaymentMethods::GIROPAY ],
			[ PaymentMethodType::IDEAL, PaymentMethods::IDEAL ],
			[ PaymentMethodType::KLARNA, PaymentMethods::KLARNA_PAY_LATER ],
			[ PaymentMethodType::MAESTRO, PaymentMethods::MAESTRO ],
			[ PaymentMethodType::PAYPAL, PaymentMethods::PAYPAL ],
			[ PaymentMethodType::DIRECT_EBANKING, PaymentMethods::SOFORT ],
			[ null, null ],
		];
	}
}
