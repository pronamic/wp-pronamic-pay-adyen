<?php
/**
 * Notification request item
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use DateTime;
use InvalidArgumentException;

/**
 * Notification request item
 *
 * @link    https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequestitem
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationRequestItem {
	/**
	 * Amount.
	 *
	 * @var Amount
	 */
	private $amount;

	/**
	 * Adyen's 16-character unique reference associated with the transaction/the request. This value is globally unique; quote it when communicating with us about this request.
	 *
	 * @var string
	 */
	private $psp_reference;

	/**
	 * The type of event the notification item refers to.
	 *
	 * @var string
	 */
	private $event_code;

	/**
	 * The time when the event was generated.
	 *
	 * @var DateTime
	 */
	private $event_date;

	/**
	 * The merchant account identifier used in the transaction the notification item refers to.
	 *
	 * @var string
	 */
	private $merchant_account_code;

	/**
	 * This field holds a list of the modification operations supported by the transaction the notification item refers to.
	 *
	 * @var array
	 */
	private $operations;

	/**
	 * A reference to uniquely identify the payment. 
	 *
	 * @var string
	 */
	private $merchant_reference;

	/**
	 * The payment method used in the transaction the notification item refers to.
	 *
	 * @var string
	 */
	private $payment_method;

	/**
	 * Informs about the outcome of the event (`eventCode`) the notification refers to:
	 *
	 * - `true`: the event (`eventCode`) the notification refers to was executed successfully.
	 * - `false`: the event was not executed successfully.
	 *
	 * @var boolean
	 */
	private $success;

	/**
	 * Create notification request item from object.
	 *
	 * @param object $object Object.
	 * @return NotificationRequestItem
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->amount ) ) {
			throw new InvalidArgumentException( 'Object must contain `amount` property.' );
		}

		if ( ! isset( $object->pspReference ) ) {
			throw new InvalidArgumentException( 'Object must contain `pspReference` property.' );
		}

		if ( ! isset( $object->eventCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `eventCode` property.' );
		}

		if ( ! isset( $object->eventDate ) ) {
			throw new InvalidArgumentException( 'Object must contain `eventDate` property.' );
		}

		if ( ! isset( $object->merchantAccountCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `merchantAccountCode` property.' );
		}

		if ( ! isset( $object->operations ) ) {
			throw new InvalidArgumentException( 'Object must contain `operations` property.' );
		}

		if ( ! isset( $object->merchantReference ) ) {
			throw new InvalidArgumentException( 'Object must contain `merchantReference` property.' );
		}

		if ( ! isset( $object->paymentMethod ) ) {
			throw new InvalidArgumentException( 'Object must contain `paymentMethod` property.' );
		}

		if ( ! isset( $object->success ) ) {
			throw new InvalidArgumentException( 'Object must contain `success` property.' );
		}

		if ( ! is_array( $object->operations ) ) {
			throw new InvalidArgumentException( 'Object property `operations` must be an array.' );			
		}

		$item = new self();

		return $item;
	}
}
