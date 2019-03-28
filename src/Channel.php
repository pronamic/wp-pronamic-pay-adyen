<?php
/**
 * Channel
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;

/**
 * Channel
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
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
