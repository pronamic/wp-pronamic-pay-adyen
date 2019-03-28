<?php
/**
 * Endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Endpoint
 *
 * @link https://docs.adyen.com/developers/development-resources/live-endpoints
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Endpoint {
	/**
	 * API endpoint test URL.
	 *
	 * @var string
	 */
	const API_URL_TEST = 'https://checkout-test.adyen.com/v41/%s';

	/**
	 * API endpoint live URL.
	 *
	 * @var string
	 */
	const API_URL_LIVE = 'https://%s-checkout-live.adyenpayments.com/checkout/v41/%s';
}
