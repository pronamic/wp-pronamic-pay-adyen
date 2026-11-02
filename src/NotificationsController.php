<?php
/**
 * Notifications controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Exception\ValidationException;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use WP_Error;
use WP_REST_Request;

/**
 * Notification controller class
 *
 * @link https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequest
 */
class NotificationsController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		// Actions.
		\add_action( 'rest_api_init', $this->rest_api_init( ... ) );

		// Filters.
		\add_filter( 'application_password_is_api_request', $this->maybe_disable_application_passwords_for_api_request( ... ), 10 );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 *
	 * @return void
	 */
	public function rest_api_init() {
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/notifications',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_adyen_notifications' ],
				'permission_callback' => $this->rest_api_adyen_permissions_check( ... ),
			]
		);
	}

	/**
	 * Disable Application Passwords for routes within integration REST route namespace,
	 * as it interferes with our HTTP Basic authorization permission check.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/489b33c97a8942e43c92f3d4a5efe069fecccb32/wp-includes/user.php#L327-L336
	 * @param bool $is_api_request If this is an acceptable API request.
	 * @return bool
	 */
	public function maybe_disable_application_passwords_for_api_request( $is_api_request ) {
		/*
		 * Check if request URI contains the integration REST route namespace,
		 * parsed REST request is not yet available at this point during WordPress bootstrap.
		*/
		if ( false === \stripos( \get_self_link(), Integration::REST_ROUTE_NAMESPACE ) ) {
			return $is_api_request;
		}

		// Return `false` to disable application passwords validation for this request.
		return false;
	}

	/**
	 * Get the HTTP Basic authentication username and password.
	 *
	 * @return array{0: string, 1: string}
	 */
	private function get_basic_authentication_credentials() {
		return [
			(string) \get_option( 'pronamic_pay_adyen_notification_authentication_username' ),
			(string) \get_option( 'pronamic_pay_adyen_notification_authentication_password' ),
		];
	}

	/**
	 * Check if HTTP Basic authentication is configured.
	 *
	 * @return bool
	 */
	public static function is_basic_authentication_configured() {
		$username = (string) \get_option( 'pronamic_pay_adyen_notification_authentication_username' );
		$password = (string) \get_option( 'pronamic_pay_adyen_notification_authentication_password' );

		return ( '' !== $username || '' !== $password );
	}

	/**
	 * REST API Adyen permissions check.
	 *
	 * When HTTP Basic authentication is not configured, the notifications
	 * handler requires a valid HMAC signature for each notification item.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#permissions-callback
	 *
	 * @param WP_REST_Request $request Request.
	 * @return true|WP_Error
	 */
	public function rest_api_adyen_permissions_check( WP_REST_Request $request ) {
		if ( ! self::is_basic_authentication_configured() ) {
			return true;
		}

		[ $username, $password ] = $this->get_basic_authentication_credentials();

		$authorization = $request->get_header( 'Authorization' );

		/**
		 * Authorization header missing.
		 *
		 * @link https://basecamp.com/1810084/projects/10966871/todos/403544593
		 * @link https://github.com/WordPress/WordPress/blob/5.2/wp-includes/rest-api/class-wp-rest-request.php#L188-L208
		 * @link https://www.wp-pay.org/http-authorization-header-missing/
		 */
		if ( null === $authorization ) {
			return new WP_Error(
				'rest_forbidden_context',
				sprintf(
					/* translators: %s: Help URL. */
					__( 'HTTP Authorization header is missing, read %s for more information.', 'pronamic_ideal' ),
					'https://www.wp-pay.org/http-authorization-header-missing/'
				),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		$expected = 'Basic ' . base64_encode( $username . ':' . $password );

		if ( \hash_equals( $expected, $authorization ) ) {
			return true;
		}

		return new WP_Error(
			'rest_forbidden_context',
			/* translators: Translate 'notification' the same as in the Adyen dashboard. */
			_x( 'Sorry, you are not allowed to post Adyen notifications.', 'Adyen', 'pronamic_ideal' ),
			[ 'status' => rest_authorization_required_code() ]
		);
	}

	/**
	 * Get payment for notification request item.
	 *
	 * @param NotificationRequestItem $item Notification request item.
	 * @return Payment|null
	 */
	private function get_payment( NotificationRequestItem $item ) {
		$payment_id = $item->get_merchant_reference();

		if ( \str_contains( $payment_id, '-' ) ) {
			[$scan_network_id, $scan_blog_id, $scan_payment_id] = \sscanf( $payment_id, '%d-%d-%s' );

			$payment_id = null;

			if ( \get_current_network_id() === $scan_network_id && \get_current_blog_id() === $scan_blog_id ) {
				$payment_id = $scan_payment_id;
			}
		}

		return \get_pronamic_payment( $payment_id );
	}

	/**
	 * REST API Adyen notifications handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_notifications( WP_REST_Request $request ) {
		$json = $request->get_body();

		$data = json_decode( $json );

		try {
			$notification_request = NotificationRequest::from_object( $data );
		} catch ( ValidationException ) {
			return new WP_Error(
				'adyen_invalid_notification',
				/* translators: Translate 'notification' the same as in the Adyen dashboard. */
				_x( 'Cannot parse JSON notification.', 'Adyen', 'pronamic_ideal' ),
				[ 'status' => 500 ]
			);
		}

		$basic_authentication = self::is_basic_authentication_configured();

		/**
		 * Authenticate all notification items before processing any of them.
		 */
		$notifications = [];

		foreach ( $notification_request->get_items() as $item ) {
			$payment = $this->get_payment( $item );

			if ( null === $payment ) {
				continue;
			}

			$gateway = $payment->get_gateway();

			if ( ! $gateway instanceof Gateway ) {
				continue;
			}

			$config = $gateway->get_config();

			$hmac_key = $config->get_hmac_key();

			if ( '' !== $hmac_key ) {
				if ( ! NotificationHmac::verify( $item, $hmac_key ) ) {
					return new WP_Error(
						'adyen_invalid_hmac_signature',
						/* translators: Translate 'notification' the same as in the Adyen dashboard. */
						_x( 'Invalid HMAC signature in Adyen notification.', 'Adyen', 'pronamic_ideal' ),
						[ 'status' => rest_authorization_required_code() ]
					);
				}
			} elseif ( ! $basic_authentication ) {
				return new WP_Error(
					'adyen_notification_authentication_required',
					/* translators: Translate 'notification' the same as in the Adyen dashboard. */
					_x( 'Adyen notifications require authentication, configure HTTP Basic authentication or an HMAC key.', 'Adyen', 'pronamic_ideal' ),
					[ 'status' => rest_authorization_required_code() ]
				);
			}

			$notifications[] = [ $item, $payment, $config ];
		}

		foreach ( $notifications as [ $item, $payment, $config ] ) {
			$this->handle_notification( $item, $payment, $config );
		}

		$response = (object) [
			'notificationResponse' => '[accepted]',
		];

		return $response;
	}

	/**
	 * Handle authenticated notification request item.
	 *
	 * @param NotificationRequestItem $item    Notification request item.
	 * @param Payment                 $payment Payment.
	 * @param Config                  $config  Adyen configuration of the payment.
	 * @return void
	 */
	private function handle_notification( NotificationRequestItem $item, Payment $payment, Config $config ) {
		// Add note.
		$note = sprintf(
			'<p>%s</p>',
			sprintf(
				/* translators: %s: payment provider name */
				__( 'Webhook requested by %s.', 'pronamic_ideal' ),
				__( 'Adyen', 'pronamic_ideal' )
			)
		);

		$json = wp_json_encode( $item->get_json(), JSON_PRETTY_PRINT );

		if ( false !== $json ) {
			$note .= sprintf(
				'<pre>%s</pre>',
				esc_html( $json )
			);
		}

		$payment->add_note( $note );

		/**
		 * Log Adyen notification request for payment.
		 *
		 * @param Payment $payment Payment.
		 */
		do_action( 'pronamic_pay_webhook_log_payment', $payment );

		// Authorization.
		if ( EventCode::AUTHORIZATION !== $item->get_event_code() ) {
			return;
		}

		if ( PaymentStatus::SUCCESS === $payment->get_status() ) {
			return;
		}

		$error = $this->validate_authorization( $item, $payment, $config );

		if ( null !== $error ) {
			$payment->add_note(
				sprintf(
					'<p>%s</p>',
					sprintf(
						/* translators: %s: reason */
						esc_html__( 'Adyen notification ignored: %s', 'pronamic_ideal' ),
						esc_html( $error )
					)
				)
			);

			return;
		}

		if ( null === $payment->get_transaction_id() || '' === $payment->get_transaction_id() ) {
			$payment->set_transaction_id( $item->get_psp_reference() );
		}

		$payment->set_status( $item->is_success() ? PaymentStatus::SUCCESS : PaymentStatus::FAILURE );

		$adyen_payment_method = $item->get_payment_method();

		if ( null !== $adyen_payment_method ) {
			$pronamic_payment_method = PaymentMethodType::to_wp( $adyen_payment_method );

			if ( null !== $pronamic_payment_method ) {
				$payment->set_payment_method( $pronamic_payment_method );
			}
		}

		$payment->save();
	}

	/**
	 * Validate authorization notification request item against payment.
	 *
	 * @param NotificationRequestItem $item    Notification request item.
	 * @param Payment                 $payment Payment.
	 * @param Config                  $config  Adyen configuration of the payment.
	 * @return string|null Error message, or `null` if valid.
	 */
	private function validate_authorization( NotificationRequestItem $item, Payment $payment, Config $config ) {
		if ( $item->get_merchant_account_code() !== $config->get_merchant_account() ) {
			return sprintf(
				/* translators: 1: notification merchant account, 2: configuration merchant account */
				__( 'merchant account `%1$s` does not match `%2$s`.', 'pronamic_ideal' ),
				$item->get_merchant_account_code(),
				$config->get_merchant_account()
			);
		}

		$transaction_id = (string) $payment->get_transaction_id();

		if ( '' !== $transaction_id && $item->get_psp_reference() !== $transaction_id ) {
			return sprintf(
				/* translators: 1: notification PSP reference, 2: payment transaction ID */
				__( 'PSP reference `%1$s` does not match `%2$s`.', 'pronamic_ideal' ),
				$item->get_psp_reference(),
				$transaction_id
			);
		}

		try {
			$payment_amount = AmountTransformer::transform( $payment->get_total_amount() );
		} catch ( \InvalidArgumentException ) {
			return __( 'payment amount could not be determined.', 'pronamic_ideal' );
		}

		$item_amount = $item->get_amount();

		if ( $item_amount->get_currency() !== $payment_amount->get_currency() || $item_amount->get_value() !== $payment_amount->get_value() ) {
			return sprintf(
				/* translators: 1: notification amount, 2: payment amount */
				__( 'amount `%1$s` does not match `%2$s`.', 'pronamic_ideal' ),
				$item_amount->get_currency() . ' ' . $item_amount->get_value(),
				$payment_amount->get_currency() . ' ' . $payment_amount->get_value()
			);
		}

		return null;
	}
}
