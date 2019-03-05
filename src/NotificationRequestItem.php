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
use Pronamic\WordPress\Pay\Core\Util;

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
	 * Get amount.
	 *
	 * @return Amount
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Set amount.
	 *
	 * @param Amount $amount Amount.
	 */
	public function set_amount( Amount $amount ) {
		$this->amount = $amount;
	}

	/**
	 * Get PSP reference.
	 *
	 * @return string
	 */
	public function get_psp_reference() {
		return $this->psp_reference;
	}

	/**
	 * Set PSP reference.
	 *
	 * @param string $psp_reference PSP reference.
	 */
	public function set_psp_reference( $psp_reference ) {
		$this->psp_reference = $psp_reference;
	}

	/**
	 * Get event code.
	 *
	 * @return string
	 */
	public function get_event_code() {
		return $this->event_code;
	}

	/**
	 * Set event code.
	 *
	 * @param string $event_code Event code.
	 */
	public function set_event_code( $event_code ) {
		$this->event_code = $event_code;
	}

	/**
	 * Get event date.
	 *
	 * @return DateTime
	 */
	public function get_event_date() {
		return $this->event_date;
	}

	/**
	 * Set event date.
	 *
	 * @param DateTime $event_date Event date.
	 */
	public function set_event_date( DateTime $event_date ) {
		$this->event_date = $event_date;
	}

	/**
	 * Get merchant account code.
	 *
	 * @return string
	 */
	public function get_merchant_account_code() {
		return $this->merchant_account_code;
	}

	/**
	 * Set merchant account code.
	 *
	 * @param string $merchant_account_code Merchant account code.
	 */
	public function set_merchant_account_code( $merchant_account_code ) {
		$this->merchant_account_code = $merchant_account_code;
	}

	/**
	 * Get operations.
	 *
	 * @return array
	 */
	public function get_operations() {
		return $this->operations;
	}

	/**
	 * Set operations.
	 *
	 * @param array $operations Operations.
	 */
	public function set_operations( $operations ) {
		$this->operations = $operations;
	}

	/**
	 * Get operations.
	 *
	 * @return string
	 */
	public function get_merchant_reference() {
		return $this->merchant_reference;
	}

	/**
	 * Set merchant reference.
	 *
	 * @param string $merchant_reference Merchant reference.
	 */
	public function set_merchant_reference( $merchant_reference ) {
		$this->merchant_reference = $merchant_reference;
	}

	/**
	 * Get payment method.
	 *
	 * @return string
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Set payment method.
	 *
	 * @param string $payment_method Payment method.
	 */
	public function set_payment_method( $payment_method ) {
		$this->payment_method = $payment_method;
	}

	/**
	 * Is success.
	 *
	 * @return boolean
	 */
	public function is_success() {
		return $this->success;
	}

	/**
	 * Set success.
	 *
	 * @param boolean $success Success.
	 */
	public function set_success( $success ) {
		$this->success = $success;
	}

	/**
	 * Get JSON.
	 *
	 * @return object|null
	 */
	public function get_json() {
		$data = array(
			'amount'              => $this->get_amount(),
			'pspReference'        => $this->get_psp_reference(),
			'eventCode'           => $this->get_event_code(),
			'eventDate'           => $this->get_event_date(),
			'merchantAccountCode' => $this->get_merchant_account_code(),
			'operations'          => $this->get_operations(),
			'merchantReference'   => $this->get_merchant_reference(),
			'paymentMethod'       => $this->get_payment_method(),
			'success'             => Util::boolean_to_string( $this->is_success() ),
		);

		$data = array_filter( $data );

		if ( empty( $data ) ) {
			return null;
		}

		return (object) $data;
	}

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

		$item->set_amount( Amount::from_object( $object->amount ) );
		$item->set_psp_reference( $object->pspReference );
		$item->set_event_code( $object->eventCode );
		$item->set_event_date( new DateTime( $object->eventDate ) );
		$item->set_merchant_account_code( $object->merchantAccountCode );
		$item->set_operations( $object->operations );
		$item->set_merchant_reference( $object->merchantReference );
		$item->set_payment_method( $object->paymentMethod );
		$item->set_success( filter_var( $object->success, FILTER_VALIDATE_BOOLEAN ) );

		return $item;
	}
}
