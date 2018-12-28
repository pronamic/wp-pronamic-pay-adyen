<?php
/**
 * Payment method type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment method type
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   2.0.0
 * @link https://docs.adyen.com/developers/classic-integration/directory-lookup#DirectoryLookup-Step2:Displaylocalpaymentmethods
 */
class PaymentMethodType {
	/**
	 * Constant for the 'scheme' payment method type.
	 *
	 * @var string
	 */
	const SCHEME = 'scheme';

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
	const DOTPAT = 'dotpay';

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
	 * Constant for the 'Multibanco' payment method type.
	 *
	 * @var string
	 */
	const MULTIBANCO = 'multibanco';

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
}
