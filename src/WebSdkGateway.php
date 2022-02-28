<?php
/**
 * Web SDK gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
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
 * Web SDK gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
 */
class WebSdkGateway extends AbstractGateway {
	/**
	 * Web SDK version.
	 *
	 * @link https://docs.adyen.com/developers/checkout/web-sdk/release-notes-web-sdk
	 *
	 * @var string
	 */
	const SDK_VERSION = '1.9.2';

	/**
	 * Constructs and initializes an Adyen gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		// Supported features.
		$this->supports = array(
			'webhook_log',
			'webhook',
		);
	}

	/**
	 * Get supported payment methods
	 *
	 * @see Core_Gateway::get_supported_payment_methods()
	 *
	 * @return array<string>
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
	 * @param Payment $payment Payment.
	 * @return void
	 * @throws \Exception Throws an exception when the shopper country cannot be determined.
	 */
	public function start( Payment $payment ) {
		// Amount.
		$amount = AmountTransformer::transform( $payment->get_total_amount() );

		// Payment method type.
		$payment_method_type = PaymentMethodType::transform( $payment->get_payment_method() );

		// Country.
		$country_code = Util::get_country_code( $payment );

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
			$payment_method = array(
				'type' => $payment_method_type,
			);

			if ( PaymentMethodType::IDEAL === $payment_method_type ) {
				$payment_method['issuer'] = (string) $payment->get_meta( 'issuer' );
			}

			// API integration.
			$payment_request = new PaymentRequest(
				$amount,
				$this->config->get_merchant_account(),
				(string) $payment->get_id(),
				$payment->get_return_url(),
				new PaymentMethod( (object) $payment_method )
			);

			$payment_request->set_country_code( $country_code );

			PaymentRequestHelper::complement( $payment, $payment_request );

			$payment_response = $this->client->create_payment( $payment_request );

			$payment->set_transaction_id( $payment_response->get_psp_reference() );

			$redirect = $payment_response->get_redirect();

			if ( null !== $redirect ) {
				$payment->set_action_url( $redirect->get_url() );
			}

			// Return early so SDK integration code will not be executed for API integration.
			return;
		}

		/**
		 * The shopper country is required.
		 * 
		 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v67/post/paymentSession__reqParam_countryCode
		 */
		if ( null === $country_code ) {
			throw new \Exception( 'Unable to determine shopper country.' );
		}

		/**
		 * SDK Integration
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
		 */
		$payment_session_request = new PaymentSessionRequest(
			$amount,
			$this->config->get_merchant_account(),
			(string) $payment->get_id(),
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

		$payment_session_response = $this->client->create_payment_session( $payment_session_request );

		$payment->set_meta( 'adyen_sdk_version', self::SDK_VERSION );
		$payment->set_meta( 'adyen_payment_session', $payment_session_response->get_payment_session() );

		$payment->set_action_url( $payment->get_pay_redirect_url() );
	}

	/**
	 * Payment redirect.
	 *
	 * @param Payment $payment Payment.
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
		 * @since 1.1 Added.
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

		require __DIR__ . '/../views/checkout-web-sdk.php';

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
}
