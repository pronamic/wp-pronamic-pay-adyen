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
			dropinComponent.setStatus( 'error', { message: error.message } );
		}
	};

	const checkout = await AdyenCheckout( configuration );

	const dropinComponent = checkout.create( 'dropin' ).mount( '#pronamic-pay-checkout' );
} )();
