<?php
/**
 * Config factory
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\GatewayConfigFactory;

/**
 * Config factory
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ConfigFactory extends GatewayConfigFactory {
	/**
	 * Get configuration by post ID.
	 *
	 * @param string $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->post_id             = intval( $post_id );
		$config->mode                = $this->get_meta( $post_id, 'mode' );
		$config->api_key             = $this->get_meta( $post_id, 'adyen_api_key' );
		$config->api_live_url_prefix = $this->get_meta( $post_id, 'adyen_api_live_url_prefix' );
		$config->merchant_account    = $this->get_meta( $post_id, 'adyen_merchant_account' );

		return $config;
	}
}
