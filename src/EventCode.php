<?php
/**
 * Event code.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Event Codes.
 *
 * @link https://docs.adyen.com/developers/development-resources/notifications/event-codes
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class EventCode {
	/**
	 * Indicator for the 'AUTHORIZATION' event code.
	 *
	 * @var string
	 */
	const AUTHORIZATION = 'AUTHORISATION';

	/**
	 * Indicator for the 'REPORT_AVAILABLE' event code.
	 *
	 * @var string
	 */
	const REPORT_AVAILABLE = 'REPORT_AVAILABLE';

	/**
	 * Indicator for the 'PAIDOUT_REVERSED' event code.
	 *
	 * @var string
	 */
	const PAIDOUT_REVERSED = 'PAIDOUT_REVERSED';
}
