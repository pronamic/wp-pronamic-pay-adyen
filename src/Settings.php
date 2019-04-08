<?php
/**
 * Settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\GatewaySettings;
use Pronamic\WordPress\Pay\WebhookManager;

/**
 * Settings
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Settings extends GatewaySettings {
	/**
	 * Constructs and initialize settings.
	 */
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	/**
	 * Sections.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public function sections( array $sections ) {
		$sections['adyen'] = array(
			'title'   => __( 'Adyen', 'pronamic_ideal' ),
			'methods' => array( 'adyen' ),
		);

		// Transaction feedback.
		$sections['adyen_feedback'] = array(
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'methods'     => array( 'adyen' ),
			'description' => __( 'The URLs below need to be copied to the payment provider dashboard to receive automatic transaction status updates.', 'pronamic_ideal' ),
			'features'    => Gateway::get_supported_features(),
		);

		return $sections;
	}

	/**
	 * Fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( array $fields ) {
		// Merchant Account.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen',
			'meta_key' => '_pronamic_gateway_adyen_merchant_account',
			'title'    => _x( 'Merchant Account', 'adyen', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __( 'The merchant account identifier, with which you want to process the transaction', 'pronamic_ideal' ),
		);

		// API Key.
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'adyen',
			'meta_key'    => '_pronamic_gateway_adyen_api_key',
			'title'       => _x( 'API Key', 'adyen', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'classes'     => array( 'code' ),
			'methods'     => array( 'adyen' ),
			'tooltip'     => __( 'API key as mentioned in the payment provider dashboard', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/developers/user-management/how-to-get-the-api-key' ),
				esc_html__( 'Adyen documentation: "How to get the API key".', 'pronamic_ideal' )
			),
		);

		// Live API URL prefix.
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'adyen',
			'meta_key'    => '_pronamic_gateway_adyen_live_url_prefix',
			'title'       => _x( 'API Live URL Prefix', 'adyen', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'methods'     => array( 'adyen' ),
			'tooltip'     => __( 'The unique prefix for the live API URL, as mentioned at <strong>Account » API URLs</strong> in the Adyen dashboard', 'pronamic_ideal' ),
			'description' => sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://docs.adyen.com/developers/development-resources/live-endpoints#liveurlprefix' ),
				esc_html__( 'Adyen documentation: "Live URL prefix".', 'pronamic_ideal' )
			),
		);

		// Transaction feedback.
		$fields[] = array(
			'section'  => 'adyen',
			'methods'  => array( 'adyen' ),
			'title'    => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'     => 'description',
			'html'     => __( 'Receiving payment status updates needs additional configuration.', 'pronamic_ideal' ),
			'features' => Gateway::get_supported_features(),
		);

		// Webhook URL.
		$fields[] = array(
			'section'  => 'adyen_feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => rest_url( Integration::REST_ROUTE_NAMESPACE . '/notifications' ),
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
			'section' => 'adyen_feedback',
			'methods' => array( 'adyen' ),
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
			'section' => 'adyen_feedback',
			'methods' => array( 'adyen' ),
			'title'   => _x( 'Method', 'adyen notification', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => __( 'JSON', 'pronamic_ideal' ),
		);

		// Webhook authentication settings.
		$fields[] = array(
			'section' => 'adyen_feedback',
			'methods' => array( 'adyen' ),
			'title'   => __( 'Authentication', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => sprintf(
				'For webhook authentication settings, please visit <a href="%2$s" title="Settings">%1$s settings</a>.',
				__( 'Pronamic Pay', 'pronamic_ideal' ),
				$url = add_query_arg(
					array(
						'page' => 'pronamic_pay_settings',
					),
					admin_url( 'admin.php' )
				)
			),
		);

		// Webhook status.
		$fields[] = array(
			'section'  => 'adyen_feedback',
			'methods'  => array( 'adyen' ),
			'title'    => __( 'Status', 'pronamic_ideal' ),
			'type'     => 'description',
			'callback' => array( $this, 'feedback_status' ),
		);

		// Return fields.
		return $fields;
	}

	/**
	 * Transaction feedback status.
	 *
	 * @param array $field Settings field.
	 */
	public function feedback_status( $field ) {
		$features = Gateway::get_supported_features();

		WebhookManager::settings_status( $field, $features );
	}
}
