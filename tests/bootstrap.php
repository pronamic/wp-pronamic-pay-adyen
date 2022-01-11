<?php
/**
 * Bootstrap tests
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

putenv( 'WP_PHPUNIT__TESTS_CONFIG=tests/wp-config.php' );

require_once __DIR__ . '/../vendor/autoload.php';

require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

/**
 * Manually load plugin.
 */
function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../pronamic-pay-adyen.php';

	global $pronamic_ideal;

	$pronamic_ideal = pronamic_pay_plugin();
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Bootstrap.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
