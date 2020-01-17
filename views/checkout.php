<?php
/**
 * Redirect message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
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

		<div id="dropin"></div>
	</body>

	<script type="text/javascript">
		const configuration = {
			locale: "en-US", // The shopper's locale. For a list of supported locales, see https://docs.adyen.com/checkout/components-web/localization-components.
			environment: "test", // When you're ready to accept live payments, change the value to one of our live environments https://docs.adyen.com/checkout/drop-in-web#testing-your-integration.  
			originKey: "pub.v2.8015393300271166.aHR0cDovL3BheS50ZXN0.Tm6amiCPrJ1bim8yYjmmVFG68Oa5RIIShvqrYbDIbww", // Your website's Origin Key. To find out how to generate one, see https://docs.adyen.com/user-management/how-to-get-an-origin-key. 
			paymentMethodsResponse: pronamicPayAdyenCheckout.paymentMethodsResponse // The payment methods response returned in step 1.
		};

		const checkout = new AdyenCheckout( configuration );

		const dropin = checkout.create('dropin', {
		  paymentMethodsConfiguration: {
		    applepay: { // Example required configuration for Apple Pay
		      configuration: {
		        merchantName: 'Adyen Test merchant', // Name to be displayed on the form
		        merchantIdentifier: 'adyen.test.merchant' // Your Apple merchant identifier as described in https://developer.apple.com/documentation/apple_pay_on_the_web/applepayrequest/2951611-merchantidentifier
		      },
		      onValidateMerchant: (resolve, reject, validationURL) => {
		      // Call the validation endpoint with validationURL.
		      // Call resolve(MERCHANTSESSION) or reject() to complete merchant validation.
		      }
		    },
		    paywithgoogle: { // Example required configuration for Google Pay
		      environment: "TEST", // Change this to PRODUCTION when you're ready to accept live Google Pay payments
		      configuration: {
		       gatewayMerchantId: "YourCompanyOrMerchantAccount", // Your Adyen merchant or company account name. Remove this field in TEST.
		       merchantIdentifier: "12345678910111213141" // Required for PRODUCTION. Remove this field in TEST. Your Google Merchant ID as described in https://developers.google.com/pay/api/web/guides/test-and-deploy/deploy-production-environment#obtain-your-merchantID
		      }
		    },
		    card: { // Example optional configuration for Cards
		      hasHolderName: true,
		      holderNameRequired: true,
		      enableStoreDetails: true,
		      hideCVC: false, // Change this to true to hide the CVC field for stored cards
		      name: 'Credit or debit card'
		    }
		  },
		  onSubmit: (state, dropin) => {
		    makePayment(state.data)
		      // Your function calling your server to make the /payments request
		      .then(action => {
		        dropin.handleAction(action);
		        // Drop-in handles the action object from the /payments response
		      })
		      .catch(error => {
		        throw Error(error);
		      });
		  },
		  onAdditionalDetails: (state, dropin) => {
		    makeDetailsCall(state.data)
		      // Your function calling your server to make a /payments/details request
		      .then(action => {
		        dropin.handleAction(action);
		        // Drop-in handles the action object from the /payments/details response
		      })
		      .catch(error => {
		        throw Error(error);
		      });
		  }
		})
		.mount('#dropin');
	</script>

	<script type="text/javascript">
		<?php

		/**
		 * Initiate the Adyen Checkout form.
		 *
		 * @link https://docs.adyen.com/developers/checkout/web-sdk
		 */

		?>
		/*
		var checkout = chckt.checkout(
			pronamicPayAdyenCheckout.paymentSession,
			'#pronamic-pay-checkout',
			pronamicPayAdyenCheckout.configObject
		);
		*/

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
		/*
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
		*/
	</script>
</html>
