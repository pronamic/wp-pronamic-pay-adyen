<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;

/**
 * Gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
 */
abstract class AbstractGateway extends Core_Gateway {
	/**
	 * Config.
	 * 
	 * @var Config
	 */
	protected $config;

	/**
	 * Client.
	 *
	 * @var Client
	 */
	public $client;

	/**
	 * Constructs and initializes an Adyen gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->config = $config;

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = array();

		// Client.
		$this->client = new Client( $config );
	}

	/**
	 * Get available payment methods.
	 *
	 * @return array<int, string>
	 * @see Core_Gateway::get_available_payment_methods()
	 */
	public function get_available_payment_methods() {
		$core_payment_methods = array();

		$payment_methods_response = $this->client->get_payment_methods( new PaymentMethodsRequest( $this->config->get_merchant_account() ) );

		foreach ( $payment_methods_response->get_payment_methods() as $payment_method ) {
			$type = $payment_method->get_type();

			if ( null === $type ) {
				continue;
			}

			$core_payment_method = PaymentMethodType::to_wp( $type );

			$core_payment_methods[] = $core_payment_method;
		}

		$core_payment_methods = array_filter( $core_payment_methods );
		$core_payment_methods = array_unique( $core_payment_methods );

		return $core_payment_methods;
	}

	/**
	 * Get issuers.
	 *
	 * @return array<string, string>|array<int, array<string, array<string, string>>>
	 * @see Core_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$issuers = array();

		$payment_methods_response = $this->client->get_payment_methods( new PaymentMethodsRequest( $this->config->get_merchant_account() ) );

		$payment_methods = $payment_methods_response->get_payment_methods();

		// Limit to iDEAL payment methods.
		$payment_methods = array_filter(
			$payment_methods,
			/**
			 * Check if payment method is iDEAL.
			 *
			 * @param PaymentMethod $payment_method Payment method.
			 *
			 * @return boolean True if payment method is iDEAL, false otherwise.
			 */
			function( $payment_method ) {
				return ( PaymentMethodType::IDEAL === $payment_method->get_type() );
			}
		);

		foreach ( $payment_methods as $payment_method ) {
			$details = $payment_method->get_details();

			if ( is_array( $details ) ) {
				foreach ( $details as $detail ) {
					if ( ! isset( $detail->key, $detail->type, $detail->items ) ) {
						continue;
					}

					if ( 'issuer' === $detail->key && 'select' === $detail->type ) {
						foreach ( $detail->items as $item ) {
							$issuers[ \strval( $item->id ) ] = \strval( $item->name );
						}
					}
				}
			}
		}

		if ( empty( $issuers ) ) {
			return $issuers;
		}

		return array(
			array(
				'options' => $issuers,
			),
		);
	}
}
