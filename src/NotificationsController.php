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

use InvalidArgumentException;
use Pronamic\WordPress\Pay\Core\Statuses as PaymentStatus;
use Pronamic\WordPress\Pay\Core\Server;
use WP_Error;
use WP_REST_Request;

/**
 * Notification controller
 *
 * @link    https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequest
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationsController {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/adyen/v1';

	/**
	 * Setup.
	 */
	public function setup() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 */
	public function rest_api_init() {
		register_rest_route(
			self::REST_ROUTE_NAMESPACE,
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
	 */
	public function rest_api_adyen_permissions_check( WP_REST_Request $request ) {
		$username = get_option( 'pronamic_pay_adyen_notification_authentication_username' );
		$password = get_option( 'pronamic_pay_adyen_notification_authentication_password' );

		if ( empty( $username ) && empty( $password ) ) {
			return true;
		}

		$username_input = Server::get( 'PHP_AUTH_USER' );	
		$password_input = Server::get( 'PHP_AUTH_PW' );

		if ( $username === $username_input && $password === $password_input ) {
			return true;
		}

		return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to post Adyen notifications.' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * REST API Adyen notifications handler.
	 *
	 * @param WP_REST_Request $request Request.
	 */
	public function rest_api_adyen_notifications( WP_REST_Request $request ) {
		$json = $request->get_body();

		$data = json_decode( $json );

		try {
			$notification_request = NotificationRequest::from_object( $data );
		} catch ( InvalidArgumentException $e ) {
			return new WP_Error( 'adyen_invalid_notification', __( 'Cannot parse JSON notification.' ), array( 'status' => 500 ) );
		}

		foreach ( $notification_request->get_items() as $item ) {
			$payment = get_pronamic_payment( $item->get_merchant_reference() );

			if ( empty( $payment ) ) {
				continue;
			}

			// Store notification.
			$payment->set_meta( 'adyen_notification', $json );

			// Add note.
			$note = sprintf(
				/* translators: %s: Adyen */
				__( 'Webhook requested by %s.', 'pronamic_ideal' ),
				__( 'Adyen', 'pronamic_ideal' )
			);

			$payment->add_note( $note );

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
