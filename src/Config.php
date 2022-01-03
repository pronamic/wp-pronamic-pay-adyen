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
	 * API Key.
	 *
	 * @var string|null
	 */
	public $api_key;

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
	 * Origin key.
	 *
	 * @var string|null
	 */
	public $origin_key;

	/**
	 * Apple Pay merchant identifier.
	 *
	 * @var string|null
	 */
	public $apple_pay_merchant_id;

	/**
	 * Apple Pay merchant identity certificate.
	 *
	 * @var string|null
	 */
	public $apple_pay_merchant_id_certificate;

	/**
	 * Apple Pay merchant identity private key.
	 *
	 * @var string|null
	 */
	public $apple_pay_merchant_id_private_key;

	/**
	 * Apple Pay merchant identity private key password.
	 *
	 * @var string|null
	 */
	public $apple_pay_merchant_id_private_key_password;

	/**
	 * Google Pay merchant identifier.
	 *
	 * @var string|null
	 */
	public $google_pay_merchant_identifier;

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
	 * Get Apple Pay merchant identifier.
	 *
	 * @return string|null
	 */
	public function get_apple_pay_merchant_id() {
		return $this->apple_pay_merchant_id;
	}

	/**
	 * Get Apple Pay merchant identity certificate.
	 *
	 * @return string|null
	 */
	public function get_apple_pay_merchant_id_certificate() {
		return $this->apple_pay_merchant_id_certificate;
	}

	/**
	 * Get Apple Pay merchant identity private key.
	 *
	 * @return string|null
	 */
	public function get_apple_pay_merchant_id_private_key() {
		return $this->apple_pay_merchant_id_private_key;
	}

	/**
	 * Get Apple Pay merchant identity private key password.
	 *
	 * @return string|null
	 */
	public function get_apple_pay_merchant_id_private_key_password() {
		return $this->apple_pay_merchant_id_private_key_password;
	}

	/**
	 * Get Google Pay merchant identifier.
	 *
	 * @return string|null
	 */
	public function get_google_pay_merchant_identifier() {
		return $this->google_pay_merchant_identifier;
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
