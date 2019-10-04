<?php
/**
 * Payment result helper test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\Payment;
use WP_UnitTestCase;

/**
 * Payment result helper test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentResultHelperTest extends WP_UnitTestCase {
	/**
	 * Test payment result response.
	 */
	public function test_update_payment() {
		$post_id = self::factory()->post->create(
			array(
				'post_type'  => 'pronamic_payment',
				'post_title' => 'Adyen - test',
			)
		);

		$payment = get_pronamic_payment( $post_id );

		$payment_result_response = new PaymentResultResponse( 'YOUR_MERCHANT_ACCOUNT', PaymentMethodType::IDEAL, 'nl_NL' );

		$payment_result_response->set_psp_reference( '1234567890123456' );
		$payment_result_response->set_result_code( ResultCode::AUTHORIZED );

		PaymentResultHelper::update_payment( $payment, $payment_result_response );

		$this->assertEquals( '1234567890123456', $payment->get_transaction_id() );
		$this->assertEquals( PaymentStatus::SUCCESS, $payment->get_status() );

		$comments = get_comments(
			array(
				'post_id' => $post_id,
				'type'    => 'payment_note',
			)
		);

		$this->assertCount( 1, $comments );

		$comment = array_pop( $comments );

		$this->assertContains( 'Verified payment result.', $comment->comment_content );
	}
}
