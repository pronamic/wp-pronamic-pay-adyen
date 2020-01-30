<?php
/**
 * Drop-in gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Locale;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Drop-in gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 1.0.5
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
	const SDK_VERSION = '3.4.0';

	/**
	 * Constructs and initializes an Adyen gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		// Supported features.
		$this->supports = array(
			'payment_status_request',
			'webhook_log',
			'webhook'
		);
	}

	/**
	 * Get supported payment methods
	 *
	 * @return array<string>
	 * @see Core_Gateway::get_supported_payment_methods()
	 *
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::ALIPAY,
			PaymentMethods::BANCONTACT,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::DIRECT_DEBIT,
			PaymentMethods::EPS,
			PaymentMethods::GIROPAY,
			PaymentMethods::IDEAL,
			PaymentMethods::SOFORT,
		);
	}

	/**
	 * Start.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 * @see Plugin::start()
	 *
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
		$payment_method_type = PaymentMethodType::transform( $payment->get_method() );

		if ( ! in_array( $payment_method_type, $api_integration_payment_method_types, true ) ) {
			return;
		}

		// Payment method.
		$payment_method = array(
			'type' => $payment_method_type,
		);

		if ( PaymentMethodType::IDEAL === $payment_method_type ) {
			$payment_method['issuer'] = (string) $payment->get_issuer();
		}

		$payment_method = new PaymentMethod( (object) $payment_method );

		// Create payment.
		$payment_response = $this->create_payment( $payment, $payment_method );

		if ( $payment_response instanceof \WP_Error ) {
			$this->error = $payment_response;

			return;
		}

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
	 *
	 * @return void
	 */
	public function payment_redirect( Payment $payment ) {
		$payment_response = $payment->get_meta( 'adyen_payment_response' );

		// Only show drop-in checkout page if payment method does not redirect.
		if ( '' !== $payment_response ) {
			$payment_response = PaymentResponse::from_object( $payment_response );

			$redirect = $payment_response->get_redirect();

			if ( null !== $redirect ) {
				\wp_redirect( $redirect->get_url() );
			}
		}

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
			null,
			false
		);

		wp_register_script(
			'pronamic-pay-adyen-checkout-drop-in',
			plugins_url( '../js/dist/checkout-drop-in.js', __FILE__ ),
			array ( 'pronamic-pay-adyen-checkout' ),
			null,
			true
		);

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
		 * Payment methods.
		 */
		$request = new PaymentMethodsRequest( $this->config->get_merchant_account() );

		if ( null !== $payment->get_method() ) {
			// Payment method type.
			$payment_method_type = PaymentMethodType::transform( $payment->get_method() );

			$request->set_allowed_payment_methods( array( $payment_method_type ) );
		}

		$locale = Util::get_payment_locale( $payment );

		$country_code = Locale::getRegion( $locale );

		$request->set_country_code( $country_code );
		$request->set_amount( AmountTransformer::transform( $payment->get_total_amount() ) );

		try {
			$payment_methods = $this->client->get_payment_methods( $request );
		} catch ( \Exception $e ) {
			Plugin::render_exception( $e );

			exit;
		}

		/**
		 * Adyen checkout configuration.
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web
		 * @link https://docs.adyen.com/checkout/components-web
		 */
		$configuration = (object) array(
			'locale'                      => Util::get_payment_locale( $payment ),
			'environment'                 => ( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			'originKey'                   => $this->config->origin_key,
			'paymentMethodsResponse'      => $payment_methods->get_original_object(),
			'paymentMethodsConfiguration' => $this->get_checkout_payment_methods_configuration( $payment ),
			'amount'                      => AmountTransformer::transform( $payment->get_total_amount() )->get_json(),
		);

		/**
		 * Filters the Adyen checkout configuration.
		 *
		 * @param object $configuration Adyen checkout configuration.
		 * @since 1.2.0
		 */
		$configuration = apply_filters( 'pronamic_pay_adyen_checkout_configuration', $configuration );

		wp_localize_script(
			'pronamic-pay-adyen-checkout',
			'pronamicPayAdyenCheckout',
			array(
				'paymentsUrl'        => rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/' . $payment->get_id() ),
				'paymentsDetailsUrl' => rest_url( Integration::REST_ROUTE_NAMESPACE . '/payments/details/' ),
				'paymentReturnUrl'   => $payment->get_return_url(),
				'configuration'      => $configuration,
				'paymentAuthorised'  => __( 'Payment completed successfully.', 'pronamic_ideal' ),
				'paymentReceived'    => __( 'The order has been received and we are waiting for the payment to clear.', 'pronamic_ideal' ),
				'paymentRefused'     => __( 'The payment has been refused. Please try again using a different method or card.', 'pronamic_ideal' ),
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

		if ( '' !== $payment_response ) {
			$payment_response = PaymentResponse::from_object( $payment_response );

			$details_result = $payment->get_meta( 'adyen_details_result' );

			// JSON decode details result meta.
			if ( '' !== $details_result ) {
				$details_result = \json_decode( $details_result );
			}

			// Set details result meta from GET or POST request parameters.
			if ( '' === $details_result ) {
				$details_result = array();

				$details = $payment_response->get_details();

				if ( null !== $details ) {
					$input_type = ( 'POST' === Server::get( 'REQUEST_METHOD' ) ? INPUT_POST : INPUT_GET );

					foreach ( $details as $detail ) {
						$key = $detail->get_key();

						$details_result[ $key ] = \filter_input( $input_type, $key, FILTER_SANITIZE_STRING );
					}

					$details_result = Util::filter_null( $details_result );

					$details_result = (object) $details_result;

					if ( ! empty( $details_result ) ) {
						$payment->set_meta( 'adyen_details_result', \wp_json_encode( $details_result ) );
					}
				}
			}

			$payment_data = $payment_response->get_payment_data();

			// Do not attempt to retrieve status without any request data,
			// payment status already updated when additional details were submitted (i.e. cards).
			if ( empty( $details_result ) && empty( $payment_data ) ) {
				return;
			}

			// Update payment status from payment details.
			$payment_details_request = new PaymentDetailsRequest( $details_result );

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
	 *
	 * @return \WP_Error|PaymentResponse
	 */
	public function create_payment( Payment $payment, PaymentMethod $payment_method ) {
		// Amount.
		try {
			$amount = AmountTransformer::transform( $payment->get_total_amount() );
		} catch ( \InvalidArgumentException $e ) {
			return new \WP_Error( 'adyen_error', $e->getMessage() );
		}

		// Payment request.
		$payment_request = new PaymentRequest(
			$amount,
			$this->config->get_merchant_account(),
			strval( $payment->get_id() ),
			$payment->get_return_url(),
			$payment_method
		);

		// Set country code.
		$locale = Util::get_payment_locale( $payment );

		$country_code = \Locale::getRegion( $locale );

		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$country = $billing_address->get_country_code();

			if ( ! empty( $country ) ) {
				$country_code = $country;
			}
		}

		$payment_request->set_country_code( $country_code );

		// Complement payment request.
		PaymentRequestHelper::complement( $payment, $payment_request );

		// Create payment.
		try {
			$payment_response = $this->client->create_payment( $payment_request );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'adyen_error', $e->getMessage() );
		}

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
	 * @param Payment               $payment                 Payment.
	 * @param PaymentDetailsRequest $payment_details_request Payment details request.
	 *
	 * @throws \Exception
	 */
	public function send_payment_details( Payment $payment, PaymentDetailsRequest $payment_details_request ) {
		$payment_response = $this->client->request_payment_details( $payment_details_request );

		return $payment_response;
	}

	/**
	 * Get checkout payment methods configuration.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return object
	 */
	public function get_checkout_payment_methods_configuration( Payment $payment ) {
		$configuration = array();

		// Cards.
		$configuration[ 'card' ] = array(
			'enableStoreDetails' => true,
			'hasHolderName'      => true,
			'holderNameRequired' => true,
			'hideCVC'            => false,
			'name'               => __( 'Credit or debit card', 'pronamic_ideal' ),
		);

		// Apple Pay.
		$configuration[ 'applepay' ] = array(
			'configuration' => array(
				// Name to be displayed on the form
				'merchantName'       => 'Adyen Test merchant',

				// Your Apple merchant identifier as described in https://developer.apple.com/documentation/apple_pay_on_the_web/applepayrequest/2951611-merchantidentifier
				'merchantIdentifier' => 'adyen.test.merchant',
			),

			/*
			onValidateMerchant: ( resolve, reject, validationURL ) => {
				// Call the validation endpoint with validationURL.
				// Call resolve(MERCHANTSESSION) or reject() to complete merchant validation.
			}
			*/
		);

		// Google Pay.
		$configuration[ 'googlepay' ] = array(
			// Change this to PRODUCTION when you're ready to accept live Google Pay payments
			'environment' => 'TEST',

			'configuration' => array(
				// Your Adyen merchant or company account name. Remove this field in TEST.
				'gatewayMerchantId'  => 'YourCompanyOrMerchantAccount',

				// Required for PRODUCTION. Remove this field in TEST. Your Google Merchant ID as described in https://developers.google.com/pay/api/web/guides/test-and-deploy/deploy-production-environment#obtain-your-merchantID
				'merchantIdentifier' => '12345678910111213141',
			),
		);

		// Boleto Bancário.
		$configuration[ 'boletobancario ' ] = array(
			// Turn personal details section on/off.
			'personalDetailsRequired' => true,

			// Turn billing address section on/off.
			'billingAddressRequired'  => true,

			// Allow shopper to specify their email address.
			'showEmailAddress'        => true,

			// Optionally pre-fill some fields, here all fields are filled:
			/*
			'data'                    => array(
				'socialSecurityNumber' => '56861752509',
				'shopperName'          => array(
					'firstName' => $payment->get_customer()->get_name()->get_first_name(),
					'lastName'  => $payment->get_customer()->get_name()->get_last_name(),
				),
				'billingAddress'       => array(
					'street'            => 'Rua Funcionarios',
					'houseNumberOrName' => '952',
					'city'              => 'São Paulo',
					'postalCode'        => '04386040',
					'stateOrProvince'   => 'SP',
					'country'           => 'BR'
				),
				'shopperEmail'         => $payment->get_customer()->get_email(),
			),
			*/
		);

		return (object) $configuration;
	}
}
