<?php
/**
 * Redirect message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />

		<title><?php esc_html_e( 'Checkout', 'pronamic_ideal' ); ?></title>

		<?php

		wp_print_styles( 'pronamic-pay-redirect' );

		wp_print_scripts( 'pronamic-pay-adyen-checkout' );

		?>
	</head>

	<body>
		<div class="pronamic-pay-redirect-page">
			<div class="pronamic-pay-redirect-container alignleft">
				<div id="pronamic-pay-checkout"></div>
			</div>
		</div>
	</body>

	<script type="text/javascript">
		// Initiate the Adyen Checkout form.
		var checkout = chckt.checkout(
			pronamicPayAdyenCheckout.paymentSession,
			'#pronamic-pay-checkout',
			pronamicPayAdyenCheckout.configObject
		);

		// Redirect once payment completes.
		chckt.hooks.beforeComplete = function ( node, paymentData ) {
			if ( "undefined" !== paymentData.payload ) {
				window.location.href = "<?php echo esc_url_raw( $payment->get_return_url() ); ?>&payload=" + encodeURIComponent( paymentData.payload );

				return false;
			}
		};
	</script>
</html>
