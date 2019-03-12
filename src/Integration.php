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
use Pronamic\WordPress\Pay\Util as Pay_Util;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Integration extends AbstractIntegration {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/adyen/v1';

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

		// Notifications controller.
		$notifications_controller = new NotificationsController();

		$notifications_controller->setup();

		// Payments result controller.
		$payments_result_controller = new PaymentsResultController();

		$payments_result_controller->setup();

		// Settings.
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public function init() {
		/*
		 * Authentication - User Name
		 */
		register_setting(
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication_username',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		/*
		 * Authentication - Password
		 */
		register_setting(
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication_password',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Admin initialize.
	 *
	 * @return void
	 */
	public function admin_init() {
		add_settings_section(
			'pronamic_pay_adyen_notification_authentication',
			__( 'Adyen Notification Authentication', 'pronamic_ideal' ),
			array( $this, 'settings_section_notification_authentication' ),
			'pronamic_pay'
		);

		add_settings_field(
			'pronamic_pay_adyen_notification_authentication_username',
			__( 'User Name', 'pronamic_ideal' ),
			array( __CLASS__, 'input_element' ),
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication',
			array(
				'label_for' => 'pronamic_pay_adyen_notification_authentication_username',
			)
		);

		add_settings_field(
			'pronamic_pay_adyen_notification_authentication_password',
			__( 'Password', 'pronamic_ideal' ),
			array( __CLASS__, 'input_element' ),
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication',
			array(
				'label_for' => 'pronamic_pay_adyen_notification_authentication_password',
			)
		);
	}

	/**
	 * Settings section notification authentication.
	 *
	 * @return void
	 */
	public function settings_section_notification_authentication() {
		printf(
			'<p>%s</p>',
			esc_html__(
				'Set the user name and password below and in the webhook authentication settings in the Adyen dashboard for increased security (recommended).',
				'pronamic_ideal'
			)
		);
	}

	/**
	 * Input text.
	 *
	 * @param array $args Arguments.
	 * @return void
	 */
	public static function input_element( $args ) {
		$name  = $args['label_for'];

		$value = get_option( $name );
		$value = strval( $value );

		printf(
			'<input name="%s" id="%s" value="%s" type="text" class="regular-text" />',
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $value )
		);
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
