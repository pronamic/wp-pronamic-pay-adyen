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
 * Integration
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
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
	public function __construct( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'            => 'adyen',
				'name'          => 'Adyen',
				'mode'          => 'live',
				'provider'      => 'adyen',
				'url'           => \__( 'https://www.adyen.com/', 'pronamic_ideal' ),
				'product_url'   => \__( 'https://www.adyen.com/pricing', 'pronamic_ideal' ),
				'dashboard_url' => 'https://ca-live.adyen.com/ca/ca/login.shtml',
				'manual_url'    => \__( 'https://www.pronamic.eu/manuals/using-adyen-pronamic-pay/', 'pronamic_ideal' ),
				'supports'      => array(
					'webhook',
					'webhook_log',
				),
			)
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

		// Payments controller.
		$payments_controller = new PaymentsController();

		$payments_controller->setup();

		// Payments result controller.
		$payments_result_controller = new PaymentsResultController();

		$payments_result_controller->setup();

		// Site Health controller.
		$site_health_controller = new SiteHealthController();

		$site_health_controller->setup();

		// Settings.
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ), 15 );

		// Actions.
		add_action( 'current_screen', array( $this, 'maybe_download_certificate_or_key' ) );

		$id = $this->get_id();

		if ( null !== $id ) {
			\add_filter( 'pronamic_gateway_configuration_display_value_' . $id, array( $this, 'gateway_configuration_display_value' ), 10, 2 );
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
	 * @return array<int, array<string, callable|int|string|bool|array<int|string,int|string>>>
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

		if ( 'live' === $this->get_mode() ) {
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
		}

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

		// Merchant Order Reference.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => array(
				'filter' => \FILTER_SANITIZE_STRING,
				'flags'  => \FILTER_FLAG_NO_ENCODE_QUOTES,
			),
			'meta_key'    => '_pronamic_gateway_adyen_merchant_order_reference',
			'title'       => __( 'Merchant Order Reference', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
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
		);

		// Apple Pay - Merchant identifier.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => \FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_apple_pay_merchant_id',
			'title'       => _x( 'Apple Pay Merchant ID', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'Your Apple Pay Merchant ID. Required for accepting live payments.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a><br /><a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/payment-methods/apple-pay/web-drop-in#before-you-begin' ),
				esc_html__( 'Adyen documentation: "Apple Pay Drop-in - Before you begin".', 'pronamic_ideal' ),
				esc_url( 'https://developer.apple.com/documentation/apple_pay_on_the_web/configuring_your_environment' ),
				esc_html__( 'Apple documentation: "Configuring your environment".', 'pronamic_ideal' )
			),
		);

		// Apple Pay - Merchant Identity PKCS#12.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => \FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_apple_pay_merchant_id_certificate',
			'title'       => __( 'Apple Pay Merchant Identity Certificate', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'callback'    => array( $this, 'field_certificate' ),
			'classes'     => array( 'code' ),
			'tooltip'     => __( 'The Apple Pay Merchant Identity certificate required for secure communication with Apple.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/payment-methods/apple-pay/enable-apple-pay#create-merchant-identity-certificate' ),
				esc_html__( 'Adyen documentation: "Enable Apple Pay - Create a merchant identity certificate".', 'pronamic_ideal' )
			),
		);

		// Apple Pay - Merchant Identity private key.
		$fields[] = array(
			'section'  => 'advanced',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_adyen_apple_pay_merchant_id_private_key',
			'title'    => __( 'Apple Pay Merchant Identity Private Key', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'callback' => array( $this, 'field_private_key' ),
			'classes'  => array( 'code' ),
			'tooltip'  => __( 'The private key of the Apple Pay Merchant Identity certificate for secure communication with Apple.', 'pronamic_ideal' ),
		);

		// Apple Pay - Merchant Identity certificate private key password.
		$fields[] = array(
			'section'  => 'advanced',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_adyen_apple_pay_merchant_id_private_key_password',
			'title'    => _x( 'Apple Pay Merchant Identity Private Key Password', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'Your Apple Pay Merchant Identity Certificate private key password.', 'pronamic_ideal' ),
		);

		// Google Pay - Merchant identifier.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => \FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_adyen_google_pay_merchant_identifier',
			'title'       => _x( 'Google Pay Merchant ID', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'Your Google Merchant ID. Required for accepting live payments.', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a><br /><a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/payment-methods/google-pay/web-drop-in#test-and-go-live' ),
				esc_html__( 'Adyen documentation: "Google Pay Drop-in - Test and go live".', 'pronamic_ideal' ),
				esc_url( 'https://developers.google.com/pay/api/web/guides/test-and-deploy/deploy-production-environment' ),
				esc_html__( 'Google documentation: "Deploy production environment".', 'pronamic_ideal' )
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
				/* translators: %s: payment provider name */
				__( 'Copy the Webhook URL to the %s dashboard to receive automatic transaction status updates.', 'pronamic_ideal' ),
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
			'html'    => \sprintf(
				/* translators: %s: Pronamic Pay settings page URL. */
				__( 'Go to the <a href="%s">Pronamic Pay settings page</a> for webhook authentication settings.', 'pronamic_ideal' ),
				\esc_url(
					\add_query_arg(
						array(
							'page' => 'pronamic_pay_settings',
						),
						\admin_url( 'admin.php' )
					)
				)
			),
		);

		// Return fields.
		return $fields;
	}

	/**
	 * Field certificate.
	 *
	 * @param array<string> $field Field.
	 * @return void
	 */
	public function field_certificate( $field ) {
		if ( ! \array_key_exists( 'meta_key', $field ) ) {
			return;
		}

		$post_id = \get_the_ID();

		if ( false === $post_id ) {
			return;
		}

		$certificate = \get_post_meta( $post_id, $field['meta_key'], true );

		if ( ! empty( $certificate ) ) {
			$fingerprint = Security::get_sha_fingerprint( $certificate );

			echo '<dl>';

			if ( null !== $fingerprint ) {
				$fingerprint = \str_split( $fingerprint, 2 );
				$fingerprint = \implode( ':', $fingerprint );

				echo '<dt>', \esc_html__( 'SHA Fingerprint', 'pronamic_ideal' ), '</dt>';
				echo '<dd>', \esc_html( $fingerprint ), '</dd>';
			}

			$info = \openssl_x509_parse( $certificate );

			if ( $info ) {
				$date_format = __( 'M j, Y @ G:i', 'pronamic_ideal' );

				if ( isset( $info['validFrom_time_t'] ) ) {
					echo '<dt>', \esc_html__( 'Valid From', 'pronamic_ideal' ), '</dt>';
					echo '<dd>', \esc_html( \date_i18n( $date_format, $info['validFrom_time_t'] ) ), '</dd>';
				}

				if ( isset( $info['validTo_time_t'] ) ) {
					echo '<dt>', \esc_html__( 'Valid To', 'pronamic_ideal' ), '</dt>';
					echo '<dd>', \esc_html( \date_i18n( $date_format, $info['validTo_time_t'] ) ), '</dd>';
				}
			}

			echo '</dl>';
		} elseif ( false !== \strpos( $field['meta_key'], 'apple_pay' ) ) {
			\printf(
				'<p class="pronamic-pay-description description">%s</p><p>&nbsp;</p>',
				\esc_html__( 'Upload an Apple Pay Merchant Identity certificate, which can be exported from Keychain Access on Mac as a PKCS#12 (*.p12) file.', 'pronamic_ideal' )
			);
		}

		?>
		<p>
			<?php

			if ( ! empty( $certificate ) ) {
				\submit_button(
					__( 'Download', 'pronamic_ideal' ),
					'secondary',
					'download' . $field['meta_key'],
					false
				);

				echo ' ';
			}

			\printf(
				'<label class="pronamic-pay-form-control-file-button button">%s <input type="file" name="%s" /></label>',
				\esc_html__( 'Upload', 'pronamic_ideal' ),
				\esc_attr( $field['meta_key'] . '_file' )
			);

			?>
		</p>
		<?php
	}

	/**
	 * Field private key.
	 *
	 * @param array<string> $field Field.
	 * @return void
	 */
	public function field_private_key( $field ) {
		if ( ! \array_key_exists( 'meta_key', $field ) ) {
			return;
		}

		$post_id = \get_the_ID();

		if ( false === $post_id ) {
			return;
		}

		$private_key = \get_post_meta( $post_id, $field['meta_key'], true );

		?>
		<p>
			<?php

			if ( ! empty( $private_key ) ) {
				\submit_button(
					__( 'Download', 'pronamic_ideal' ),
					'secondary',
					'download' . $field['meta_key'],
					false
				);

				echo ' ';
			}

			if ( empty( $private_key ) && false !== \strpos( $field['meta_key'], 'apple_pay' ) ) {
				\printf(
					'<p class="pronamic-pay-description description">%s</p><p>&nbsp;</p>',
					\esc_html__( 'Leave empty to auto fill when uploading an Apple Pay Merchant Identity PKCS#12 certificate file.', 'pronamic_ideal' )
				);
			}

			\printf(
				'<label class="pronamic-pay-form-control-file-button button">%s <input type="file" name="%s" /></label>',
				\esc_html__( 'Upload', 'pronamic_ideal' ),
				\esc_attr( $field['meta_key'] . '_file' )
			);

			?>
		</p>
		<?php
	}

	/**
	 * Download certificate or key in Privacy Enhanced Mail (PEM) format.
	 *
	 * @return void
	 */
	public function maybe_download_certificate_or_key() {
		// Certificate fields and download filename.
		$fields = array(
			'_pronamic_gateway_adyen_apple_pay_merchant_id_certificate' => 'apple-pay-merchant-identity-certificate-%s.pem',
			'_pronamic_gateway_adyen_apple_pay_merchant_id_private_key' => 'apple-pay-merchant-identity-private-key-%s.pem',
		);

		// Check download actions.
		$is_download_action = false;

		foreach ( $fields as $meta_key => $filename ) {
			if ( \filter_has_var( \INPUT_POST, 'download' . $meta_key ) ) {
				$is_download_action = true;

				break;
			}
		}

		// No valid download action found.
		if ( false === $is_download_action ) {
			return;
		}

		$post_id = filter_input( \INPUT_POST, 'post_ID', \FILTER_SANITIZE_STRING );

		$filename = sprintf( $filename, $post_id );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: application/x-pem-file; charset=' . get_option( 'blog_charset' ), true );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo get_post_meta( $post_id, $meta_key, true );

		exit;
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
	 * Save post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		// Files.
		$files = array(
			'_pronamic_gateway_adyen_apple_pay_merchant_id_certificate_file' => '_pronamic_gateway_adyen_apple_pay_merchant_id_certificate',
			'_pronamic_gateway_adyen_apple_pay_merchant_id_private_key_file' => '_pronamic_gateway_adyen_apple_pay_merchant_id_private_key',
		);

		foreach ( $files as $name => $meta_key ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( isset( $_FILES[ $name ] ) && \UPLOAD_ERR_OK === $_FILES[ $name ]['error'] ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$value = file_get_contents( $_FILES[ $name ]['tmp_name'], true );

				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		// Update Apple Pay Merchant Identity certificate and private key from uploaded PKCS#12 file.
		$apple_pay_merchant_id_pkcs12 = get_post_meta( $post_id, '_pronamic_gateway_adyen_apple_pay_merchant_id_certificate', true );

		if ( ! empty( $apple_pay_merchant_id_pkcs12 ) ) {
			// Try to read file without using password.
			$pkcs12_read = \openssl_pkcs12_read( $apple_pay_merchant_id_pkcs12, $certs, '' );

			$password = \get_post_meta( $post_id, '_pronamic_gateway_adyen_apple_pay_merchant_id_private_key_password', true );

			// Try to read file with private key password.
			if ( false === $pkcs12_read ) {
				$pkcs12_read = \openssl_pkcs12_read( $apple_pay_merchant_id_pkcs12, $certs, $password );
			}

			if ( true === $pkcs12_read ) {
				if ( isset( $certs['cert'] ) ) {
					\update_post_meta( $post_id, '_pronamic_gateway_adyen_apple_pay_merchant_id_certificate', $certs['cert'] );
				}

				if ( isset( $certs['pkey'] ) ) {
					$private_key = $certs['pkey'];

					$cipher = null;

					// Try to export the private key encrypted.
					if ( defined( 'OPENSSL_CIPHER_AES_128_CBC' ) ) {
						$cipher = \OPENSSL_CIPHER_AES_128_CBC;
					} elseif ( defined( 'OPENSSL_CIPHER_3DES' ) ) {
						$cipher = \OPENSSL_CIPHER_3DES;
					}

					if ( null !== $cipher && '' !== $password ) {
						$args = array(
							'digest_alg'             => 'SHA256',
							'private_key_bits'       => 2048,
							'private_key_type'       => \OPENSSL_KEYTYPE_RSA,
							'encrypt_key'            => true,
							'encrypt_key_cipher'     => $cipher,
							'subjectKeyIdentifier'   => 'hash',
							'authorityKeyIdentifier' => 'keyid:always,issuer:always',
							'basicConstraints'       => 'CA:true',
						);

						\openssl_pkey_export( $certs['pkey'], $private_key, $password, $args );
					}

					\update_post_meta( $post_id, '_pronamic_gateway_adyen_apple_pay_merchant_id_private_key', $private_key );
				}
			}
		}
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

		$config->api_key                                    = $this->get_meta( $post_id, 'adyen_api_key' );
		$config->api_live_url_prefix                        = $this->get_meta( $post_id, 'adyen_api_live_url_prefix' );
		$config->merchant_account                           = $this->get_meta( $post_id, 'adyen_merchant_account' );
		$config->origin_key                                 = $this->get_meta( $post_id, 'adyen_origin_key' );
		$config->merchant_order_reference                   = $this->get_meta( $post_id, 'adyen_merchant_order_reference' );
		$config->apple_pay_merchant_id                      = $this->get_meta( $post_id, 'adyen_apple_pay_merchant_id' );
		$config->apple_pay_merchant_id_certificate          = $this->get_meta( $post_id, 'adyen_apple_pay_merchant_id_certificate' );
		$config->apple_pay_merchant_id_private_key          = $this->get_meta( $post_id, 'adyen_apple_pay_merchant_id_private_key' );
		$config->apple_pay_merchant_id_private_key_password = $this->get_meta( $post_id, 'adyen_apple_pay_merchant_id_private_key_password' );
		$config->google_pay_merchant_identifier             = $this->get_meta( $post_id, 'adyen_google_pay_merchant_identifier' );

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
			$gateway = new WebSdkGateway( $config );

			$gateway->set_mode( $this->get_mode() );

			return $gateway;
		}

		$gateway = new DropInGateway( $config );

		$gateway->set_mode( $this->get_mode() );

		return $gateway;
	}
}
