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

use InvalidArgumentException;

/**
 * Payment session response
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentSessionResponse {
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
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->paymentSession ) ) {
			throw new InvalidArgumentException( 'Object must contain `paymentSession` property.' );
		}

		return new self( $object->paymentSession );
	}
}
