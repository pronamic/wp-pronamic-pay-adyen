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
 * Config
 *
 * @author  Remco Tolsma
 * @version 1.1.1
 * @since   1.0.0
 */
class Config extends GatewayConfig {
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
	 * Get API URL.
	 *
	 * @param string $method API method.
	 * @return string
	 * @throws \Exception Throws exception when mode is live and API live URL prefix is empty.
	 */
	public function get_api_url( $method ) {
		if ( Core_Gateway::MODE_TEST === $this->mode ) {
			return sprintf( Endpoint::API_URL_TEST, $method );
		}

		if ( empty( $this->api_live_url_prefix ) ) {
			throw new \Exception( 'Adyen API Live URL prefix is required for live configurations.' );
		}

		return sprintf( Endpoint::API_URL_LIVE, $this->api_live_url_prefix, $method );
	}
}
