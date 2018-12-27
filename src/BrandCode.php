<?php
/**
 * Brand codes
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Brand codes
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   2.0.0
 * @link https://docs.adyen.com/developers/classic-integration/directory-lookup#DirectoryLookup-Step2:Displaylocalpaymentmethods
 */
class BrandCode {
	/**
	 * Constant for the iDEAL brand code.
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Constant for the SEPA Direct Debit brand code.
	 *
	 * @var string
	 */
	const SEPA_DIRECT_DEBIT = 'sepadirectdebit';

	/**
	 * Constant for the PayPal brand code.
	 *
	 * @var string
	 */
	const PAYPAL = 'paypal';
}
