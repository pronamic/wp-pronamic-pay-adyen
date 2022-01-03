<?php
/**
 * Payment details request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment details request
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments/details
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class PaymentDetailsRequest extends Request {
	/**
	 * Use this collection to submit the details that were returned as a result of the /payments call.
	 *
	 * @var object|null
	 */
	private $details;

	/**
	 * The paymentData value that you received in the response to the /payments call.
	 *
	 * @var string|null
	 */
	private $payment_data;

	/**
	 * Get details.
	 *
	 * @return object|null
	 */
	public function get_details() {
		return $this->details;
	}

	/**
	 * Set details.
	 *
	 * @param object|null $details Details.
	 * @return void
	 */
	public function set_details( $details ) {
		$this->details = $details;
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
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'details'     => $this->get_details(),
				'paymentData' => $this->get_payment_data(),
			)
		);

		$object = (object) $properties;

		return $object;
	}
}
