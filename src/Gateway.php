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

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 * @link    https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 */
class Gateway extends Core_Gateway {
	/**
	 * Slug of this gateway.
	 *
	 * @var string
	 */
	const SLUG = 'adyen';

	/**
	 * Client.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Constructs and initializes an Adyen gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );
		$this->set_slug( self::SLUG );

		$this->client = new Client( $config->api_key, $config->api_live_url_prefix );
		$this->client->set_merchant_account( $config->merchant_account );
		$this->client->set_mode( $config->mode );
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
	 * @param Payment $payment Payment.
	 *
	 * @see Plugin::start()
	 */
	public function start( Payment $payment ) {
		// Payment request.
		$request = new PaymentRequest();

		$request->merchant_account = $this->config->merchant_account;
		$request->return_url       = $payment->get_return_url();
		$request->reference        = $payment->get_id();
		$request->origin_url       = home_url();
		$request->sdk_version      = '1.6.3';
		$request->channel          = 'Web';

		// Amount.
		$request->currency     = $payment->get_total_amount()->get_currency()->get_alphabetic_code();
		$request->amount_value = $payment->get_total_amount()->get_cents(); // @todo Money +get_minor_units().

		// Payment method. Take leap of faith for unknown payment method types.
		$adyen_method = PaymentMethodType::transform( $payment->get_method(), $payment->get_method() );

		$request->payment_method = array(
			'type' => $adyen_method,
		);

		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
				$request->payment_method['issuer'] = $payment->get_issuer();

				break;
		}

		// Country.
		$locale = get_locale();

		if ( null !== $payment->get_customer() ) {
			$locale = $payment->get_customer()->get_locale();
		}

		$locale = explode( '_', $locale );

		$request->country_code = strtoupper( substr( $locale[1], 0, 2 ) );

		// Shopper.
		$request->shopper_statement = $payment->get_description();

		if ( null !== $payment->get_customer() ) {
			$request->shopper_ip               = $payment->get_customer()->get_ip_address();
			$request->shopper_gender           = $payment->get_customer()->get_gender();
			$request->shopper_locale           = $payment->get_customer()->get_locale();
			$request->shopper_reference        = $payment->get_customer()->get_user_id();
			$request->shopper_telephone_number = $payment->get_customer()->get_phone();

			if ( null !== $payment->get_customer()->get_name() ) {
				$request->shopper_first_name = $payment->get_customer()->get_name()->get_first_name();
				$request->shopper_name_infix = $payment->get_customer()->get_name()->get_middle_name();
				$request->shopper_last_name  = $payment->get_customer()->get_name()->get_last_name();
			}
		}

		// Create payment or payment session.
		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
			case PaymentMethods::SOFORT:
				// API integration.
				$result = $this->client->create_payment( $request );

				break;
			default:
				// Web SDK integration.
				$allowed_methods = array( $adyen_method );

				// Add all available payment methods if no payment method is given.
				if ( empty( $adyen_method ) ) {
					$allowed_methods = array();

					foreach ( $this->get_available_payment_methods() as $method ) {
						$allowed_methods[] = PaymentMethodType::transform( $method );
					}
				}

				// Set allowed payment methods.
				$request->allowed_payment_methods = $allowed_methods;

