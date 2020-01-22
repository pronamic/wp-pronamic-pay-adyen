<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
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
 * @version 1.0.5
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
		$this->url           = __( 'https://www.adyen.com/', 'pronamic_ideal' );
		$this->product_url   = __( 'https://www.adyen.com/pricing', 'pronamic_ideal' );
		$this->dashboard_url = array(
			__( 'test', 'pronamic_ideal' ) => 'https://ca-test.adyen.com/ca/ca/login.shtml',
			__( 'live', 'pronamic_ideal' ) => 'https://ca-live.adyen.com/ca/ca/login.shtml',
		);
		$this->supports      = array(
			'webhook',
			'webhook_log',
		);

		$this->set_manual_url( __( 'https://www.pronamic.eu/manuals/using-adyen-pronamic-pay/', 'pronamic_ideal' ) );

		// Notifications controller.
		$notifications_controller = new NotificationsController();

		$notifications_controller->setup();

		// Payments controller.
		$payments_controller = new PaymentsController();

		$payments_controller->setup();

		// Payments result controller.
		$payments_result_controller = new PaymentsResultController();

		$payments_result_controller->setup();

		// Site Health controller.
		$site_healht_controller = new SiteHealthController();

		$site_healht_controller->setup();

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
			/* translators: Translate 'notification' the same as in the Adyen dashboard. */
			_x( 'Adyen Notification Authentication', 'Adyen', 'pronamic_ideal' ),
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
	 * @param array<string,string> $args Arguments.
	 * @return void
	 */
	public static function input_element( $args ) {
		$name = $args['label_for'];

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
	 * Get settings fields.
	 *
	 * @return array<int, array<string, int|string|bool|array<int,string>>>
	 */
	public function get_settings_fields() {
		$fields = array();

		// Merchant Account.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_adyen_merchant_account',
			'title'    => _x( 'Merchant Account', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'The merchant account identifier, with which you want to process the transaction.', 'pronamic_ideal' ),
		);

		// API Key.
		$fields[] = array(
			'section'     => 'general',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_api_key',
			'title'       => _x( 'API Key', 'adyen', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'classes'     => array( 'code' ),
			'tooltip'     => __( 'API key as mentioned in the payment provider dashboard.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/developers/user-management/how-to-get-the-api-key' ),
				esc_html__( 'Adyen documentation: "How to get the API key".', 'pronamic_ideal' )
			),
		);

		// Live API URL prefix.
		$fields[] = array(
			'section'     => 'general',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_api_live_url_prefix',
			'title'       => _x( 'API Live URL Prefix', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'The unique prefix for the live API URL, as mentioned at <strong>Account » API URLs</strong> in the Adyen dashboard.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/developers/development-resources/live-endpoints#liveurlprefix' ),
				esc_html__( 'Adyen documentation: "Live URL prefix".', 'pronamic_ideal' )
			),
		);

		// Origin Key.
		$fields[] = array(
			'section'     => 'general',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_origin_key',
			'title'       => _x( 'Origin Key', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array(
				'regular-text',
				'code',
				'pronamic-pay-form-control-lg',
			),
			'tooltip'     => __( 'An origin key is a client-side key that is used to validate Adyen\'s JavaScript component library. It is required for the Drop-in and Component integrations.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/user-management/how-to-get-an-origin-key' ),
				esc_html__( 'Adyen documentation: "How to get an origin key".', 'pronamic_ideal' )
			),
		);

		// Webhook URL.
		$fields[] = array(
			'section'  => 'feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => rest_url( self::REST_ROUTE_NAMESPACE . '/notifications' ),
			'readonly' => true,
			'tooltip'  => sprintf(
				/* translators: %s: Adyen */
				__(
					'Copy the Webhook URL to the %s dashboard to receive automatic transaction status updates.',
					'pronamic_ideal'
				),
				__( 'Adyen', 'pronamic_ideal' )
			),
		);

		/**
		 * SSL Version.
		 *
		 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications#step3configurenotificationsinthecustomerarea
		 * @link https://www.howsmyssl.com/a/check
		 */
		$fields[] = array(
			'section' => 'feedback',
			'title'   => __( 'SSL Version', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => __( 'Choose the SSL Version of your server on the Adyen Customer Area.', 'pronamic_ideal' ),
		);

		/**
		 * Method.
		 *
		 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications#step3configurenotificationsinthecustomerarea
		 * @link https://www.howsmyssl.com/a/check
		 */
		$fields[] = array(
			'section' => 'feedback',
			'title'   => _x( 'Method', 'adyen notification', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => __( 'JSON', 'pronamic_ideal' ),
		);

		// Webhook authentication settings.
		$fields[] = array(
			'section' => 'feedback',
			'title'   => __( 'Authentication', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => sprintf(
				'For webhook authentication settings, please visit <a href="%2$s" title="Settings">%1$s settings</a>.',
				__( 'Pronamic Pay', 'pronamic_ideal' ),
				add_query_arg(
					array(
						'page' => 'pronamic_pay_settings',
					),
					admin_url( 'admin.php' )
				)
			),
		);

		// Return fields.
		return $fields;
	}

	/**
	 * Get configuration by post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->mode                = $this->get_meta( $post_id, 'mode' );
		$config->api_key             = $this->get_meta( $post_id, 'adyen_api_key' );
		$config->api_live_url_prefix = $this->get_meta( $post_id, 'adyen_api_live_url_prefix' );
		$config->merchant_account    = $this->get_meta( $post_id, 'adyen_merchant_account' );
		$config->origin_key          = $this->get_meta( $post_id, 'adyen_origin_key' );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return AbstractGateway
	 */
	public function get_gateway( $post_id ) {
		$config = $this->get_config( $post_id );

		if ( empty( $config->origin_key ) ) {
			return new WebSdkGateway( $config );
		}

		return new DropInGateway( $config );
	}
}
