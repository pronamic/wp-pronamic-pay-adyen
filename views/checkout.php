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
			<div class="pronamic-pay-redirect-container alignleft">
				<div id="pronamic-pay-checkout"></div>
			</div>
		</div>
	</body>

	<script type="text/javascript">
		<?php

		/**
		 * Initiate the Adyen Checkout form.
		 *
		 * @link https://docs.adyen.com/developers/checkout/web-sdk
		 */

		?>
		var checkout = chckt.checkout(
			pronamicPayAdyenCheckout.paymentSession,
			'#pronamic-pay-checkout',
			pronamicPayAdyenCheckout.configObject
		);

		<?php

		/**
		 * Redirect once payment completes.
		 *
		 * @link https://docs.adyen.com/developers/checkout/web-sdk/customization/logic#beforecomplete
		 * @link https://developer.mozilla.org/en-US/docs/Web/API/URL
		 * @link https://caniuse.com/#search=URL
		 * @link https://stackoverflow.com/questions/486896/adding-a-parameter-to-the-url-with-javascript
		 * @link https://stackoverflow.com/questions/503093/how-do-i-redirect-to-another-webpage
		 */

		?>
		chckt.hooks.beforeComplete = function( node, paymentData ) {
			jQuery.post(
				pronamicPayAdyenCheckout.paymentsResultUrl,
				paymentData,
				function() {
					window.location.replace( pronamicPayAdyenCheckout.paymentReturnUrl );
				}
			);

			return false;
		};
	</script>
</html>
