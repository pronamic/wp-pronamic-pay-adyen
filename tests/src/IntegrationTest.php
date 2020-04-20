<?php
/**
 * Integration test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_UnitTestCase;

/**
 * Integration test
 *
 * @author  Remco Tolsma
 * @version 1.1.1
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
	public function setUp() {
		parent::setUp();

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

		$this->assertEquals( has_filter( 'init', array( $this->integration, 'init' ) ), 10 );
		$this->assertEquals( has_filter( 'admin_init', array( $this->integration, 'admin_init' ) ), 10 );
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

		$this->assertArraySubset(
			array(
				'pronamic_pay' => array(
					'pronamic_pay_adyen_notification_authentication' => array(
						'id' => 'pronamic_pay_adyen_notification_authentication',
					),
				),
			),
			$wp_settings_sections
		);

		$this->assertArraySubset(
			array(
				'pronamic_pay' => array(
					'pronamic_pay_adyen_notification_authentication' => array(
						'pronamic_pay_adyen_notification_authentication_username' => array(
							'id' => 'pronamic_pay_adyen_notification_authentication_username',
						),
						'pronamic_pay_adyen_notification_authentication_password' => array(
							'id' => 'pronamic_pay_adyen_notification_authentication_password',
						),
					),
				),
			),
			$wp_settings_fields
		);
	}

	/**
	 * Test settings section.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-admin/includes/template.php#L1578-L1614
	 */
	public function test_settings_print() {
		ob_start();

		do_settings_sections( 'pronamic_pay' );

		$output = ob_get_clean();

		$this->assertContains( '<h2>Adyen Notification Authentication</h2>', $output );

		$this->assertContains( '<label for="pronamic_pay_adyen_notification_authentication_username">User Name</label>', $output );
		$this->assertContains( '<input name="pronamic_pay_adyen_notification_authentication_username" id="pronamic_pay_adyen_notification_authentication_username" value="" type="text" class="regular-text" />', $output );

		$this->assertContains( '<label for="pronamic_pay_adyen_notification_authentication_password">Password</label>', $output );
		$this->assertContains( '<input name="pronamic_pay_adyen_notification_authentication_password" id="pronamic_pay_adyen_notification_authentication_password" value="" type="text" class="regular-text" />', $output );
	}

	/**
	 * Test settings fields.
	 */
	public function test_settings_fields() {
		$fields = $this->integration->get_settings_fields();

		$this->assertCount( 14, $fields );
	}
}
