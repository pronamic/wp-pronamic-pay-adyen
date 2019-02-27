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
		// Amount.
		$amount = new Amount(
			$payment->get_total_amount()->get_currency()->get_alphabetic_code(),
			$payment->get_total_amount()->get_minor_units()
		);

		// Payment method. Take leap of faith for unknown payment methods.
		$type = PaymentMethodType::transform(
			$payment->get_method(),
			$payment->get_method()
		);

		$payment_method = new PaymentMethod( $type );

		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
				$payment_method->issuer = $payment->get_issuer();

				break;
		}

		// Country.
		$locale = get_locale();

		if ( null !== $payment->get_customer() ) {
			$locale = $payment->get_customer()->get_locale();
		}

		$locale = explode( '_', $locale );

		$country_code = strtoupper( substr( $locale[1], 0, 2 ) );

		// Create payment or payment session request.
		switch ( $payment->get_method() ) {
			case PaymentMethods::IDEAL:
			case PaymentMethods::SOFORT:
				// API integration.
				$request = new PaymentRequest(
					$amount,
					$this->config->merchant_account,
					$payment->get_id(),
					$payment->get_return_url(),
					$payment_method
				);

				$request->set_country_code( $country_code );

				break;
			default:
				// Web SDK integration.
				$request = new PaymentSessionRequest(
					$amount,
					$this->config->merchant_account,
					$payment->get_id(),
					$payment->get_return_url(),
					$country_code
				);

				$request->set_origin( home_url() );
				$request->set_sdk_version( self::SDK_VERSION );

				// Set allowed payment methods.
				$allowed_methods = array( $type );

				// Add all available payment methods if no payment method is given.
				if ( empty( $type ) ) {
					$allowed_methods = array();

					foreach ( $this->get_available_payment_methods() as $method ) {
						$allowed_methods[] = PaymentMethodType::transform( $method );
					}
				}

				$request->set_allowed_payment_methods( $allowed_methods );
		}

		// Channel.
		$request->set_channel( 'Web' );

		// Shopper.
		$request->set_shopper_statement( $payment->get_description() );

		if ( null !== $payment->get_customer() ) {
			$request->set_shopper_ip( $payment->get_customer()->get_ip_address() );
			$request->set_shopper_statement( $payment->get_customer()->get_gender() );
			$request->set_shopper_locale( $payment->get_customer()->get_locale() );
			$request->set_shopper_reference( $payment->get_customer()->get_user_id() );
			$request->set_telephone_number( $payment->get_customer()->get_phone() );

			if ( null !== $payment->get_customer()->get_name() ) {
				$shopper_name = new ShopperName(
					$payment->get_customer()->get_name()->get_first_name(),
					$payment->get_customer()->get_name()->get_middle_name(),
					$payment->get_customer()->get_name()->get_last_name()
				);

				$request->set_shopper_name( $shopper_name );
			}
		}

		// Lines.
		$lines = $payment->get_lines();

		if ( null !== $lines ) {
			$line_items = $request->new_items();

			$i = 1;

			foreach ( $lines as $line ) {
				/* translators: %s: item index */
				$name = sprintf( __( 'Item %s', 'pronamic_ideal' ), $i ++ );

				if ( null !== $line->get_name() && '' !== $line->get_name() ) {
					$name = $line->get_name();
				}

				$item = $line_items->new_item(
					DataHelper::shorten( $name, 50 ),
					$line->get_quantity(),
					// The amount in cents, including VAT, of the item each, see below for more details.
					AmountTransformer::transform( $line->get_unit_price() ),
					$line->get_type()
				);

				$item->set_id( $line->get_id() );

				// Description.
				$description = $line->get_description();

				if ( null !== $description ) {
					$description = DataHelper::shorten( $description, 100 );
				}

				$item->set_description( $description );

				$tax_amount = $line->get_unit_price()->get_tax_amount();

				if ( null !== $tax_amount ) {
					// The VAT of the item each, see below for more details.
					$item->set_tax( AmountTransformer::transform( $tax_amount ) );
				}
			}
		}

		// Create payment or payment session.
		if ( $request instanceof PaymentRequest ) {
			$result = $this->client->create_payment( $request );
		} else {
			$result = $this->client->create_payment_session( $request );
		}

		// Handle errors.
		if ( ! $result ) {
			$this->error = $this->client->get_error();

			return;
		}

		// Load checkout view for payment sessions.
		if ( isset( $result->paymentSession ) ) {
			$url = sprintf(
				'https://checkoutshopper-%s.adyen.com/checkoutshopper/assets/js/sdk/checkoutSDK.%s.min.js',
				( self::MODE_TEST === $this->config->mode ? 'test' : 'live' ),
				self::SDK_VERSION
			);

			wp_register_script(
				'pronamic-pay-adyen-checkout',
				$url,
				array(),
				self::SDK_VERSION,
				false
			);

			// No cache.
			Util::no_cache();

			$payment_session = $result->paymentSession;

			$context = ( self::MODE_TEST === $this->config->mode ? 'test' : 'live' );

			require __DIR__ . '/../views/checkout.php';

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
			$status = ResultCode::transform( $result->resultCode );

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
			case EventCode::AUTHORIZATION:
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
