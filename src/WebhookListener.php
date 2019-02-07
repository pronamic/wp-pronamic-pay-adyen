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
}
