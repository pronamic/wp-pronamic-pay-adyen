<?php
/**
 * Payment response helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;

/**
 * Payment response helper
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class PaymentResponseHelper {
	/**
	 * Update payment to the payment response.
	 *
	 * @param Payment         $payment  Payment.
	 * @param PaymentResponse $response Response.
	 *
	 * @return void
	 */
	public static function update_payment( Payment $payment, PaymentResponse $response ) {
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

		$payment->save();
	}
}
