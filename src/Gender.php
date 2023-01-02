<?php
/**
 * Gender
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Gender class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 */
class Gender {
	/**
	 * Indicator for the 'MALE' gender.
	 *
	 * @var string
	 */
	const MALE = 'MALE';

	/**
	 * Indicator for the 'FEMALE' gender.
	 *
	 * @var string
	 */
	const FEMALE = 'FEMALE';

	/**
	 * Indicator for the 'UNKNOWN' gender.
	 *
	 * @var string
	 */
	const UNKNOWN = 'UNKNOWN';
}
