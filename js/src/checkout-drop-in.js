/* global AdyenCheckout, pronamicPayAdyenCheckout */

'use strict';

( async function () {

	/**
	 * Adyen Checkout.
	 */
	const configuration = {
		...pronamicPayAdyenCheckout.configuration,
		onPaymentCompleted: ( result, component ) => {
			let redirectUrl = new URL( pronamicPayAdyenCheckout.paymentRedirectUrl );

			redirectUrl.searchParams.set( 'resultCode', result.resultCode );

			window.location.href = redirectUrl;
		},
		onError: ( error, component ) => {
			dropinComponent.setStatus( 'error', { message: error.message } );
		}
	};

	const checkout = await AdyenCheckout( configuration );

	const dropinComponent = checkout.create( 'dropin' ).mount( '#pronamic-pay-checkout' );
} )();
