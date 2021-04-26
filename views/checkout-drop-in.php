<?php
/**
 * Redirect message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2021 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

		<title><?php esc_html_e( 'Checkout', 'pronamic_ideal' ); ?></title>

		<?php

		/**
		 * Triggers on Adyen checkout page.
		 *
		 * @link https://github.com/wp-pay-gateways/adyen#pronamic_pay_adyen_checkout_head
		 *
		 * @since 1.1
		 */
		do_action( 'pronamic_pay_adyen_checkout_head' );

		?>
	</head>

	<body>
		<div class="pronamic-pay-redirect-page">
			<div class="pronamic-pay-redirect-container">
				<div class="pp-page-section-container">
					<div class="pp-page-section-wrapper">
						<div id="pronamic-pay-checkout"></div>
					</div>
				</div>

				<?php if ( isset( $payment ) ) : ?>

					<div class="pp-page-section-container">
						<div class="pp-page-section-wrapper alignleft">
							<h1><?php esc_html_e( 'Payment', 'pronamic_ideal' ); ?></h1>

							<dl>
								<dt><?php esc_html_e( 'Date', 'pronamic_ideal' ); ?></dt>
								<dd><?php echo esc_html( $payment->get_date()->format_i18n() ); ?></dd>

								<dt><?php esc_html_e( 'Description', 'pronamic_ideal' ); ?></dt>
								<dd><?php echo esc_html( $payment->get_description() ); ?></dd>

								<dt><?php esc_html_e( 'Amount', 'pronamic_ideal' ); ?></dt>
								<dd><?php echo esc_html( $payment->get_total_amount()->format_i18n() ); ?></dd>
							</dl>
						</div>
					</div>

				<?php endif; ?>

			</div>
		</div>

		<?php

		wp_print_scripts( 'pronamic-pay-adyen-checkout-drop-in' );

		?>
	</body>
</html>
