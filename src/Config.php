<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSerializable;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config class
 */
class Config extends GatewayConfig implements JsonSerializable {
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

	/**
	 * Serialize to JSON.
	 *
	 * @link https://www.w3.org/TR/json-ld11/#specifying-the-type
	 * @return object
	 */
	public function jsonSerialize(): object {
		return (object) [
			'@type'            => __CLASS__,
			'environment'      => $this->environment,
			'merchant_account' => (string) $this->merchant_account,
			'api_key'          => (string) $this->api_key,
			'client_key'       => (string) $this->client_key,
		];
	}
}
