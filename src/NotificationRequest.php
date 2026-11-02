<?php
/**
 * Notification request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Notification request class
 *
 * @link https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequest
 */
class NotificationRequest extends ResponseObject {
	/**
	 * Construct notification request.
	 *
	 * @param boolean                   $live  Informs about the origin of the notification.
	 * @param NotificationRequestItem[] $items A container object for the details included in the notification.
	 */
	public function __construct(
		/**
		 * Informs about the origin of the notification:
		 *
		 * - `true`: the notification originated from the live environment.
		 * - `false`: the notification originated from the test environment.
		 */
		private $live,
		/**
		 * A container object for the details included in the notification.
		 */
		private $items
	) {
	}

	/**
	 * Live.
	 *
	 * @return boolean True if live, false otherwise.
	 */
	public function is_live() {
		return $this->live;
	}

	/**
	 * Get items.
	 *
	 * @return NotificationRequestItem[]
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Create notification request from object.
	 *
	 * @param object $value Object.
	 * @return NotificationRequest
	 * @throws \InvalidArgumentException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $value ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/notification-request.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		$items = [];

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		foreach ( $value->notificationItems as $o ) {
			$items[] = NotificationRequestItem::from_object( $o->NotificationRequestItem );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		return new self(
			filter_var( $value->live, FILTER_VALIDATE_BOOLEAN ),
			$items
		);
	}
}
