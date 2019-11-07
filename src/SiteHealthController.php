<?php
/**
 * Site Health controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Site Health controller
 *
 * @author  Remco Tolsma
 * @version unreleased
 * @since   unreleased
 */
class SiteHealthController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 *
	 * @return void
	 */
	public function rest_api_init() {
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/http-authorization-test',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, '__return_false' ),
				'permission_callback' => array( $this, '__return_false' ),
			)
		);
	}
}
