<?php
/**
 * Redirect message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>
	<head>
		<?php require __DIR__ . '/head.php'; ?>
	</head>

	<body>
		<div class="pronamic-pay-redirect-page">
			<div class="pronamic-pay-redirect-container">

				<div class="pp-page-section-container">
					<div class="pp-page-section-wrapper">
						<div id="pronamic-pay-checkout"></div>
					</div>
				</div>

				<div class="pp-page-section-container">
					<div class="pp-page-section-wrapper alignleft">
						<h1><?php esc_html_e( 'Payment', 'pronamic_ideal' ); ?></h1>

						<dl>
							<dt><?php esc_html_e( 'Date', 'pronamic_ideal' ); ?></dt>
							<dd><?php echo esc_html( $payment->get_date()->format_i18n() ); ?></dd>

							<dt><?php esc_html_e( 'Description', 'pronamic_ideal' ); ?></dt>
							<dd><?php echo esc_html( (string) $payment->get_description() ); ?></dd>

							<dt><?php esc_html_e( 'Amount', 'pronamic_ideal' ); ?></dt>
							<dd><?php echo esc_html( $payment->get_total_amount()->format_i18n() ); ?></dd>
						</dl>
					</div>
				</div>

			</div>
		</div>

		<?php

		wp_print_scripts( 'pronamic-pay-adyen-checkout-drop-in' );

		?>
	</body>
</html>