				// Create payment session.
				$result = $this->client->create_payment_session( $request );
		}

		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return;
		}

		if ( isset( $result->paymentSession ) ) {
			// No cache.
			Util::no_cache();

			$redirect_message = '<div id="pronamic-pay-checkout"></div><div style="clear:both;"></div>';

			include Plugin::$dirname . '/views/redirect-message.php';

			?>

			<script type="text/javascript" src="https://checkoutshopper-test.adyen.com/checkoutshopper/assets/js/sdk/checkoutSDK.1.6.3.min.js"></script>

			<script type="text/javascript">
			// Initiate the Adyen Checkout form.
			var checkout = chckt.checkout(
				'<?php echo esc_html( $result->paymentSession ); ?>',
				'#pronamic-pay-checkout',
				{ context: '<?php echo( self::MODE_TEST === $this->config->mode ? 'test' : 'live' ); ?>' }
			);

			// Redirect once payment completes.
			chckt.hooks.beforeComplete = function ( node, paymentData ) {
				if ( "undefined" !== paymentData.payload ) {
					window.location.href = '<?php echo esc_url( $payment->get_return_url() ); ?>&payload=' + encodeURIComponent( paymentData.payload );

					return false;
				}
			};
			</script>

			<?php

			exit;
		}

		// Set transaction ID.
		if ( isset( $result->pspReference ) ) {
			$payment->set_transaction_id( $result->pspReference );
		}

		// Set redirect URL.
		if ( isset( $result->redirect->url ) ) {
			$payment->set_action_url( $result->redirect->url );
		}
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 */
	public function update_status( Payment $payment ) {
		// Maybe process stored webhook notification.
		$this->maybe_handle_notification( $payment );

		// Process payload on return.
		if ( ! filter_has_var( INPUT_GET, 'payload' ) ) {
			return;
		}

		$status = null;

		$payload = filter_input( INPUT_GET, 'payload', FILTER_SANITIZE_STRING );

		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
			case PaymentMethods::SOFORT:
				$result = $this->client->get_payment_details( $payload );

				break;
			default:
				$result = $this->client->get_payment_result( $payload );
		}

		if ( $result ) {
			$status = Statuses::transform( $result->resultCode );

			$psp_reference = $result->pspReference;
		}

		// Handle errors.
		if ( empty( $status ) ) {
			$payment->set_status( Core_Statuses::FAILURE );

			$this->error = $this->client->get_error();

			return;
		}

		// Update status.
		$payment->set_status( $status );

		// Update transaction ID.
		if ( isset( $psp_reference ) ) {
			$payment->set_transaction_id( $psp_reference );
		}
	}

	/**
	 * Maybe handle notification.
	 *
	 * @param Payment $payment      Payment.
	 */
	public function maybe_handle_notification( Payment $payment ) {
		$notification = $payment->get_meta( 'adyen_notification' );

		if ( empty( $notification ) ) {
			return;
		}

		$notification = json_decode( $notification );

		if ( ! is_object( $notification ) ) {
			return;
		}

		switch ( $notification->eventCode ) {
			case EventCodes::AUTHORIZATION:
				$this->handle_authorization_event( $payment, $notification );

				break;
		}

		$payment->set_meta( 'adyen_notification', null );
	}

	/**
	 * Handle authorization event.
	 *
	 * @param Payment $payment      Payment.
	 * @param object  $notification Notification.
	 */
	public function handle_authorization_event( Payment $payment, $notification ) {
		if ( ! is_object( $notification ) ) {
			return;
		}

		$success = $notification->success;

		if ( 'true' === $success ) {
			$status = Core_Statuses::SUCCESS;
		} else {
			$status = Core_Statuses::FAILURE;

			// Add note.
			$note = sprintf(
				/* translators: %s: failure reason message */
				__( 'Failure reason: %s.', 'pronamic_ideal' ),
				esc_html( $notification->reason )
			);

			$payment->add_note( $note );
		}

		$payment->set_status( $status );
	}

	/**
	 * Get available payment methods.
	 *
	 * @see Core_Gateway::get_available_payment_methods()
	 */
	public function get_available_payment_methods() {
		$payment_methods = array();

		// Get active payment methods for Adyen account.
		$methods = $this->client->get_payment_methods();

		if ( ! $methods ) {
			$this->error = $this->client->get_error();

			return $payment_methods;
		}

		// Transform to WordPress payment methods.
		foreach ( $methods as $method => $details ) {
			$payment_method = PaymentMethodType::transform_gateway_method( $method );

			if ( $payment_method ) {
				$payment_methods[] = $payment_method;
			}
		}

		$payment_methods = array_unique( $payment_methods );

		return $payment_methods;
	}

	/**
	 * Get issuers.
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$payment_method = PaymentMethodType::transform( PaymentMethods::IDEAL );

		$result = $this->client->get_issuers( $payment_method );

		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return $groups;
		}

		$groups[] = array(
			'options' => $result,
		);

		return $groups;
	}
}
