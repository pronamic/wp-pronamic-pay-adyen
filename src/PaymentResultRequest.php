<?php
/**
 * Payment result request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment result request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments/result
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResultRequest extends Request {
	/**
	 * Encrypted and signed payment result data. You should receive this value from the Checkout SDK after the shopper completes the payment.
	 *
	 * @var string
	 */
	private $payload;

	/**
	 * Construct a payment result request object.
	 *
	 * @param string $payload Payload.
	 */
	public function __construct( $payload ) {
		$this->payload = $payload;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		return (object) array(
			'payload' => $this->payload,
		);
	}
}
