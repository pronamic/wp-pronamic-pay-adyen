<?php
/**
 * Drop-in gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Drop-in gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class DropInGateway extends AbstractGateway {
	/**
	 * Web SDK version.
	 *
	 * @link https://docs.adyen.com/developers/checkout/web-sdk/release-notes-web-sdk
	 *
	 * @var string
	 */
	const SDK_VERSION = '3.23.0';

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
	 * @return array<string>
	 * @see Core_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::AFTERPAY_COM,
			PaymentMethods::ALIPAY,
			PaymentMethods::APPLE_PAY,
			PaymentMethods::BANCONTACT,
			PaymentMethods::BLIK,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::DIRECT_DEBIT,
			PaymentMethods::EPS,
			PaymentMethods::GIROPAY,
			PaymentMethods::GOOGLE_PAY,
			PaymentMethods::IDEAL,
			PaymentMethods::KLARNA_PAY_LATER,
			PaymentMethods::KLARNA_PAY_NOW,
			PaymentMethods::KLARNA_PAY_OVER_TIME,
			PaymentMethods::MB_WAY,
			PaymentMethods::SOFORT,
			PaymentMethods::SWISH,
			PaymentMethods::TWINT,
			PaymentMethods::VIPPS,
		);
	}

	/**
	 * Start.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 */
	public function start( Payment $payment ) {
		$payment->set_meta( 'adyen_sdk_version', self::SDK_VERSION );
		$payment->set_action_url( $payment->get_pay_redirect_url() );

		/*
		 * API Integration
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
		 */
		$api_integration_payment_method_types = array(
			PaymentMethodType::ALIPAY,
			PaymentMethodType::IDEAL,
			PaymentMethodType::DIRECT_EBANKING,
		);

		// Return early if API integration is not being used.
		$payment_method_type = PaymentMethodType::transform( $payment->get_payment_method() );

		if ( ! in_array( $payment_method_type, $api_integration_payment_method_types, true ) ) {
			return;
		}

		// Payment method.
		$payment_method = array(
			'type' => $payment_method_type,
		);

		if ( PaymentMethodType::IDEAL === $payment_method_type ) {
			$payment_method['issuer'] = (string) $payment->get_meta( 'issuer' );
		}

		$payment_method = new PaymentMethod( (object) $payment_method );

		// Create payment.
		$payment_response = $this->create_payment( $payment, $payment_method );

		// Set payment action URL.
		$redirect = $payment_response->get_redirect();

		if ( null !== $redirect ) {
			$payment->set_action_url( $redirect->get_url() );
		}
	}

	/**
	 * Payment redirect.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 */
	public function payment_redirect( Payment $payment ) {
		// Check payment ID.
		$payment_id = $payment->get_id();

		if ( null === $payment_id ) {
			return;
		}

		// Redirect if payment is already successful.
		if ( PaymentStatus::SUCCESS === $payment->get_status() ) {
			\wp_redirect( $payment->get_return_redirect_url() );

			exit;
		}

		$payment_response = $payment->get_meta( 'adyen_payment_response' );

		// Only show drop-in checkout page if payment method does not redirect.
		if ( is_object( $payment_response ) ) {
			$payment_response = PaymentResponse::from_object( $payment_response );

			$redirect = $payment_response->get_redirect();

			if ( null !== $redirect ) {
				\wp_redirect( $redirect->get_url() );

				exit;
			}
		}

		/**
		 * Payment methods.
		 */
		$request = new PaymentMethodsRequest( $this->config->get_merchant_account() );

		$payment_method = $payment->get_payment_method();

		if ( null !== $payment_method ) {
			// Payment method type.
			$payment_method_type = PaymentMethodType::transform( $payment_method );

			if ( null !== $payment_method_type ) {
				$request->set_allowed_payment_methods( array( $payment_method_type ) );
			}
		}

		// Prevent Apple Pay if no merchant identifier has been configured.
		$apple_pay_merchant_id = $this->config->get_apple_pay_merchant_id();

		if ( empty( $apple_pay_merchant_id ) ) {
			$request->set_blocked_payment_methods( array( PaymentMethodType::APPLE_PAY ) );
		}

		// Set country code.
		$request->set_country_code( Util::get_country_code( $payment ) );

		$request->set_amount( AmountTransformer::transform( $payment->get_total_amount() ) );

		try {
			$payment_methods = $this->client->get_payment_methods( $request );
		} catch ( \Exception $e ) {
			Plugin::render_exception( $e );

			exit;
		}

		$payment_method_types = $payment_methods->get_payment_method_types();

		// Register scripts.
		$url_script = sprintf(
			'https://checkoutshopper-%s.adyen.com/checkoutshopper/sdk/%s/adyen.js',
			( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			self::SDK_VERSION
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
		wp_register_script(
			'pronamic-pay-adyen-checkout',
			$url_script,
			array(),
			self::SDK_VERSION,
			false
		);

		wp_register_script(
			'pronamic-pay-adyen-google-pay',
			'https://pay.google.com/gp/p/js/pay.js',
			array(),
			\pronamic_pay_plugin()->get_version(),
			false
		);

		$dependencies = array( 'pronamic-pay-adyen-checkout' );

		if ( \in_array( PaymentMethodType::GOOGLE_PAY, $payment_method_types, true ) ) {
			$dependencies[] = 'pronamic-pay-adyen-google-pay';
		}

		wp_register_script(
			'pronamic-pay-adyen-checkout-drop-in',
			plugins_url( '../js/dist/checkout-drop-in.js', __FILE__ ),
			$dependencies,
			\pronamic_pay_plugin()->get_version(),
			true
		);

		// Register styles.
		$url_stylesheet = sprintf(
			'https://checkoutshopper-%s.adyen.com/checkoutshopper/sdk/%s/adyen.css',
			( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			self::SDK_VERSION
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
		wp_register_style(
			'pronamic-pay-adyen-checkout',
			$url_stylesheet,
			array(),
			null
		);

		/**
		 * Adyen checkout configuration.
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web
		 * @link https://docs.adyen.com/checkout/components-web
		 */
		$configuration = array(
			'locale'                 => Util::get_payment_locale( $payment ),
			'environment'            => ( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			'originKey'              => $this->config->origin_key,
			'paymentMethodsResponse' => $payment_methods->get_original_object(),
			'amount'                 => AmountTransformer::transform( $payment->get_total_amount() )->get_json(),
		);

		/**
		 * Auto submit drop-in.
		 */
		$auto_submit_methods = array(
			PaymentMethodType::SWISH,
			PaymentMethodType::TWINT,
			PaymentMethodType::VIPPS,
			PaymentMethodType::UNIONPAY,
		);

		if ( 1 === \count( $payment_method_types ) && \in_array( $payment_method_types[0], $auto_submit_methods ) ) {
			$configuration['showPayButton'] = false;
		}

		$configuration = (object) $configuration;

		/**
		 * Filters the Adyen checkout configuration.
		 *
		 * @param object $configuration Adyen checkout configuration.
		 * @link https://docs.adyen.com/online-payments/drop-in-web#step-2-add-drop-in
		 * @since 1.2.0 Added.
		 */
		$configuration = apply_filters( 'pronamic_pay_adyen_checkout_configuration', $configuration );

		// Refused payment redirect URL.
		$refusal_redirect_url = null;

		if ( 'woocommerce' === $payment->get_source() ) {
			$refusal_redirect_url = $payment->get_return_url();
		}

		wp_localize_script(
			'pronamic-pay-adyen-checkout',
			'pronamicPayAdyenCheckout',
			array(
				'paymentMethodsConfiguration'   => $this->get_checkout_payment_methods_configuration( $payment_method_types, $payment ),
				'paymentsUrl'                   => rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/' . $payment_id ),
				'paymentsDetailsUrl'            => rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/details/' . $payment_id ),
				'applePayMerchantValidationUrl' => empty( $this->config->apple_pay_merchant_id_certificate ) ? false : \rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/applepay/merchant-validation/' . $payment_id ),
				'paymentReturnUrl'              => $payment->get_return_url(),
				'refusalRedirectUrl'            => $refusal_redirect_url,
				'configuration'                 => $configuration,
				'paymentAuthorised'             => __( 'Payment completed successfully.', 'pronamic_ideal' ),
				'paymentReceived'               => __( 'The order has been received and we are waiting for the payment to clear.', 'pronamic_ideal' ),
				'paymentRefused'                => __( 'The payment has been refused. Please try again using a different method or card.', 'pronamic_ideal' ),
				'syntaxError'                   => __( 'Received an invalid response while processing your request. Please try reloading this page.', 'pronamic_ideal' ),
				'unknownError'                  => __( 'An unknown error occurred while processing your request. Please try reloading this page.', 'pronamic_ideal' ),
			)
		);

		// Add checkout head action.
		add_action( 'pronamic_pay_adyen_checkout_head', array( $this, 'checkout_head' ) );

		// No cache.
		Core_Util::no_cache();

		require __DIR__ . '/../views/checkout-drop-in.php';

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

		wp_print_styles( 'pronamic-pay-adyen-checkout' );
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
		if ( filter_has_var( INPUT_GET, 'payload' ) ) {
			$payload = filter_input( INPUT_GET, 'payload', FILTER_SANITIZE_STRING );

			$payment_result_request = new PaymentResultRequest( $payload );

			try {
				$payment_result_response = $this->client->get_payment_result( $payment_result_request );

				PaymentResultHelper::update_payment( $payment, $payment_result_response );
			} catch ( \Exception $e ) {
				$note = sprintf(
					/* translators: %s: exception message */
					__( 'Error getting payment result: %s', 'pronamic_ideal' ),
					$e->getMessage()
				);

				$payment->add_note( $note );
			}

			return;
		}

		// Retrieve status from payment details.
		$payment_response = $payment->get_meta( 'adyen_payment_response' );

		if ( is_object( $payment_response ) ) {
			$payment_response = PaymentResponse::from_object( $payment_response );

			$details_result = $payment->get_meta( 'adyen_details_result' );

			// Set details result meta from GET or POST request parameters.
			if ( '' === $details_result ) {
				$details_result = array();

				$details = $payment_response->get_details();

				if ( null !== $details ) {
					$input_type = ( 'POST' === Server::get( 'REQUEST_METHOD' ) ? INPUT_POST : INPUT_GET );

					foreach ( $details as $detail ) {
						$key = (string) $detail->get_key();

						$details_result[ $key ] = \filter_input( $input_type, $key, FILTER_SANITIZE_STRING );
					}

					$details_result = Util::filter_null( $details_result );
				}

				if ( ! empty( $details_result ) ) {
					$payment->set_meta( 'adyen_details_result', \wp_json_encode( (object) $details_result ) );
				}
			}

			$payment_data = $payment_response->get_payment_data();

			// Do not attempt to retrieve status without any request data,
			// payment status already updated when additional details were submitted (i.e. cards).
			if ( empty( $details_result ) && empty( $payment_data ) ) {
				return;
			}

			// Update payment status from payment details.
			$payment_details_request = new PaymentDetailsRequest();

			$payment_details_request->set_details( (object) $details_result );

			$payment_details_request->set_payment_data( $payment_data );

			try {
				$payment_details_response = $this->client->request_payment_details( $payment_details_request );

				PaymentResponseHelper::update_payment( $payment, $payment_details_response );
			} catch ( \Exception $e ) {
				$note = sprintf(
					/* translators: %s: exception message */
					__( 'Error getting payment details: %s', 'pronamic_ideal' ),
					$e->getMessage()
				);

				$payment->add_note( $note );
			}
		}
	}

	/**
	 * Create payment.
	 *
	 * @param Payment       $payment        Payment.
	 * @param PaymentMethod $payment_method Payment method.
	 * @param object        $data           Adyen `state.data` object from drop-in.
	 *
	 * @return PaymentResponse
	 * @throws \InvalidArgumentException Throws exception on invalid amount.
	 * @throws \Exception Throws exception if payment creation request fails.
	 */
	public function create_payment( Payment $payment, PaymentMethod $payment_method, $data = null ) {
		$amount = AmountTransformer::transform( $payment->get_total_amount() );

		// Payment request.
		$payment_request = new PaymentRequest(
			$amount,
			$this->config->get_merchant_account(),
			(string) $payment->get_id(),
			$payment->get_return_url(),
			$payment_method
		);

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		// Set browser info.
		if ( \is_object( $data ) && isset( $data->browserInfo ) ) {
			$browser_info = BrowserInformation::from_object( $data->browserInfo );

			$payment_request->set_browser_info( $browser_info );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		// Merchant order reference.
		$payment_request->set_merchant_order_reference( $payment->format_string( $this->config->get_merchant_order_reference() ) );

		/**
		 * Application info.
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo
		 * @link https://docs.adyen.com/development-resources/building-adyen-solutions
		 */
		$application_info = new ApplicationInfo();

		$application_info->merchant_application = (object) array(
			'name'    => 'Pronamic Pay',
			'version' => \pronamic_pay_plugin()->get_version(),
		);

		$application_info->external_platform = (object) array(
			'integrator' => 'Pronamic',
			'name'       => 'WordPress',
			'version'    => \get_bloginfo( 'version' ),
		);

		$payment_request->set_application_info( $application_info );

		// Set country code.
		$payment_request->set_country_code( Util::get_country_code( $payment ) );

		// Complement payment request.
		PaymentRequestHelper::complement( $payment, $payment_request );

		// Create payment.
		$payment_response = $this->client->create_payment( $payment_request );

		/*
		 * Store payment response for later requests to `/payments/details`.
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments/details
		 */
		$payment->set_meta( 'adyen_payment_response', $payment_response->get_json() );

		// Update payment status based on response.
		PaymentResponseHelper::update_payment( $payment, $payment_response );

		return $payment_response;
	}

	/**
	 * Send payment details.
	 *
	 * @param PaymentDetailsRequest $payment_details_request Payment details request.
	 *
	 * @return PaymentResponse
	 * @throws \Exception Throws error if request fails.
	 */
	public function send_payment_details( PaymentDetailsRequest $payment_details_request ) {
		$payment_response = $this->client->request_payment_details( $payment_details_request );

		return $payment_response;
	}

	/**
	 * Get checkout payment methods configuration.
	 *
	 * @param array<int, string> $payment_method_types Payment method types.
	 * @param Payment            $payment              Payment.
	 *
	 * @return object
	 */
	public function get_checkout_payment_methods_configuration( $payment_method_types, Payment $payment ) {
		$configuration = array();

		/*
		 * Apple Pay.
		 *
		 * @link https://docs.adyen.com/payment-methods/apple-pay/web-drop-in#show-apple-pay-in-your-payment-form
		 */
		if ( \in_array( PaymentMethodType::APPLE_PAY, $payment_method_types, true ) ) {
			$configuration['applepay'] = array(
				'amount'        => $payment->get_total_amount()->get_minor_units()->to_int(),
				'currencyCode'  => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
				'configuration' => array(
					'merchantName'       => \get_bloginfo( 'name' ),
					'merchantIdentifier' => $this->config->get_apple_pay_merchant_id(),
				),
			);

			// Set country code.
			$billing_address = $payment->get_billing_address();

			if ( null !== $billing_address ) {
				$configuration['applepay']['countryCode'] = $billing_address->get_country_code();
			}

			/**
			 * Line Items.
			 *
			 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypaymentrequest/1916120-lineitems
			 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaylineitem
			 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaylineitem/1916086-amount
			 */
			$lines = $payment->get_lines();

			if ( null !== $lines ) {
				$line_items = array();

				foreach ( $lines as $line ) {
					$line_items[] = array(
						'label'  => $line->get_name(),
						'amount' => $line->get_total_amount()->number_format( null, '.', '' ),
						'type'   => 'final',
					);
				}

				$configuration['applepay']['lineItems'] = $line_items;
			}
		}

		/*
		 * Cards.
		 *
		 * @link https://docs.adyen.com/payment-methods/cards/web-drop-in#show-the-available-cards-in-your-payment-form
		 */
		if ( \in_array( PaymentMethodType::SCHEME, $payment_method_types, true ) ) {
			$configuration['card'] = array(
				'hasHolderName'      => true,
				'holderNameRequired' => true,
				'hideCVC'            => false,
				'name'               => __( 'Credit or debit card', 'pronamic_ideal' ),
			);
		}

		/*
		 * Google Pay.
		 *
		 * @link https://docs.adyen.com/payment-methods/google-pay/web-drop-in#show-google-pay-in-your-payment-form
		 */
		if ( \in_array( PaymentMethodType::GOOGLE_PAY, $payment_method_types, true ) ) {
			$configuration['paywithgoogle'] = array(
				'environment'   => ( self::MODE_TEST === $this->get_mode() ? 'TEST' : 'PRODUCTION' ),
				'amount'        => array(
					'currency' => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
					'value'    => $payment->get_total_amount()->get_minor_units()->to_int(),
				),
				'configuration' => array(
					'gatewayMerchantId' => $this->config->merchant_account,
				),
			);

			if ( self::MODE_LIVE === $this->get_mode() ) {
				$configuration['paywithgoogle']['configuration']['merchantIdentifier'] = $this->config->get_google_pay_merchant_identifier();
			}
		}

		/*
		 * PayPal.
		 *
		 * @link https://docs.adyen.com/payment-methods/paypal/web-drop-in#show-paypal-in-your-payment-form
		 */
		if ( \in_array( PaymentMethodType::PAYPAL, $payment_method_types, true ) ) {
			$configuration['paypal'] = array(
				'environment' => ( self::MODE_TEST === $this->get_mode() ? 'test' : 'live' ),
				'amount'      => array(
					'currency' => $payment->get_total_amount()->get_currency()->get_alphabetic_code(),
					'value'    => $payment->get_total_amount()->get_minor_units()->get_value(),
				),
			);

			$billing_address = $payment->get_billing_address();

			if ( null !== $billing_address ) {
				$configuration['paypal']['countryCode'] = $billing_address->get_country_code();
			}
		}

		return (object) $configuration;
	}
}
