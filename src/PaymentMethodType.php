<?php
/**
 * Payment method type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
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
 * @version 1.0.0
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
	 * @var string
	 */
	const AFTERPAY = 'afterpay_default';

	/**
	 * Constant for the 'alipay' payment method type.
	 *
	 * @var string
	 */
	const ALIPAY = 'alipay';

	/**
	 * Constant for the 'bcmc' payment method type.
	 *
	 * @var string
	 */
	const BANCONTACT = 'bcmc';

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
	 * Constant for the 'GiroPay' payment method type.
	 *
	 * @var string
	 */
	const GIROPAY = 'giropay';

	/**
	 * Constant for the 'ideal' payment method type.
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Constant for the 'klarna' payment method type.
	 *
	 * @var string
	 */
	const KLARNA = 'klarna';

	/**
	 * Constant for the 'maestro' payment method type.
	 *
	 * @var string
	 */
	const MAESTRO = 'maestro';

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
	 * Constant for the 'UnionPay' payment method type.
	 *
	 * @var string
	 */
	const UNIONPAY = 'unionpay';

	/**
	 * Map payment methods to brand codes.
	 *
	 * @var array
	 */
	private static $map = array(
		PaymentMethods::AFTERPAY         => self::AFTERPAY,
		PaymentMethods::ALIPAY           => self::ALIPAY,
		PaymentMethods::BANCONTACT       => self::BANCONTACT,
		PaymentMethods::CREDIT_CARD      => self::SCHEME,
		PaymentMethods::DIRECT_DEBIT     => self::SEPA_DIRECT_DEBIT,
		PaymentMethods::GIROPAY          => self::GIROPAY,
		PaymentMethods::IDEAL            => self::IDEAL,
		PaymentMethods::KLARNA_PAY_LATER => self::KLARNA,
		PaymentMethods::MAESTRO          => self::MAESTRO,
		PaymentMethods::PAYPAL           => self::PAYPAL,
		PaymentMethods::SOFORT           => self::DIRECT_EBANKING,
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
