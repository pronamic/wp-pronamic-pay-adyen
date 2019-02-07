<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Gateways\Common\AbstractIntegration;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Integration extends AbstractIntegration {
	/**
	 * Integration constructor.
	 */
	public function __construct() {
		$this->id            = 'adyen';
		$this->name          = 'Adyen';
		$this->provider      = 'adyen';
		$this->url           = 'https://www.adyen.com/';
		$this->dashboard_url = array(
			__( 'test', 'pronamic_ideal' ) => 'https://ca-test.adyen.com/ca/ca/login.shtml',
			__( 'live', 'pronamic_ideal' ) => 'https://ca-live.adyen.com/ca/ca/login.shtml',
		);

		/**
		 * Webhook listener function.
		 *
		 * @var callable $webhook_listener_function
		 */
		$webhook_listener_function = array( __NAMESPACE__ . '\WebhookListener', 'listen' );

		if ( ! has_action( 'wp_loaded', $webhook_listener_function ) ) {
			add_action( 'wp_loaded', $webhook_listener_function );
		}
	}

	/**
	 * Get config factory class.
	 *
	 * @return string
	 */
	public function get_config_factory_class() {
		return __NAMESPACE__ . '\ConfigFactory';
	}

	/**
	 * Get settings class.
	 *
	 * @return string
	 */
	public function get_settings_class() {
		return __NAMESPACE__ . '\Settings';
	}

	/**
	 * Get required settings for this integration.
	 *
	 * @link https://github.com/wp-premium/gravityforms/blob/1.9.16/includes/fields/class-gf-field-multiselect.php#L21-L42
	 * @since 1.1.6
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		$settings[] = 'adyen';

		return $settings;
	}
}
