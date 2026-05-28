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
			[ PaymentMethodType::AFTERPAY_TOUCH, PaymentMethods::AFTERPAY_COM ],
			[ PaymentMethodType::ALIPAY, PaymentMethods::ALIPAY ],
			[ PaymentMethodType::APPLE_PAY, PaymentMethods::APPLE_PAY ],
			[ PaymentMethodType::BANCONTACT, PaymentMethods::BANCONTACT ],
			[ PaymentMethodType::BLIK, PaymentMethods::BLIK ],
			[ PaymentMethodType::SCHEME, PaymentMethods::CREDIT_CARD ],
			[ PaymentMethodType::SEPA_DIRECT_DEBIT, PaymentMethods::DIRECT_DEBIT ],
			[ PaymentMethodType::EPS, PaymentMethods::EPS ],
			[ PaymentMethodType::GIROPAY, PaymentMethods::GIROPAY ],
			[ PaymentMethodType::GOOGLE_PAY, PaymentMethods::GOOGLE_PAY ],
			[ PaymentMethodType::IDEAL, PaymentMethods::IDEAL ],
			[ PaymentMethodType::KLARNA, PaymentMethods::KLARNA_PAY_LATER ],
			[ PaymentMethodType::KLARNA_PAY_NOW, PaymentMethods::KLARNA_PAY_NOW ],
			[ PaymentMethodType::KLARNA_ACCOUNT, PaymentMethods::KLARNA_PAY_OVER_TIME ],
			[ PaymentMethodType::MAESTRO, PaymentMethods::MAESTRO ],
			[ PaymentMethodType::MB_WAY, PaymentMethods::MB_WAY ],
			[ PaymentMethodType::MOBILEPAY, PaymentMethods::MOBILEPAY ],
			[ PaymentMethodType::ONLINE_BANKING_CZ, PaymentMethods::ONLINE_BANKING_CZ ],
			[ PaymentMethodType::ONLINE_BANKING_SK, PaymentMethods::ONLINE_BANKING_SK ],
			[ PaymentMethodType::PAYBYBANK, PaymentMethods::PAY_BY_BANK ],
			[ PaymentMethodType::PAYPAL, PaymentMethods::PAYPAL ],
			[ PaymentMethodType::DIRECT_EBANKING, PaymentMethods::SOFORT ],
			[ PaymentMethodType::SWISH, PaymentMethods::SWISH ],
			[ PaymentMethodType::TWINT, PaymentMethods::TWINT ],
			[ PaymentMethodType::VIPPS, PaymentMethods::VIPPS ],
			[ null, null ],
		];
	}
}
