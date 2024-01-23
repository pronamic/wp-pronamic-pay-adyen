<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\PaymentMethodsCollection;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Fields\CachedCallbackOptions;
use Pronamic\WordPress\Pay\Fields\IDealIssuerSelectField;
use Pronamic\WordPress\Pay\Fields\SelectFieldOption;
use Pronamic\WordPress\Pay\Fields\SelectFieldOptionGroup;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Gateway class
 */
class Gateway extends Core_Gateway {
	/**
	 * Web SDK version.
	 *
	 * @link https://docs.adyen.com/developers/checkout/web-sdk/release-notes-web-sdk
	 * @link https://www.npmjs.com/package/@adyen/adyen-web
	 * @var string
	 */
	const SDK_VERSION = '5.27.0';

	/**
	 * Config.
	 *
	 * @var Config
	 */
	private $config;

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

		$this->supports = [
			'webhook_log',
			'webhook',
		];

		$this->client = new Client( $config );

		// Methods.
		$ideal_payment_method = new PaymentMethod( PaymentMethods::IDEAL );

		$ideal_issuer_field = new IDealIssuerSelectField( 'ideal-issuer' );

		$ideal_issuer_field->set_required( true );

		$ideal_issuer_field->set_options(
			new CachedCallbackOptions(
				function () {
					return $this->get_ideal_issuers();
				},
				'pronamic_pay_ideal_issuers_' . \md5( \wp_json_encode( $config ) )
			)
		);

		$ideal_payment_method->add_field( $ideal_issuer_field );

