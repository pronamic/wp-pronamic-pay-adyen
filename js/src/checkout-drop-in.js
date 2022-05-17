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

	const dropinComponent = checkout.create( 'dropin', {
		/**
		 * The `onSelect` and `onReady` events, since they're not generic events,
		 * should be defined when creating the Drop-in component.
		 *
		 * @link https://github.com/Adyen/adyen-web/issues/973#issuecomment-821148830
		 * @link https://docs.adyen.com/online-payments/migrate-to-web-4-0-0#dropin-configuration
		 */
		onSelect: ( dropin ) => {
			if ( pronamicPayAdyenCheckout.autoSubmit ) {
				dropin.submit();
			}
		}
	} ).mount( '#pronamic-pay-checkout' );
} )();
