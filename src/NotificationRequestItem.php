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
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Notification request item
 *
 * @link https://docs.adyen.com/developers/api-reference/notifications-api#notificationrequestitem
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class NotificationRequestItem extends ResponseObject {
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
	 * This field is populated only in authorisation notifications.
	 *
	 * In case of HTTP POST notifications, the operation list is a sequence of comma-separated string values.
	 *
	 * @var array|null
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
	 * This field is populated only in authorisation notifications.
	 *
	 * @var string|null
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
	 * Construct notification request item.
	 *
	 * @link https://stackoverflow.com/questions/34468660/how-to-use-builder-pattern-with-all-parameters-as-mandatory
	 *
	 * @param Amount   $amount                Amount.
	 * @param string   $psp_reference         PSP reference.
	 * @param string   $event_code            Event code.
	 * @param DateTime $event_date            Event date.
	 * @param string   $merchant_account_code Merchant account code.
	 * @param string   $merchant_reference    Merchant reference.
	 * @param boolean  $success               Success.
	 */
	public function __construct(
		Amount $amount,
		$psp_reference,
		$event_code,
		DateTime $event_date,
		$merchant_account_code,
		$merchant_reference,
		$success
	) {
		$this->amount                = $amount;
		$this->psp_reference         = $psp_reference;
		$this->event_code            = $event_code;
		$this->event_date            = $event_date;
		$this->merchant_account_code = $merchant_account_code;
		$this->merchant_reference    = $merchant_reference;
		$this->success               = $success;
	}

	/**
	 * Get amount.
	 *
	 * @return Amount
	 */
	public function get_amount() {
		return $this->amount;
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
	 * Get event code.
	 *
	 * @return string
	 */
	public function get_event_code() {
		return $this->event_code;
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
	 * Get merchant account code.
	 *
	 * @return string
	 */
	public function get_merchant_account_code() {
		return $this->merchant_account_code;
	}

	/**
	 * Get operations.
	 *
	 * @return array|null
	 */
	public function get_operations() {
		return $this->operations;
	}

	/**
	 * Set operations.
	 *
	 * @param array|null $operations Operations.
	 * @return void
	 */
	public function set_operations( array $operations = null ) {
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
	 * Get payment method.
	 *
	 * @return string|null
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Set payment method.
	 *
	 * @param string|null $payment_method Payment method.
	 * @return void
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
	 * Create notification request item from object.
	 *
	 * @param object $object Object.
	 * @return NotificationRequestItem
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/notification-request-item.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$item = new self(
			Amount::from_object( $object->amount ),
			$object->pspReference,
			$object->eventCode,
			new DateTime( $object->eventDate ),
			$object->merchantAccountCode,
			$object->merchantReference,
			filter_var( $object->success, FILTER_VALIDATE_BOOLEAN )
		);

		if ( property_exists( $object, 'operations' ) ) {
			$item->set_operations( $object->operations );
		}

		if ( property_exists( $object, 'paymentMethod' ) ) {
			$item->set_payment_method( $object->paymentMethod );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$item->set_original_object( $object );

		return $item;
	}
}
