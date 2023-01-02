<?php
/**
 * Channel
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Channel class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 */
class Channel {
	/**
	 * Channel iOS.
	 *
	 * @var string
	 */
	const IOS = 'iOS';

	/**
	 * Channel Android.
	 *
	 * @var string
	 */
	const ANDROID = 'Android';

	/**
	 * Channel web.
	 *
	 * @var string
	 */
	const WEB = 'Web';
}
