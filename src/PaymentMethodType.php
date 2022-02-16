<?php
/**
 * Payment method type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment method type
 *
 * @link https://docs.adyen.com/developers/classic-integration/directory-lookup#DirectoryLookup-Step2:Displaylocalpaymentmethods
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.0
 */
class PaymentMethodType {
	/**
	 * Constant for the 'scheme' payment method type.
	 *
	 * @link https://docs.adyen.com/developers/development-resources/test-cards/test-card-numbers
	 *
	 * @var string
	 */
	const SCHEME = 'scheme';

	/**
	 * Constant for the 'afterpay_default' payment method type.
	 *
	 * Note: this is for Afterpay (afterpay.com) and not for AfterPay (afterpay.nl).
	 *
	 * @deprecated We have deprecated this constant because we can no longer find it in the Adyen documentation.
	 * @var string
	 */
	const AFTERPAY = 'afterpay_default';

	/**
	 * Constant for the 'afterpaytouch' payment method type.
	 *
	 * Note: this is for Afterpay (afterpay.com) and not for AfterPay (afterpay.nl).
	 *
	 * @link https://en.wikipedia.org/wiki/Afterpay
	 * @link https://docs.adyen.com/payment-methods/afterpaytouch/api-only
	 * @link https://docs.adyen.com/payment-methods/afterpaytouch
	 * @var string
	 */
	const AFTERPAY_TOUCH = 'afterpaytouch';

	/**
	 * Constant for the 'alipay' payment method type.
	 *
	 * @var string
	 */
	const ALIPAY = 'alipay';

	/**
	 * Constant for the 'applepay' payment method type.
	 *
	 * @var string
	 */
	const APPLE_PAY = 'applepay';

	/**
	 * Constant for the 'bcmc' payment method type.
	 *
	 * @var string
	 */
	const BANCONTACT = 'bcmc';

	/**
	 * Constant for the 'blik' payment method type.
	 *
	 * @var string
	 */
	const BLIK = 'blik';

	/**
	 * Constant for the 'sepadirectdebit' payment method type.
	 *
	 * @var string
	 */
	const DIRECT_DEBIT = 'sepadirectdebit';

	/**
	 * Constant for the 'directEbanking' payment method type.
	 *
	 * @var string
	 */
	const DIRECT_EBANKING = 'directEbanking';

	/**
	 * Constant for the 'dotpay' payment method type.
	 *
	 * @var string
	 */
	const DOTPAY = 'dotpay';

	/**
	 * Constant for the 'eps' payment method type.
	 *
	 * @var string
	 */
	const EPS = 'eps';

	/**
	 * Constant for the 'GiroPay' payment method type.
	 *
	 * @var string
	 */
	const GIROPAY = 'giropay';

	/**
	 * Constant for the 'paywithgoogle' payment method type.
	 *
	 * @var string
	 */
	const GOOGLE_PAY = 'paywithgoogle';

	/**
	 * Constant for the 'ideal' payment method type.
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Constant for the 'klarna' payment method type, for Klarna — Pay later.
	 *
	 * @link https://docs.adyen.com/payment-methods/klarna/api-only#make-a-payment
	 * @var string
	 */
	const KLARNA = 'klarna';

	/**
	 * Constant for the 'klarna_paynow' payment method type, for Klarna — Pay Now.
	 *
	 * @link https://docs.adyen.com/payment-methods/klarna/api-only#make-a-payment
	 * @var string
	 */
	const KLARNA_PAY_NOW = 'klarna_paynow';

	/**
	 * Constant for the 'klarna_account' payment method type, for Klarna — Pay over time.
	 *
	 * @link https://docs.adyen.com/payment-methods/klarna/api-only#make-a-payment
	 * @var string
	 */
	const KLARNA_ACCOUNT = 'klarna_account';

	/**
	 * Constant for the 'maestro' payment method type.
	 *
	 * @var string
	 */
	const MAESTRO = 'maestro';

	/**
	 * Constant for the 'mbway' payment method type.
	 *
	 * @var string
	 */
	const MB_WAY = 'mbway';

	/**
	 * Constant for the 'Multibanco' payment method type.
	 *
	 * @var string
	 */
	const MULTIBANCO = 'multibanco';

	/**
	 * Constant for the 'PayPal' payment method type.
	 *
	 * @var string
	 */
	const PAYPAL = 'paypal';

	/**
	 * Constant for the 'SEPA Direct Debit' payment method type.
	 *
	 * @var string
	 */
	const SEPA_DIRECT_DEBIT = 'sepadirectdebit';

	/**
	 * Constant for the 'Swish' payment method type.
	 *
	 * @var string
	 */
	const SWISH = 'swish';

	/**
	 * Constant for the 'TWINT' payment method type.
	 *
	 * @var string
	 */
	const TWINT = 'twint';

	/**
	 * Constant for the 'UnionPay' payment method type.
	 *
	 * @var string
	 */
	const UNIONPAY = 'unionpay';

	/**
	 * Constant for the 'Vipps' payment method type.
	 *
	 * @var string
	 */
	const VIPPS = 'vipps';

	/**
	 * Map payment methods to brand codes.
	 *
	 * @var array<string, string>
	 */
	private static $map = array(
		PaymentMethods::AFTERPAY_COM         => self::AFTERPAY_TOUCH,
		PaymentMethods::ALIPAY               => self::ALIPAY,
		PaymentMethods::APPLE_PAY            => self::APPLE_PAY,
		PaymentMethods::BANCONTACT           => self::BANCONTACT,
		PaymentMethods::BLIK                 => self::BLIK,
		PaymentMethods::CREDIT_CARD          => self::SCHEME,
		PaymentMethods::DIRECT_DEBIT         => self::SEPA_DIRECT_DEBIT,
		PaymentMethods::EPS                  => self::EPS,
		PaymentMethods::GIROPAY              => self::GIROPAY,
		PaymentMethods::GOOGLE_PAY           => self::GOOGLE_PAY,
		PaymentMethods::IDEAL                => self::IDEAL,
		PaymentMethods::KLARNA_PAY_LATER     => self::KLARNA,
		PaymentMethods::KLARNA_PAY_NOW       => self::KLARNA_PAY_NOW,
		PaymentMethods::KLARNA_PAY_OVER_TIME => self::KLARNA_ACCOUNT,
		PaymentMethods::MAESTRO              => self::MAESTRO,
		PaymentMethods::MB_WAY               => self::MB_WAY,
		PaymentMethods::PAYPAL               => self::PAYPAL,
		PaymentMethods::SOFORT               => self::DIRECT_EBANKING,
		PaymentMethods::SWISH                => self::SWISH,
		PaymentMethods::TWINT                => self::TWINT,
		PaymentMethods::VIPPS                => self::VIPPS,
	);

	/**
	 * Transform WordPress payment method to Adyen brand code.
	 *
	 * @param string|null $payment_method Payment method.
	 * @return string|null
	 */
	public static function transform( $payment_method ) {
		if ( null === $payment_method ) {
			return null;
		}

		if ( array_key_exists( $payment_method, self::$map ) ) {
			return self::$map[ $payment_method ];
		}

		return null;
	}

	/**
	 * Transform Adyen method to WordPress payment method.
	 *
	 * @param string $adyen_type Adyen method type.
	 * @return string|null
	 */
	public static function to_wp( $adyen_type ) {
		$result = array_search( $adyen_type, self::$map, true );

		if ( false === $result ) {
			return null;
		}

		return $result;
	}
}
