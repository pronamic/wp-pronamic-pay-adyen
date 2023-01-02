<?php
/**
 * Error type
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Error type class
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/serviceexception
 */
class ErrorType {
	/**
	 * Indicator for 'internal' error type.
	 *
	 * @var string
	 */
	const INTERNAL = 'internal';

	/**
	 * Indicator for 'validation' error type.
	 *
	 * @var string
	 */
	const VALIDATION = 'validation';

	/**
	 * Indicator for 'security' error type.
	 *
	 * @var string
	 */
	const SECURITY = 'security';

	/**
	 * Indicator for 'configuration' error type.
	 *
	 * @var string
	 */
	const CONFIGURATION = 'configuration';
}
