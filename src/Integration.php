<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Dependencies\PhpExtensionDependency;
use Pronamic\WordPress\Pay\AbstractGatewayIntegration;
use Pronamic\WordPress\Pay\Util as Pay_Util;

/**
 * Integration class
 */
class Integration extends AbstractGatewayIntegration {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/adyen/v1';

	/**
	 * Construct Adyen integration.
	 *
	 * @param array<string, array<string>> $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'            => 'adyen',
				'name'          => 'Adyen',
				'mode'          => 'live',
				'provider'      => 'adyen',
				'url'           => \__( 'https://www.adyen.com/', 'pronamic_ideal' ),
				'product_url'   => \__( 'https://www.adyen.com/pricing', 'pronamic_ideal' ),
				'dashboard_url' => 'https://ca-live.adyen.com/ca/ca/login.shtml',
				'manual_url'    => \__( 'https://www.pronamic.eu/manuals/using-adyen-pronamic-pay/', 'pronamic_ideal' ),
				'supports'      => [
					'webhook',
					'webhook_log',
				],
			]
		);

		parent::__construct( $args );
	}

	/**
	 * Setup gateway integration.
	 *
	 * @return void
	 */
	public function setup() {
		// Check if dependencies are met and integration is active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Notifications controller.
		$notifications_controller = new NotificationsController();

		$notifications_controller->setup();

		// Site Health controller.
		$site_health_controller = new SiteHealthController();

		$site_health_controller->setup();

		// Return controller.
		$return_controller = new ReturnController();

		$return_controller->setup();

		// Settings.
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ], 15 );

		$id = $this->get_id();

		if ( null !== $id ) {
			\add_filter( 'pronamic_gateway_configuration_display_value_' . $id, [ $this, 'gateway_configuration_display_value' ], 10, 2 );
		}
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
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			]
		);

		/*
		 * Authentication - Password
		 */
		register_setting(
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication_password',
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			]
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
			[ $this, 'settings_section_notification_authentication' ],
			'pronamic_pay'
		);

		add_settings_field(
			'pronamic_pay_adyen_notification_authentication_username',
			__( 'User Name', 'pronamic_ideal' ),
			[ __CLASS__, 'input_element' ],
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication',
			[
				'label_for' => 'pronamic_pay_adyen_notification_authentication_username',
			]
		);

		add_settings_field(
			'pronamic_pay_adyen_notification_authentication_password',
			__( 'Password', 'pronamic_ideal' ),
			[ __CLASS__, 'input_element' ],
			'pronamic_pay',
			'pronamic_pay_adyen_notification_authentication',
			[
				'label_for' => 'pronamic_pay_adyen_notification_authentication_password',
			]
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
	 * @return array<int, array<string, callable|int|string|bool|array<int|string,int|string>>>
	 */
	public function get_settings_fields() {
		$fields = [];

		// Merchant Account.
		$fields[] = [
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_adyen_merchant_account',
			'title'    => _x( 'Merchant Account', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'tooltip'  => __( 'The merchant account identifier, with which you want to process the transaction.', 'pronamic_ideal' ),
		];

		// API Key.
		$fields[] = [
			'section'     => 'general',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_api_key',
			'title'       => _x( 'API Key', 'adyen', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'classes'     => [ 'code' ],
			'tooltip'     => __( 'API key as mentioned in the payment provider dashboard.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/development-resources/api-credentials' ),
				esc_html__( 'Adyen documentation: "API credentials".', 'pronamic_ideal' )
			),
		];

		if ( 'live' === $this->get_mode() ) {
			// Live API URL prefix.
			$fields[] = [
				'section'     => 'general',
				'filter'      => FILTER_SANITIZE_STRING,
				'meta_key'    => '_pronamic_gateway_adyen_api_live_url_prefix',
				'title'       => _x( 'API Live URL Prefix', 'adyen', 'pronamic_ideal' ),
				'type'        => 'text',
				'classes'     => [ 'regular-text', 'code' ],
				'tooltip'     => __( 'The unique prefix for the live API URL, as mentioned at <strong>Account » API URLs</strong> in the Adyen dashboard.', 'pronamic_ideal' ),
				'description' => sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( 'https://docs.adyen.com/developers/development-resources/live-endpoints#liveurlprefix' ),
					esc_html__( 'Adyen documentation: "Live URL prefix".', 'pronamic_ideal' )
				),
			];
		}

		// Client Key.
		$fields[] = [
			'section'     => 'general',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_client_key',
			'title'       => _x( 'Client Key', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => [
				'regular-text',
				'code',
				'pronamic-pay-form-control-lg',
			],
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/development-resources/client-side-authentication#get-your-client-key' ),
				esc_html__( 'Adyen documentation: "Get your client key".', 'pronamic_ideal' )
			),
		];

		// Merchant Order Reference.
		$fields[] = [
			'section'     => 'advanced',
			'filter'      => [
				'filter' => \FILTER_SANITIZE_STRING,
				'flags'  => \FILTER_FLAG_NO_ENCODE_QUOTES,
			],
			'meta_key'    => '_pronamic_gateway_adyen_merchant_order_reference',
			'title'       => __( 'Merchant Order Reference', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => [ 'regular-text', 'code' ],
			'tooltip'     => \sprintf(
				/* translators: %s: <code>parameterName</code> */
				\__( 'The Adyen %s parameter.', 'pronamic_ideal' ),
				\sprintf( '<code>%s</code>', 'merchantOrderReference' )
			),
			'description' => \sprintf(
				'%s %s<br />%s',
				\__( 'Available tags:', 'pronamic_ideal' ),
				\sprintf(
					'<code>%s</code> <code>%s</code>',
					'{order_id}',
					'{payment_id}'
				),
				\sprintf(
					/* translators: %s: default code */
					\__( 'Default: <code>%s</code>', 'pronamic_ideal' ),
					'{payment_id}'
				)
			),
		];

		// Webhook URL.
		$fields[] = [
			'section'  => 'feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'large-text', 'code' ],
			'value'    => rest_url( self::REST_ROUTE_NAMESPACE . '/notifications' ),
			'readonly' => true,
			'tooltip'  => sprintf(
				/* translators: %s: payment provider name */
				__( 'Copy the Webhook URL to the %s dashboard to receive automatic transaction status updates.', 'pronamic_ideal' ),
				__( 'Adyen', 'pronamic_ideal' )
			),
		];

		/**
		 * SSL Version.
		 *
		 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications#step3configurenotificationsinthecustomerarea
		 * @link https://www.howsmyssl.com/a/check
		 */
		$fields[] = [
			'section' => 'feedback',
			'title'   => __( 'SSL Version', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => __( 'Choose the SSL Version of your server on the Adyen Customer Area.', 'pronamic_ideal' ),
		];

		/**
		 * Method.
		 *
		 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications#step3configurenotificationsinthecustomerarea
		 * @link https://www.howsmyssl.com/a/check
		 */
		$fields[] = [
			'section' => 'feedback',
			'title'   => _x( 'Method', 'adyen notification', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => __( 'JSON', 'pronamic_ideal' ),
		];

		// Webhook authentication settings.
		$fields[] = [
			'section' => 'feedback',
			'title'   => __( 'Authentication', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => \sprintf(
				/* translators: %s: Pronamic Pay settings page URL. */
				__( 'Go to the <a href="%s">Pronamic Pay settings page</a> for webhook authentication settings.', 'pronamic_ideal' ),
				\esc_url(
					\add_query_arg(
						[
							'page' => 'pronamic_pay_settings',
						],
						\admin_url( 'admin.php' )
					)
				)
			),
		];

		// Return fields.
		return $fields;
	}

	/**
	 * Gateway configuration display value.
	 *
	 * @param string $display_value Display value.
	 * @param int    $post_id       Gateway configuration post ID.
	 * @return string
	 */
	public function gateway_configuration_display_value( $display_value, $post_id ) {
		$config = $this->get_config( $post_id );

		return $config->get_merchant_account();
	}

	/**
	 * Get configuration by post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->mode = $this->get_mode();

		$config->api_key                  = $this->get_meta( $post_id, 'adyen_api_key' );
		$config->api_live_url_prefix      = $this->get_meta( $post_id, 'adyen_api_live_url_prefix' );
		$config->merchant_account         = $this->get_meta( $post_id, 'adyen_merchant_account' );
		$config->client_key               = $this->get_meta( $post_id, 'adyen_client_key' );
		$config->merchant_order_reference = $this->get_meta( $post_id, 'adyen_merchant_order_reference' );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		$config = $this->get_config( $post_id );

		$gateway = new Gateway( $config );

		$gateway->set_mode( $this->get_mode() );

		return $gateway;
	}
}
