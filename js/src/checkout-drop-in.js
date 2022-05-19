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
		/**
		 * Error handler.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error
		 * @link https://github.com/Adyen/adyen-web/blob/v5.15.0/packages/lib/src/core/Errors/AdyenCheckoutError.ts
		 * @link https://github.com/Adyen/adyen-web/blob/v5.15.0/packages/lib/src/components/UIElement.tsx#L115-L126
		 * @param AdyenCheckoutError error Adyen checkout error.
		 */
		onError: ( error ) => {
			if ( 'CANCEL' === error.name ) {
				return;
			}

			let redirectUrl = new URL( pronamicPayAdyenCheckout.paymentErrorUrl );

			redirectUrl.searchParams.set( 'name', error.name );
			redirectUrl.searchParams.set( 'message', error.message );

			window.location.href = redirectUrl;
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
