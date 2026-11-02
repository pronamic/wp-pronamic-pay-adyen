<?php
/**
 * Integration test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_UnitTestCase;

/**
 * Integration test
 *
 * @version 1.1.2
 * @since   1.0.0
 */
class IntegrationTest extends WP_UnitTestCase {
	/**
	 * Integration.
	 *
	 * @var Integration
	 */
	private $integration;

	/**
	 * Setup.
	 */
	public function set_up(): void {
		parent::set_up();

		$this->integration = new Integration();
	}

	/**
	 * Test filters.
	 *
	 * @link https://github.com/easydigitaldownloads/easy-digital-downloads/blob/2.9.11/tests/tests-filters.php
	 * @link https://github.com/woocommerce/woocommerce/blob/3.5.6/tests/unit-tests/settings/register-wp-admin-settings.php
	 * @link https://developer.wordpress.org/reference/functions/has_filter/
	 */
	public function test_filters() {
		$this->integration->setup();

		$this->assertSame( 10, $this->get_integration_hook_priority( 'init', 'init' ) );
		$this->assertSame( 15, $this->get_integration_hook_priority( 'admin_init', 'admin_init' ) );
	}

	/**
	 * Get the priority of an integration method hooked as first-class callable.
	 *
	 * The integration hooks closures like `$this->init( ... )`, which `has_filter()`
	 * cannot match against an array callable.
	 *
	 * @param string $hook_name Hook name.
	 * @param string $method    Integration method name.
	 * @return int|null
	 */
	private function get_integration_hook_priority( $hook_name, $method ) {
		global $wp_filter;

		if ( ! isset( $wp_filter[ $hook_name ] ) ) {
			return null;
		}

		foreach ( $wp_filter[ $hook_name ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $callback ) {
				if ( ! $callback['function'] instanceof \Closure ) {
					continue;
				}

				$reflection = new \ReflectionFunction( $callback['function'] );

				if ( $reflection->getClosureThis() instanceof Integration && $method === $reflection->getName() ) {
					return $priority;
				}
			}
		}

		return null;
	}

	/**
	 * Test init.
	 */
	public function test_init() {
		$this->integration->init();

		$registered_settings = get_registered_settings();

		$this->assertArrayHasKey( 'pronamic_pay_adyen_notification_authentication_username', $registered_settings );
		$this->assertArrayHasKey( 'pronamic_pay_adyen_notification_authentication_password', $registered_settings );
	}

	/**
	 * Test admin init.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-admin/includes/template.php#L1448-L1504
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-admin/includes/template.php#L1506-L1576
	 */
	public function test_admin_init() {
		global $wp_settings_sections, $wp_settings_fields;

		$this->integration->admin_init();

		$this->assertArrayHasKey( 'pronamic_pay_adyen_notification_authentication', $wp_settings_sections['pronamic_pay'] );

		$fields = $wp_settings_fields['pronamic_pay']['pronamic_pay_adyen_notification_authentication'];

		$this->assertArrayHasKey( 'pronamic_pay_adyen_notification_authentication_username', $fields );
		$this->assertArrayHasKey( 'pronamic_pay_adyen_notification_authentication_password', $fields );
	}

	/**
	 * Test settings section.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-admin/includes/template.php#L1578-L1614
	 */
	public function test_settings_print() {
		$this->integration->admin_init();

		ob_start();

		do_settings_sections( 'pronamic_pay' );

		$output = ob_get_clean();

		$this->assertStringContainsString( '<h2>Adyen Notification Authentication</h2>', $output );

		$this->assertStringContainsString( '<label for="pronamic_pay_adyen_notification_authentication_username">User Name</label>', $output );
		$this->assertStringContainsString( '<input name="pronamic_pay_adyen_notification_authentication_username" id="pronamic_pay_adyen_notification_authentication_username" value="" type="text" class="regular-text" />', $output );

		$this->assertStringContainsString( '<label for="pronamic_pay_adyen_notification_authentication_password">Password</label>', $output );
		$this->assertStringContainsString( '<input name="pronamic_pay_adyen_notification_authentication_password" id="pronamic_pay_adyen_notification_authentication_password" value="" type="text" class="regular-text" />', $output );
	}

	/**
	 * Test settings fields.
	 */
	public function test_settings_fields() {
		$fields = $this->integration->get_settings_fields();

		$this->assertCount( 11, $fields );

		$integration = new Integration( [ 'mode' => 'test' ] );

		$fields = $integration->get_settings_fields();

		$this->assertCount( 9, $fields );
	}
}
