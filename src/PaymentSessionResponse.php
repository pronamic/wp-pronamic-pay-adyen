<?php
/**
 * Payment session response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment session response class
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 */
class PaymentSessionResponse extends ResponseObject {
	/**
	 * A unique identifier of the session.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The payment session data you need to pass to your front end.
	 *
	 * @var string|null
	 */
	private $data;

	/**
	 * Construct payment session response object.
	 *
	 * @param string $id   A unique identifier of the session.
	 * @param string $data The payment session data you need to pass to your front end.
	 */
	public function __construct( $id, $data ) {
		$this->id   = $id;
		$this->data = $data;
	}

	/**
	 * Get unique identifier of the session.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get payment session data.
	 *
	 * @return string|null
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Create payment session response from object.
	 *
	 * @param object $value Object.
	 * @return PaymentSessionResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $value ) {
		$validator = new Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-session-response.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$data = new ObjectAccess( $value );

		return new self(
			$data->get_property( 'id' ),
			$data->get_property( 'sessionData' )
		);
	}
}
