/* global AdyenCheckout, pronamicPayAdyenCheckout */

'use strict';

( async function () {

	/**
	 * Adyen Checkout.
	 */
	const configuration = {
		environment: pronamicPayAdyenCheckout.configuration.environment,
		clientKey: pronamicPayAdyenCheckout.configuration.clientKey,
		session: pronamicPayAdyenCheckout.session,
		onPaymentCompleted: ( result, component ) => {
			console.info( result, component );
		},
		onError: ( error, component ) => {
			console.error( error.name, error.message, error.stack, component );
		},
		paymentMethodsConfiguration: {
			card: {
				hasHolderName: true,
				holderNameRequired: true,
				billingAddressRequired: true
			}
		}
	};

	const checkout = await AdyenCheckout( configuration );

	const dropinComponent = checkout.create( 'dropin' ).mount( '#pronamic-pay-checkout' );
} )();
