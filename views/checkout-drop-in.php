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
				<div id="pronamic-pay-adyen-drop-in"></div>
			</div>
		</div>
	</body>

	<script type="text/javascript">
		const checkout = new AdyenCheckout( pronamicPayAdyenCheckout.configuration );

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
			onSubmit: ( state, dropin ) => {
                // Your function calling your server to make the `/payments` request.
                makePayment( state.data )
                .then( response => {
                    console.log( response );

                    // Drop-in handles the action object from the `/payments` response.
                    if ( typeof response.action != "undefined" ) {
                        dropin.handleAction( response.action );
                    }

                    // Handle result codes.
                    if ( typeof response.resultCode != "undefined" ) {
                        switch ( response.resultCode ) {
                            case 'Received' :
                                dropin.setStatus('success', { message: 'The order has been received and we are waiting for the payment to clear.' } );

                                // @todo redirect to payment pending status page.

                                break;
                        }
                    }

                    return response;
                } )
                .catch( error => {
                    throw Error( error );
                } );
			},
			onAdditionalDetails: ( state, dropin ) => {
				makeDetailsCall( state.data )
				// Your function calling your server to make a /payments/details request
				.then( action => {
					dropin.handleAction( action );
					// Drop-in handles the action object from the /payments/details response
				} )
				.catch( error => {
					throw Error(error);
				} );
			}
		} )
		.mount( '#pronamic-pay-adyen-drop-in' );

		async function makePayment( data ) {
			console.log( data );

			const response = await fetch( pronamicPayAdyenCheckout.paymentsUrl, {
				method: 'POST',
				cache: 'no-cache',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( data )
			} );

			return await response.json();
		}
	</script>
</html>
