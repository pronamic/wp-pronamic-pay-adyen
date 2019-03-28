<?php
/**
 * Tax category
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Tax category
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class TaxCategory {
	/**
	 * High.
	 *
	 * @var string
	 */
	const HIGH = 'High';

	/**
	 * Low.
	 *
	 * @var string
	 */
	const LOW = 'Low';

	/**
	 * None.
	 *
	 * @var string
	 */
	const NONE = 'None';

	/**
	 * Zero.
	 *
	 * @var string
	 */
	const ZERO = 'Zero';
}
