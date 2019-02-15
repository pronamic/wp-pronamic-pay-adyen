<?php
/**
 * Plugin Name: Pronamic Pay Adyen Add-On
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-adyen/
 * Description: Extend the Pronamic Pay plugin with the Adyen gateway to receive payments with Adyen through a variety of WordPress plugins.
 *
 * Version: 2.0.0
 * Requires at least: 4.7
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-pay-adyen
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * GitHub URI: https://github.com/wp-pay-gateways/adyen
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\AddOn;

/**
 * Function to block activation of the plugin.
 */
function block_activation() {
	$message = sprintf(
		/* translators: 1: http://www.wpupdatephp.com/update/, 2: _blank */
		__(
			'The Pronamic Pay Adyen Add-On requires at least PHP 5.3. Read more information about how you can <a href="%1$s" target="%2$s">update your PHP version</a>.',
			'pronamic_ideal'
		),
		esc_attr__( 'http://www.wpupdatephp.com/update/', 'pronamic_ideal' ),
		esc_attr( '_blank' )
	);

	wp_die(
		wp_kses(
			$message,
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
				),
			)
		)
	);
}

/**
 * Deactive Pronamic Pay add-on.
 */
function deactivate_plugin() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	register_activation_hook( __FILE__, __NAMESPACE__ . '\block_activation' );

	add_action( 'admin_init', __NAMESPACE__ . '\deactivate_plugin' );

	return;
}

// Load Pronamic Pay add-on.
require plugin_dir_path( __FILE__ ) . '/vendor/wp-pay/core/src/AddOn.php';

$addon = new AddOn( __FILE__ );

$addon->add_gateways(
	array(
		'Pronamic\WordPress\Pay\Gateways\Adyen\Integration',
	)
);
