/* global AdyenCheckout, pronamicPayAdyenCheckout */

'use strict';

( async function () {

	/**
	 * Adyen Checkout.
	 */
	const configuration = {
		...pronamicPayAdyenCheckout.configuration,
		onPaymentCompleted: ( result, component ) => {
			window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;
		},
		onError: ( error, component ) => {
			console.error( error.name, error.message, error.stack, component );
		}
	};

	const checkout = await AdyenCheckout( configuration );

	const dropinComponent = checkout.create( 'dropin' ).mount( '#pronamic-pay-checkout' );
} )();
