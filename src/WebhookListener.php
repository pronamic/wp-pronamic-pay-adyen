<?php
/**
 * Webhook listener.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use Pronamic\WordPress\Pay\Core\Server;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Webhook listener.
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class WebhookListener {
	/**
	 * Listen to Adyen webhook requests.
	 */
	public static function listen() {
		if ( ! filter_has_var( INPUT_GET, 'adyen_webhook' ) ) {
			return;
		}

		$json = file_get_contents( 'php://input' );

		if ( empty( $json ) ) {
			return;
		}

		$notifications = json_decode( $json );

		if ( ! is_object( $notifications ) ) {
			return;
		}

		// Process notification.
		foreach ( $notifications->notificationItems as $notification ) {
			$notification = $notification->NotificationRequestItem;

			$payment = get_pronamic_payment( $notification->merchantReference );

			if ( ! $payment ) {
				continue;
			}

			if ( ! self::authenticate( $payment->get_config_id() ) ) {
				exit;
			}

			// Store notification.
			$payment->set_meta( 'adyen_notification', wp_json_encode( $notification ) );

			// Add note.
			$note = sprintf(
				/* translators: %s: Adyen */
				__( 'Webhook requested by %s.', 'pronamic_ideal' ),
				__( 'Adyen', 'pronamic_ideal' )
			);

			$payment->add_note( $note );

			// Update payment.
			try {
				Plugin::update_payment( $payment, false );
			} catch ( Exception $e ) {
				/*
				 * Try/catch to make sure the notification will be accepted.
				 *
				 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications?redirect#preventingqueueing
				 */
			}
		}

		/*
		 * Accept notification.
		 *
		 * @link https://docs.adyen.com/developers/development-resources/notifications/set-up-notifications?redirect#step2acceptnotifications
		 */
		wp_send_json(
			array(
				'notificationResponse' => '[accepted]',
			)
		);
	}

	/**
	 * Check authentication or request HTTP Basic auth.
	 *
	 * @param string $config_id Config ID.
	 *
	 * @return bool
	 */
	public static function authenticate( $config_id ) {
		// Check if gateway exists.
		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway ) {
			return true;
		}

		// Check empty webhook authentication settings.
		$config_factory = new ConfigFactory();

		$config = $config_factory->get_config( $config_id );

		if ( empty( $config->webhook_username ) && empty( $config->webhook_password ) ) {
			return true;
		}

		// Validate authentication.
		$user     = Server::get( 'PHP_AUTH_USER' );
		$password = Server::get( 'PHP_AUTH_PW' );

		if ( null !== $user && null !== $password && $config->webhook_username === $user && $config->webhook_password === $password ) {
			return true;
		}

		// Send HTTP Basic authentication headers.
		$realm = __( 'Pronamic Pay Adyen webhook', 'pronamic_ideal' );

		header( 'WWW-Authenticate: Basic realm="' . esc_html( $realm ) . '"' );
		header( 'HTTP/1.0 401 Unauthorized' );

		echo 'Unauthorized';

		exit;
	}
}
