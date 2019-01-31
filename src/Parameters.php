<?php
/**
 * Parameters
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Parameters
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Parameters {
	/**
	 * Indicator for the 'merchantReference' parameter
	 *
	 * @var string
	 */
	const MERCHANT_REFERENCE = 'merchantReference';

	/**
	 * Indicator for the 'paymentAmount' parameter
	 *
	 * @var string
	 */
	const PAYMENT_AMOUNT = 'paymentAmount';

	/**
	 * Indicator for the 'currencyCode' parameter
	 *
	 * @var string
	 */
	const CURRENCY_CODE = 'currencyCode';

	/**
	 * Indicator for the 'shipBeforeDate' parameter
	 *
	 * @var string
	 */
	const SHIP_BEFORE_DATE = 'shipBeforeDate';

	/**
	 * Indicator for the 'skinCode' parameter
	 *
	 * @var string
	 */
	const SKIN_CODE = 'skinCode';

	/**
	 * Indicator for the 'merchantAccount' parameter
	 *
	 * @var string
	 */
	const MERCHANT_ACCOUNT = 'merchantAccount';

	/**
	 * Indicator for the 'shopperLocale' parameter
	 *
	 * @var string
	 */
	const SHOPPER_LOCALE = 'shopperLocale';

	/**
	 * Indicator for the 'orderData' parameter
	 *
	 * @var string
	 */
	const ORDER_DATA = 'orderData';

	/**
	 * Indicator for the 'sessionValidity' parameter
	 *
	 * @var string
	 */
	const SESSION_VALIDITY = 'sessionValidity';

	/**
	 * Indicator for the 'merchantSig' parameter
	 *
	 * @var string
	 */
	const MERCHANT_SIGNATURE = 'merchantSig';

	/**
	 * Indicator for the 'shopperEmail' parameter
	 *
	 * @var string
	 */
	const SHOPPER_EMAIL = 'shopperEmail';

	/**
	 * Indicator for the 'shopperEmail' parameter
	 *
	 * @var string
	 */
	const SHOPPER_REFERENCE = 'shopperReference';
}
