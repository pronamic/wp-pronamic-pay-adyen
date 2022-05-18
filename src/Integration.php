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

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;
use WP_Query;

/**
 * Integration class
 */
class Integration extends AbstractGatewayIntegration {
	/**
	 * Flag for singles.
	 *
	 * @var bool
	 */
	private static $singles = false;

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

		$id = $this->get_id();

		if ( null !== $id ) {
			\add_filter( 'pronamic_gateway_configuration_display_value_' . $id, [ $this, 'gateway_configuration_display_value' ], 10, 2 );
		}

		if ( false === self::$singles ) {
			$this->setup_singles();

			self::$singles = true;
		}
	}

	/**
	 * Setup singles.
	 *
	 * @return void
	 */
	private function setup_singles() {
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

		/**
		 * Backward compatibility.
		 *
		 * @link https://github.com/pronamic/wp-pronamic-pay-adyen/issues/10
		 */
		\add_action( 'admin_notices', [ $this, 'maybe_display_migrate_client_key_admin_notice' ] );
		\add_action( 'save_post_pronamic_gateway', [ $this, 'delete_migrate_client_key_query_transient' ] );
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
			'required' => true,
		];

		// API Key.
		$fields[] = [
			'section'     => 'general',
			'filter'      => \FILTER_UNSAFE_RAW,
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
			'required'    => true,
		];

		if ( 'live' === $this->get_mode() ) {
			// Environment.
			$fields[] = [
				'section'  => 'general',
				'filter'   => \FILTER_SANITIZE_STRING,
				'meta_key' => '_pronamic_gateway_adyen_environment',
				'title'    => \_x( 'Environment', 'adyen', 'pronamic_ideal' ),
				'type'     => 'select',
				'options'  => [
					[
						'options' => [
							'live'      => \__( 'Live - Europe', 'pronamic_ideal' ),
							'live-apse' => \__( 'Live - Asia Pacific South East', 'pronamic_ideal' ),
							'live-au'   => \__( 'Live - Australia', 'pronamic_ideal' ),
							'live-us'   => \__( 'Live - United States', 'pronamic_ideal' ),
						],
					],
				],
				'required' => true,
			];

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
				'required'    => true,
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
			'required'    => true,
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

		$config->environment = ( 'test' === $this->get_mode() ) ? 'test' : $this->get_meta( $post_id, 'adyen_environment' );

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

	/**
	 * Maybe display migrate client key admin notice.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-adyen/issues/10
	 * @link https://developer.wordpress.org/apis/handbook/transients/
	 * @return void
	 */
	public function maybe_display_migrate_client_key_admin_notice() {
		if ( ! \current_user_can( 'manage_options' ) ) {
			return;
		}

		$query = \get_transient( 'pronamic_pay_adyen_migrate_client_key_query' );

		if ( false === $query ) {
			$query = new WP_Query(
				[
					'post_type'      => 'pronamic_gateway',
					'posts_per_page' => 10,
					'meta_query'     => [
						'relation' => 'AND',
						[
							'key'     => '_pronamic_gateway_id',
							'compare' => 'IN',
							'value'   => [
								'adyen',
								'adyen-test',
							],
						],
						[
							'relation' => 'OR',
							[
								'key'     => '_pronamic_gateway_adyen_client_key',
								'compare' => '=',
								'value'   => '',
							],
							[
								'key'     => '_pronamic_gateway_adyen_client_key',
								'compare' => 'NOT EXISTS',
							],
						],
					],
					'no_found_rows'  => true,
				]
			);

			\set_transient( 'pronamic_pay_adyen_migrate_client_key_query', $query, WEEK_IN_SECONDS );
		}

		if ( empty( $query->posts ) ) {
			return;
		}

		?>
		<div class="error notice">
			<p>
				<strong><?php \esc_html_e( 'Pronamic Pay', 'pronamic_ideal' ); ?></strong> —
				<?php \esc_html_e( 'The following Ayden configurations must be migrated to a client key:', 'pronamic_ideal' ); ?>
			</p>

			<ul>

				<?php foreach ( $query->posts as $adyen_config_post ) : ?>

					<li>
						<?php

						\printf(
							'<a href="%s">%s</a>',
							\esc_url( \get_edit_post_link( $adyen_config_post ) ),
							\esc_html( \get_the_title( $adyen_config_post ) )
						);

						?>
					</li>

				<?php endforeach; ?>

			</ul>
		</div>
		<?php
	}

	/**
	 * Delete the migrate client key query transient.
	 *
	 * @return void
	 */
	public function delete_migrate_client_key_query_transient() {
		\delete_transient( 'pronamic_pay_adyen_migrate_client_key_query' );
	}
}
