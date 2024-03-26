<?php
/**
 * Plugin Name: Pronamic Pay Adyen Add-On
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-adyen/
 * Description: Extend the Pronamic Pay plugin with the Adyen gateway to receive payments with Adyen through a variety of WordPress plugins.
 *
 * Version: 4.5.1
 * Requires at least: 5.9
 * Requires PHP: 7.4
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-pay-adyen
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * Requires Plugins: pronamic-ideal
 * Depends: wp-pay/core
 *
 * GitHub URI: https://github.com/wp-pay-gateways/adyen
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Gateway.
 */
add_filter(
	'pronamic_pay_gateways',
	function ( $gateways ) {
		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\Adyen\Integration(
			[
				'id'            => 'adyen',
				'name'          => 'Adyen',
				'mode'          => 'live',
				'dashboard_url' => 'https://ca-live.adyen.com/ca/ca/login.shtml',
			]
		);

		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\Adyen\Integration(
			[
				'id'            => 'adyen-test',
				'name'          => 'Adyen - Test',
				'mode'          => 'test',
				'dashboard_url' => 'https://ca-test.adyen.com/ca/ca/login.shtml',
			]
		);

		return $gateways;
	}
);