		$this->register_payment_method( new PaymentMethod( PaymentMethods::AFTERPAY_COM ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::ALIPAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::APPLE_PAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANCONTACT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BLIK ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::CREDIT_CARD ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::DIRECT_DEBIT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::EPS ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::GIROPAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::GOOGLE_PAY ) );
		$this->register_payment_method( $ideal_payment_method );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::KLARNA_PAY_LATER ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::KLARNA_PAY_NOW ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::KLARNA_PAY_OVER_TIME ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MB_WAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MOBILEPAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::PAYPAL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SOFORT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SWISH ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::TWINT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::VIPPS ) );
	}

	/**
	 * Get payment methods.
	 *
	 * @param array $args Query arguments.
	 * @return PaymentMethodsCollection
	 */
	public function get_payment_methods( array $args = [] ): PaymentMethodsCollection {
		try {
			$this->maybe_enrich_payment_methods();
		} catch ( \Exception $e ) {
			// No problem.
		}

		return parent::get_payment_methods( $args );
	}

	/**
	 * Maybe enrich payment methods.
	 *
	 * @return void
	 */
	private function maybe_enrich_payment_methods() {
		$cache_key = 'pronamic_pay_adyen_payment_methods_' . \md5( \wp_json_encode( $this->config ) );

		$adyen_payment_methods = \get_transient( $cache_key );

		if ( false === $adyen_payment_methods ) {
			$payment_methods_response = $this->client->get_payment_methods( new PaymentMethodsRequest( $this->config->get_merchant_account() ) );

			$adyen_payment_methods = $payment_methods_response->get_payment_methods();

			\set_transient( $cache_key, $adyen_payment_methods, \DAY_IN_SECONDS );
		}

		foreach ( $adyen_payment_methods as $adyen_payment_method ) {
			$core_payment_method_id = PaymentMethodType::to_wp( $adyen_payment_method->get_type() );

			$core_payment_method = $this->get_payment_method( $core_payment_method_id );

			if ( null !== $core_payment_method ) {
				$core_payment_method->set_status( 'active' );
			}
		}

		foreach ( $this->payment_methods as $payment_method ) {
			if ( '' === $payment_method->get_status() ) {
				$payment_method->set_status( 'inactive' );
			}
		}
	}

	/**
	 * Get iDEAL issuers.
	 *
	 * @return iterable<SelectFieldOption|SelectFieldOptionGroup>
	 */
	private function get_ideal_issuers() {
		$issuers = [];

		$payment_methods_request = new PaymentMethodsRequest( $this->config->get_merchant_account() );

		$payment_methods_request->set_allowed_payment_methods( [ PaymentMethodType::IDEAL ] );

		$payment_methods_response = $this->client->get_payment_methods( $payment_methods_request );

		$payment_methods = $payment_methods_response->get_payment_methods();

		foreach ( $payment_methods as $payment_method ) {
			$payment_method_issuers = $payment_method->get_issuers();

			if ( is_array( $payment_method_issuers ) ) {
				foreach ( $payment_method_issuers as $payment_method_issuer ) {
					$id   = $payment_method_issuer->get_id();
					$name = $payment_method_issuer->get_name();

					$issuers[] = new SelectFieldOption( $id, $name );
				}
			}
		}

		return $issuers;
	}

	/**
	 * Start.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 * @throws \Exception Throws an exception if Adyen's resposne cannot be handled.
	 */
	public function start( Payment $payment ) {
		$payment->set_meta( 'adyen_sdk_version', self::SDK_VERSION );
		$payment->set_action_url( $payment->get_pay_redirect_url() );

		// API only.
		$payment_method_type = PaymentMethodType::transform( $payment->get_payment_method() );

		if ( ! $this->can_api_only( $payment_method_type ) ) {
			return;
		}

		// Payment method.
		switch ( $payment_method_type ) {
			case PaymentMethodType::IDEAL:
				$payment_method_details = new PaymentMethodIDealDetails( (string) $payment->get_meta( 'issuer' ) );

				break;
			default:
				$payment_method_details = new PaymentMethodDetails( $payment_method_type );
		}

		// Create payment.
		$payment_id = (string) $payment->get_id();

		$payment_request = new PaymentRequest(
			AmountTransformer::transform( $payment->get_total_amount() ),
			$this->config->get_merchant_account(),
			\sprintf( '%s-%s-%s', \get_current_network_id(), \get_current_blog_id(), $payment_id ),
			$this->get_payment_return_url( $payment_id ),
			$payment_method_details
		);

		PaymentRequestHelper::complement( $payment, $payment_request, $this->config );

		$payment_response = $this->client->create_payment( $payment_request );

		PaymentResponseHelper::update_payment( $payment, $payment_response );

		$result_code = $payment_response->get_result_code();

		if ( ResultCode::REDIRECT_SHOPPER !== $result_code ) {
			throw new \Exception(
				\sprintf(
					'The handling of the `%s` result code is not implemented.',
					\esc_html( (string) $result_code )
				)
			);
		}

		$action = $payment_response->get_action();

		if ( null === $action ) {
			throw new \Exception( 'Adyen did not provide an action to take for completing the payment.' );
		}

		$url = $action->get_url();

		if ( null === $url ) {
			throw new \Exception( 'Adyen did not provide an action URL.' );
		}

		$payment->set_action_url( $url );
	}

	/**
	 * Get payment return URL.
	 *
	 * @param string $payment_id Payment ID.
	 * @return string
	 */
	private function get_payment_return_url( $payment_id ) {
		return \rest_url( Integration::REST_ROUTE_NAMESPACE . '/return/' . $payment_id );
	}

	/**
	 * Payment redirect.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 * @throws \Exception When something goes wrong.
	 */
	public function payment_redirect( Payment $payment ) {
		// Check payment ID.
		$payment_id = $payment->get_id();

		if ( null === $payment_id ) {
			return;
		}

		$payment_id = (string) $payment_id;

		// Redirect if payment is already successful.
		if ( PaymentStatus::OPEN !== $payment->get_status() ) {
			\wp_redirect( $payment->get_return_redirect_url() );

			exit;
		}

		// Redirect API-only payment methods to payment action URL.
		$payment_method_type = PaymentMethodType::transform( $payment->get_payment_method() );

		if ( $this->can_api_only( $payment_method_type ) ) {
			$action_url = $payment->get_action_url();

			if ( null !== $action_url ) {
				\wp_redirect( $action_url );

				exit;
			}
		}

		/**
		 * Step 1: Create a payment session
		 *
		 * @link https://docs.adyen.com/online-payments/web-drop-in#create-payment-session
		 */
		$request = new PaymentSessionRequest(
			AmountTransformer::transform( $payment->get_total_amount() ),
			$this->config->get_merchant_account(),
			\sprintf( '%s-%s-%s', \get_current_network_id(), \get_current_blog_id(), $payment_id ),
			$this->get_payment_return_url( $payment_id )
		);

		PaymentRequestHelper::complement( $payment, $request, $this->config );

		// Payment method.
		$payment_method = $payment->get_payment_method();

		$payment_method_type = null;

		if ( null !== $payment_method ) {
			$payment_method_type = PaymentMethodType::transform( $payment_method );

			if ( null !== $payment_method_type ) {
				$request->set_allowed_payment_methods( [ $payment_method_type ] );
			}
		}

		try {
			if ( empty( $this->config->environment ) ) {
				throw new \Exception(
					'No Adyen environment configured.'
				);
			}

			if ( empty( $this->config->client_key ) ) {
				throw new \Exception(
					'No Adyen client key configured.'
				);
			}

			$payment_session = $this->client->create_payment_session( $request );
		} catch ( \Exception $e ) {
			Plugin::render_exception( $e );

			exit;
		}

		// Endpoint.
		$endpoint = new Endpoint( $this->config->environment, $this->config->api_live_url_prefix );

		// Register scripts.
		$url_script = $endpoint->get_web_url( self::SDK_VERSION, 'adyen.js' );

		\wp_register_script(
			'pronamic-pay-adyen-checkout',
			$url_script,
			[],
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
			null,
			false
		);

		$asset = include __DIR__ . '/../js/dist/checkout-drop-in.asset.php';

		\wp_register_script(
			'pronamic-pay-adyen-checkout-drop-in',
			\plugins_url( '../js/dist/checkout-drop-in.js', __FILE__ ),
			[ 'pronamic-pay-adyen-checkout' ],
			$asset['version'],
			true
		);

		// Register styles.
		$url_stylesheet = $endpoint->get_web_url( self::SDK_VERSION, 'adyen.css' );

		\wp_register_style(
			'pronamic-pay-adyen-checkout',
			$url_stylesheet,
			[],
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
			null
		);

		/**
		 * Adyen checkout configuration.
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web
		 * @link https://docs.adyen.com/checkout/components-web
		 */
		$configuration = [
			'locale'                      => Util::get_payment_locale( $payment ),
			'environment'                 => $this->config->environment,
			'session'                     => (object) [
				'id'          => $payment_session->get_id(),
				'sessionData' => $payment_session->get_data(),
			],
			'clientKey'                   => $this->config->client_key,
			'amount'                      => AmountTransformer::transform( $payment->get_total_amount() )->get_json(),
			'paymentMethodsConfiguration' => $this->get_payment_methods_configuration( $payment ),
		];

		$configuration = (object) $configuration;

		/**
		 * Filters the Adyen checkout configuration.
		 *
		 * @param object $configuration Adyen checkout configuration.
		 * @link https://docs.adyen.com/online-payments/drop-in-web#step-2-add-drop-in
		 * @since 1.2.0 Added.
		 */
		$configuration = \apply_filters( 'pronamic_pay_adyen_checkout_configuration', $configuration );

		\wp_localize_script(
			'pronamic-pay-adyen-checkout',
			'pronamicPayAdyenCheckout',
			[
				'configuration'      => $configuration,
				'paymentRedirectUrl' => \add_query_arg(
					[
						'_wpnonce' => \wp_create_nonce( 'wp_rest' ),
						'nonce'    => \wp_create_nonce( 'pronamic-pay-adyen-payment-redirect-' . $payment->get_id() ),
					],
					\rest_url( Integration::REST_ROUTE_NAMESPACE . '/redirect/' . $payment_id )
				),
				'paymentErrorUrl'    => \add_query_arg(
					[
						'_wpnonce' => \wp_create_nonce( 'wp_rest' ),
						'nonce'    => \wp_create_nonce( 'pronamic-pay-adyen-payment-error-' . $payment->get_id() ),
					],
					\rest_url( Integration::REST_ROUTE_NAMESPACE . '/error/' . $payment_id )
				),
				'autoSubmit'         => $this->can_auto_submit( $payment_method_type ),
			]
		);

		\add_action( 'pronamic_pay_adyen_checkout_head', [ $this, 'checkout_head' ] );

		Core_Util::no_cache();

		require __DIR__ . '/../views/checkout-drop-in.php';

		exit;
	}

	/**
	 * Check if payment method type can be used API only.
	 *
	 * @param string|null $payment_method_type Adyen payment method type.
	 * @return bool True if payment method type can be used API only, false otherwise.
	 */
	private function can_api_only( $payment_method_type ) {
		return \in_array(
			$payment_method_type,
			[
				/**
				 * Payment method type Alipay.
				 *
				 * @link https://docs.adyen.com/payment-methods/alipay/api-only
				 */
				PaymentMethodType::ALIPAY,
				/**
				 * Payment method type iDEAL.
				 *
				 * @link https://docs.adyen.com/payment-methods/ideal/api-only
				 */
				PaymentMethodType::IDEAL,
				/**
				 * Payment method type Sofort.
				 *
				 * @link https://docs.adyen.com/payment-methods/sofort/api-only
				 */
				PaymentMethodType::DIRECT_EBANKING,
				/**
				 * Payment method type TWINT.
				 *
				 * @link https://docs.adyen.com/payment-methods/twint/api-only
				 */
				PaymentMethodType::TWINT,
				/**
				 * Payment method type Vipps.
				 *
				 * @link https://docs.adyen.com/payment-methods/vipps/api-only
				 */
				PaymentMethodType::VIPPS,
			],
			true
		);
	}

	/**
	 * Check if drop-in should auto submit.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-adyen/issues/9
	 * @param string|null $payment_method_type Adyen payment method type.
	 * @return bool True if drop-in should auto submit, false otherwise.
	 */
	private function can_auto_submit( $payment_method_type ) {
		return \in_array(
			$payment_method_type,
			[
				PaymentMethodType::MOBILEPAY,
				PaymentMethodType::SWISH,
				PaymentMethodType::UNIONPAY,
			],
			true
		);
	}

	/**
	 * Checkout head.
	 *
	 * @return void
	 */
	public function checkout_head() {
		\wp_print_styles( 'pronamic-pay-redirect' );

		\wp_print_scripts( 'pronamic-pay-adyen-checkout' );

		\wp_print_styles( 'pronamic-pay-adyen-checkout' );
	}

	/**
	 * Get checkout payment methods configuration.
	 *
	 * @param Payment $payment Payment.
	 * @return object
	 */
	private function get_payment_methods_configuration( Payment $payment ) {
		$configuration = [];

		/**
		 * Apple Pay.
		 *
		 * @link https://docs.adyen.com/payment-methods/apple-pay/web-drop-in#drop-in-configuration
		 */
		$configuration['applepay'] = [];

		/**
		 * Line Items.
		 *
		 * @link https://docs.adyen.com/payment-methods/apple-pay/web-drop-in#ap-payment-request-data
		 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaypaymentrequest/1916120-lineitems
		 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaylineitem
		 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/applepaylineitem/1916086-amount
		 */
		$lines = $payment->get_lines();

		if ( null !== $lines ) {
			$line_items = [];

			foreach ( $lines as $line ) {
				$line_items[] = [
					'label'  => $line->get_name(),
					'amount' => $line->get_total_amount()->number_format( null, '.', '' ),
					'type'   => 'final',
				];
			}

			$configuration['applepay']['lineItems'] = $line_items;
		}

		return (object) $configuration;
	}
}
