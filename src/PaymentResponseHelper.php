<?php
/**
 * Payment response helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\FailureReason;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Payment response helper class
 */
class PaymentResponseHelper {
	/**
	 * Update payment to the payment response.
	 *
	 * @param Payment                 $payment  Payment.
	 * @param AbstractPaymentResponse $response Response.
	 * @return void
	 */
	public static function update_payment( Payment $payment, AbstractPaymentResponse $response ) {
		// Add note.
		$note = sprintf(
			'<p>%s</p>',
			sprintf(
				/* translators: %s: payment provider name */
				__( 'Verified payment result.', 'pronamic_ideal' ),
				__( 'Adyen', 'pronamic_ideal' )
			)
		);

		$json = wp_json_encode( $response->get_json(), JSON_PRETTY_PRINT );

		if ( false !== $json ) {
			$note .= sprintf(
				'<pre>%s</pre>',
				$json
			);
		}

		$payment->add_note( $note );

		// PSP reference.
		$psp_reference = $response->get_psp_reference();

		if ( null !== $psp_reference ) {
			$payment->set_transaction_id( $psp_reference );
		}

		// Result code.
		$result_code = $response->get_result_code();

		$status = ResultCode::transform( $result_code );

		if ( null !== $status ) {
			$payment->set_status( $status );
		}

		// Refusal reason.
		$refusal_reason = $response->get_refusal_reason();

		if ( null !== $refusal_reason ) {
			$failure_reason = new FailureReason();

			$message = sprintf(
				/* translators: %s: refusal reason */
				__( 'The payment has been refused. (%s)', 'pronamic_ideal' ),
				$refusal_reason
			);

			$failure_reason->set_message( $message );

			$payment->set_failure_reason( $failure_reason );
		}

		$payment->save();
	}
}
