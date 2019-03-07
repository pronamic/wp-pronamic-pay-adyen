<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Config extends GatewayConfig {
	/**
	 * API Key.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * API Live URL Prefix.
	 *
	 * @var string
	 */
	public $api_live_url_prefix;

	/**
	 * Merchant Account.
	 *
	 * @var string
	 */
	public $merchant_account;

	/**
	 * Get API key.
	 *
	 * @return string
	 */
	public function get_api_key() {
		return $this->api_key;
	}

	/**
	 * Get merchant account.
	 *
	 * @return string
	 */
	public function get_merchant_account() {
		return $this->merchant_account;
	}

	/**
	 * Get API URL.
	 *
	 * @param string $method API method.
	 * @return string
	 */
	public function get_api_url( $method ) {
		if ( Core_Gateway::MODE_TEST === $this->mode ) {
			return sprintf( Endpoint::API_URL_TEST, $method );
		}

		return sprintf( Endpoint::API_URL_LIVE, $this->api_live_url_prefix, $method );
	}
}
