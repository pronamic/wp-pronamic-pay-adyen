<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config class
 */
class Config extends GatewayConfig {
	/**
	 * Environment.
	 * 
	 * @var string
	 */
	public $environment;

	/**
	 * API Live URL Prefix.
	 *
	 * @var string|null
	 */
	public $api_live_url_prefix;

	/**
	 * Merchant Account.
	 *
	 * @var string|null
	 */
	public $merchant_account;

	/**
	 * Merchant Order Reference.
	 *
	 * @var string|null
	 */
	public $merchant_order_reference;

	/**
	 * API key.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * Client key.
	 *
	 * @var string
	 */
	public $client_key;

	/**
	 * Get API key.
	 *
	 * @return string|null
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
		return (string) $this->merchant_account;
	}

	/**
	 * Get merchant order reference.
	 *
	 * @return string
	 */
	public function get_merchant_order_reference() {
		return (string) $this->merchant_order_reference;
	}
}
