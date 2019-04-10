<?php
/**
 * Payment session response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Payment session response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentSessionResponse extends ResponseObject {
	/**
	 * The encoded payment session that you need to pass to the SDK.
	 *
	 * @var string
	 */
	private $payment_session;

	/**
	 * Construct payment session response object.
	 *
	 * @param string $payment_session The encoded payment session.
	 */
	public function __construct( $payment_session ) {
		$this->payment_session = $payment_session;
	}

	/**
	 * Get payment session.
	 *
	 * @return string
	 */
	public function get_payment_session() {
		return $this->payment_session;
	}

	/**
	 * Create payment session repsonse from object.
	 *
	 * @param object $object Object.
	 * @return PaymentSessionResponse
	 * @throws ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-session-response.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.
		return new self( $object->paymentSession );
	}
}
