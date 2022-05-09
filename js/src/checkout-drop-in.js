/* global AdyenCheckout, pronamicPayAdyenCheckout */

'use strict';

( async function () {

	/**
	 * Adyen Checkout.
	 */
	const configuration = {
		environment: pronamicPayAdyenCheckout.configuration.environment,
		clientKey: pronamicPayAdyenCheckout.configuration.clientKey,
		session: pronamicPayAdyenCheckout.configuration.session,
		onPaymentCompleted: ( result, component ) => {
			console.info( result, component );
		},
		onError: ( error, component ) => {
			console.error( error.name, error.message, error.stack, component );
		}
	};

	const checkout = await AdyenCheckout( configuration );

	const dropinComponent = checkout.create( 'dropin' ).mount( '#pronamic-pay-checkout' );
} )();
