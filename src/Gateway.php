<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use InvalidArgumentException;
use Locale;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;
use WP_Error;

/**
 * Gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Web SDK version.
	 *
	 * @link https://docs.adyen.com/developers/checkout/web-sdk/release-notes-web-sdk
	 *
	 * @var string
	 */
	const SDK_VERSION = '1.9.2';

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
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = array();

		// Client.
		$this->client = new Client( $config );
	}

	/**
	 * Get supported payment methods
	 *
	 * @see Core_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::BANCONTACT,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::DIRECT_DEBIT,
			PaymentMethods::GIROPAY,
			PaymentMethods::IDEAL,
			PaymentMethods::MAESTRO,
			PaymentMethods::SOFORT,
		);
	}

	/**
	 * Start.
	 *
	 * @see Plugin::start()
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 */
	public function start( Payment $payment ) {
		// Amount.
		try {
			$amount = AmountTransformer::transform( $payment->get_total_amount() );
		} catch ( InvalidArgumentException $e ) {
			$this->error = new WP_Error( 'adyen_error', $e->getMessage() );

			return;
		}

		// Payment method type.
		$payment_method_type = PaymentMethodType::transform( $payment->get_method() );

		// Country.
		$locale = get_locale();

		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			$locale = $customer->get_locale();
		}

		$locale = strval( $locale );

		$country_code = Locale::getRegion( $locale );

		// Set country from billing address.
		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$country = $billing_address->get_country_code();

			if ( ! empty( $country ) ) {
				$country_code = $country;
			}
		}

		/*
		 * API Integration
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
		 */
		$api_integration_payment_method_types = array(
			PaymentMethodType::IDEAL,
			PaymentMethodType::DIRECT_EBANKING,
		);

		if ( in_array( $payment_method_type, $api_integration_payment_method_types, true ) ) {
			$payment_method = new PaymentMethod( $payment_method_type );

			if ( PaymentMethodType::IDEAL === $payment_method_type ) {
				$payment_method = new PaymentMethodIDeal( $payment_method_type, (string) $payment->get_issuer() );
			}

			// API integration.
			$payment_request = new PaymentRequest(
				$amount,
				$this->config->get_merchant_account(),
				strval( $payment->get_id() ),
				$payment->get_return_url(),
				$payment_method
			);

			$payment_request->set_country_code( $country_code );

			PaymentRequestHelper::complement( $payment, $payment_request );

			try {
				$payment_response = $this->client->create_payment( $payment_request );
			} catch ( Exception $e ) {
				$this->error = new WP_Error( 'adyen_error', $e->getMessage() );

				return;
			}

			$payment->set_transaction_id( $payment_response->get_psp_reference() );

			$redirect = $payment_response->get_redirect();

			if ( null !== $redirect ) {
				$payment->set_action_url( $redirect->get_url() );
			}

			// Return early so SDK integration code will not be executed for API integration.
			return;
		}

		/*
		 * SDK Integration
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
		 */
		$payment_session_request = new PaymentSessionRequest(
			$amount,
			$this->config->get_merchant_account(),
			strval( $payment->get_id() ),
			$payment->get_return_url(),
			$country_code
		);

		PaymentRequestHelper::complement( $payment, $payment_session_request );

		// Origin.
		$origin = home_url();

		$origin_url = wp_parse_url( home_url() );

		if ( is_array( $origin_url ) && isset( $origin_url['scheme'], $origin_url['host'] ) ) {
			$origin = sprintf(
				'%s://%s',
				$origin_url['scheme'],
				$origin_url['host']
			);
		}

		$payment_session_request->set_origin( $origin );
		$payment_session_request->set_sdk_version( self::SDK_VERSION );

		if ( null !== $payment_method_type ) {
			$payment_session_request->set_allowed_payment_methods( array( $payment_method_type ) );
		}

		try {
			$payment_session_response = $this->client->create_payment_session( $payment_session_request );
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'adyen_error', $e->getMessage() );

			return;
		}

		$payment->set_meta( 'adyen_sdk_version', self::SDK_VERSION );
		$payment->set_meta( 'adyen_payment_session', $payment_session_response->get_payment_session() );

		$payment->set_action_url( $payment->get_pay_redirect_url() );
	}

	/**
	 * Payment redirect.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 */
	public function payment_redirect( Payment $payment ) {
		$sdk_version     = $payment->get_meta( 'adyen_sdk_version' );
		$payment_session = $payment->get_meta( 'adyen_payment_session' );

		if ( empty( $sdk_version ) || empty( $payment_session ) ) {
			return;
		}

		if ( empty( $payment->config_id ) ) {
			return;
		}

		$url = sprintf(
			'https://checkoutshopper-%s.adyen.com/checkoutshopper/assets/js/sdk/checkoutSDK.%s.min.js',
			( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			$sdk_version
		);

		wp_register_script(
			'pronamic-pay-adyen-checkout',
			$url,
			array(
				'jquery',
			),
			$sdk_version,
			false
		);

		/**
		 * Config object.
		 *
		 * @link https://docs.adyen.com/checkout/web-sdk/
		 * @link https://docs.adyen.com/checkout/web-sdk/customization/settings/
		 * @link https://docs.adyen.com/checkout/web-sdk/customization/styling/#styling-the-card-fields
		 */
		$config_object = (object) array(
			'context' => ( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
		);

		/**
		 * Filters the Adyen config object.
		 *
		 * @link https://github.com/wp-pay-gateways/adyen#pronamic_pay_adyen_config_object
		 * @link https://docs.adyen.com/checkout/web-sdk/
		 * @link https://docs.adyen.com/checkout/web-sdk/customization/settings/
		 * @link https://docs.adyen.com/checkout/web-sdk/customization/styling/#styling-the-card-fields
		 *
		 * @param object $config_object Adyen config object.
		 *
		 * @since 1.1
		 */
		$config_object = apply_filters( 'pronamic_pay_adyen_config_object', $config_object );

		wp_localize_script(
			'pronamic-pay-adyen-checkout',
			'pronamicPayAdyenCheckout',
			array(
				'paymentsResultUrl' => rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/result/' . $payment->config_id ),
				'paymentReturnUrl'  => $payment->get_return_url(),
				'paymentSession'    => $payment_session,
				'configObject'      => $config_object,
			)
		);

		// Add checkout head action.
		add_action( 'pronamic_pay_adyen_checkout_head', array( $this, 'checkout_head' ) );

		// No cache.
		Core_Util::no_cache();

		require __DIR__ . '/../views/checkout.php';

		exit;
	}

	/**
	 * Checkout head.
	 *
	 * @return void
	 */
	public function checkout_head() {
		wp_print_styles( 'pronamic-pay-redirect' );

		wp_print_scripts( 'pronamic-pay-adyen-checkout' );
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 */
	public function update_status( Payment $payment ) {
		// Process payload on return.
		if ( ! filter_has_var( INPUT_GET, 'payload' ) ) {
			return;
		}

		$payload = filter_input( INPUT_GET, 'payload', FILTER_SANITIZE_STRING );

		$payment_result_request = new PaymentResultRequest( $payload );

		try {
			$payment_result_response = $this->client->get_payment_result( $payment_result_request );

			PaymentResultHelper::update_payment( $payment, $payment_result_response );
		} catch ( Exception $e ) {
			$note = sprintf(
				/* translators: %s: exception message */
				__( 'Error getting payment result: %s', 'pronamic_ideal' ),
				$e->getMessage()
			);

			$payment->add_note( $note );
		}
	}

	/**
	 * Get available payment methods.
	 *
	 * @see Core_Gateway::get_available_payment_methods()
	 */
	public function get_available_payment_methods() {
		$core_payment_methods = array();

		try {
			$payment_methods_response = $this->client->get_payment_methods();
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'adyen_error', $e->getMessage() );

			return $core_payment_methods;
		}

		foreach ( $payment_methods_response->get_payment_methods() as $payment_method ) {
			$core_payment_method = PaymentMethodType::to_wp( $payment_method->get_type() );

			$core_payment_methods[] = $core_payment_method;
		}

		$core_payment_methods = array_filter( $core_payment_methods );
		$core_payment_methods = array_unique( $core_payment_methods );

		return $core_payment_methods;
	}

	/**
	 * Get issuers.
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 * @return array
	 */
	public function get_issuers() {
		$issuers = array();

		try {
			$payment_methods_response = $this->client->get_payment_methods();
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'adyen_error', $e->getMessage() );

			return $issuers;
		}

		$payment_methods = $payment_methods_response->get_payment_methods();

		// Limit to iDEAL payment methods.
		$payment_methods = array_filter(
			$payment_methods,
			/**
			 * Check if payment method is iDEAL.
			 *
			 * @param PaymentMethod $payment_method Payment method.
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
					if ( 'issuer' === $detail->key && 'select' === $detail->type ) {
						foreach ( $detail->items as $item ) {
							$issuers[ $item->id ] = $item->name;
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
