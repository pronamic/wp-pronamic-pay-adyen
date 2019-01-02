<?php
/**
 * Settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\GatewaySettings;

/**
 * Settings
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   2.0.0
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

		return $sections;
	}

	/**
	 * Fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( array $fields ) {
		// API Key.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'adyen',
			'meta_key' => '_pronamic_gateway_adyen_api_key',
			'title'    => _x( 'API Key', 'adyen', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => array( 'code' ),
			'methods'  => array( 'adyen' ),
			'tooltip'  => __( 'API key as mentioned in the payment provider dashboard', 'pronamic_ideal' ),
		);

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

		return $fields;
	}
}
