<?php
/**
 * Notifications controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Exception\ValidationException;
use Pronamic\WordPress\Pay\Core\Statuses as PaymentStatus;
use WP_Error;
use WP_REST_Request;

/**
 * Notification controller
 *
 * @link https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequest
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationsController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
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
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_adyen_notifications' ),
				'permission_callback' => array( $this, 'rest_api_adyen_permissions_check' ),
			)
		);
	}

	/**
	 * REST API Adyen permissions check.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#permissions-callback
	 *
	 * @param WP_REST_Request $request Request.
	 * @return true|WP_Error
	 */
	public function rest_api_adyen_permissions_check( WP_REST_Request $request ) {
		$username = get_option( 'pronamic_pay_adyen_notification_authentication_username' );
		$password = get_option( 'pronamic_pay_adyen_notification_authentication_password' );

		if ( empty( $username ) && empty( $password ) ) {
			return true;
		}

		$authorization = $request->get_header( 'Authorization' );

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		if ( 'Basic ' . base64_encode( $username . ':' . $password ) === $authorization ) {
			return true;
		}

		return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to post Adyen notifications.', 'pronamic_ideal' ), array( 'status' => rest_authorization_required_code() ) );
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
		} catch ( ValidationException $e ) {
			return new WP_Error( 'adyen_invalid_notification', __( 'Cannot parse JSON notification.', 'pronamic_ideal' ), array( 'status' => 500 ) );
		}

		foreach ( $notification_request->get_items() as $item ) {
			$payment = get_pronamic_payment( $item->get_merchant_reference() );

			if ( null === $payment ) {
				continue;
			}

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
					$json
				);
			}

			$payment->add_note( $note );

			do_action( 'pronamic_pay_webhook_log_payment', $payment );

			// Authorization.
			if ( EventCode::AUTHORIZATION === $item->get_event_code() ) {
				$payment->set_status( $item->is_success() ? PaymentStatus::SUCCESS : PaymentStatus::FAILURE );

				$payment->save();
			}
		}

		$response = (object) array(
			'notificationResponse' => '[accepted]',
		);

		return $response;
	}
}
