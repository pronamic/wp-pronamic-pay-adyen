<?php
/**
 * Action information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Action information
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class ActionInformation extends ResponseObject {
	/**
	 * When the redirect URL must be accessed via POST, use this data to post to the redirect URL.
	 *
	 * @var object|null
	 */
	private $data;

	/**
	 * The web method that you must use to access the redirect URL.
	 *
	 * Possible values: GET, POST.
	 *
	 * @var string|null
	 */
	private $method;

	/**
	 * When non-empty, contains a value that you must submit to the /payments/details endpoint. In some cases, required for polling.
	 *
	 * @var string|null
	 */
	private $payment_data;

	/**
	 * Specifies the payment method.
	 *
	 * @var string|null
	 */
	private $payment_method_type;

	/**
	 * A token to pass to the 3DS2 Component to get the fingerprint/challenge.
	 *
	 * @var string|null
	 */
	private $token;

	/**
	 * Enum that specifies the action that needs to be taken by the client.
	 *
	 * @var string|null
	 */
	private $type;

	/**
	 * The URL, to which you must redirect a shopper to complete a payment
	 *
	 * @var string|null
	 */
	private $url;

	/**
	 * Get data.
	 *
	 * @return object|null
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set data.
	 *
	 * @param object|null $data Data.
	 * @return void
	 */
	public function set_data( $data ) {
		$this->data = $data;
	}

	/**
	 * Get method.
	 *
	 * @return string|null
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Set method.
	 *
	 * @param string|null $method Method.
	 * @return void
	 */
	public function set_method( $method ) {
		$this->method = $method;
	}

	/**
	 * Get payment data.
	 *
	 * @return string|null
	 */
	public function get_payment_data() {
		return $this->payment_data;
	}

	/**
	 * Set payment data.
	 *
	 * @param string|null $payment_data Payment data.
	 * @return void
	 */
	public function set_payment_data( $payment_data ) {
		$this->payment_data = $payment_data;
	}

	/**
	 * Get payment method type.
	 *
	 * @return string|null
	 */
	public function get_payment_method_type() {
		return $this->payment_method_type;
	}

	/**
	 * Set payment method type.
	 *
	 * @param string|null $payment_method_type Payment method type.
	 * @return void
	 */
	public function set_payment_method_type( $payment_method_type ) {
		$this->payment_method_type = $payment_method_type;
	}

	/**
	 * Get token.
	 *
	 * @return string|null
	 */
	public function get_token() {
		return $this->token;
	}

	/**
	 * Set token.
	 *
	 * @param string|null $token Token.
	 * @return void
	 */
	public function set_token( $token ) {
		$this->token = $token;
	}

	/**
	 * Get type.
	 *
	 * @return string|null
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set type.
	 *
	 * @param string|null $type Type.
	 * @return void
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Get URL.
	 *
	 * @return string|null
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Set URL.
	 *
	 * @param string|null $url URL.
	 * @return void
	 */
	public function set_url( $url ) {
		$this->url = $url;
	}

	/**
	 * Create action information from object.
	 *
	 * @param object $object Object.
	 * @return ActionInformation
	 * @throws \JsonSchema\Exception\ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/action.json' ),
			),
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		$action = new self();

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		if ( isset( $object->data ) ) {
			$action->set_data( $object->data );
		}

		if ( isset( $object->method ) ) {
			$action->set_method( $object->method );
		}

		if ( isset( $object->paymentData ) ) {
			$action->set_payment_data( $object->paymentData );
		}

		if ( isset( $object->paymentMethodType ) ) {
			$action->set_payment_method_type( $object->paymentMethodType );
		}

		if ( isset( $object->token ) ) {
			$action->set_token( $object->token );
		}

		if ( isset( $object->type ) ) {
			$action->set_type( $object->type );
		}

		if ( isset( $object->url ) ) {
			$action->set_url( $object->url );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		$action->set_original_object( $object );

		return $action;
	}
}
