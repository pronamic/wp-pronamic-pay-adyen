<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   2.0.0
 */
class Config extends GatewayConfig {
	/**
	 * API Key.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * Merchant Account.
	 *
	 * @var string
	 */
	public $merchant_account;
}
