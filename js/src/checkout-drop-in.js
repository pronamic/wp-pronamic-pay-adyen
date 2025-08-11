/* global AdyenCheckout, pronamicPayAdyenCheckout */

/**
 * Adyen checkout error definition.
 *
 * @see https://github.com/Adyen/adyen-web/blob/v5.57.0/packages/lib/src/core/Errors/AdyenCheckoutError.ts
 * @typedef AdyenCheckoutError
 * @type {Object}
 * @property {string} name    - Adyen error name.
 * @property {string} message - Adyen error message.
 */

'use strict';

( async function () {
	/**
	 * Adyen Checkout.
	 */
	const configuration = {
		...pronamicPayAdyenCheckout.configuration,
		onPaymentCompleted: ( result ) => {
			const redirectUrl = new URL(
				pronamicPayAdyenCheckout.paymentRedirectUrl
			);

			redirectUrl.searchParams.set( 'resultCode', result.resultCode );

			window.location.href = redirectUrl;
		},
		/**
		 * Error handler.
		 *
		 * @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error
		 * @see https://github.com/Adyen/adyen-web/blob/v5.15.0/packages/lib/src/core/Errors/AdyenCheckoutError.ts
		 * @see https://github.com/Adyen/adyen-web/blob/v5.15.0/packages/lib/src/components/UIElement.tsx#L115-L126
		 * @param {AdyenCheckoutError} error Adyen checkout error.
		 */
		onError: ( error ) => {
			if ( 'CANCEL' === error.name ) {
				return;
			}

			const redirectUrl = new URL(
				pronamicPayAdyenCheckout.paymentErrorUrl
			);

			redirectUrl.searchParams.set( 'name', error.name );
			redirectUrl.searchParams.set( 'message', error.message );

			window.location.href = redirectUrl;
		},
	};

	const { AdyenCheckout, Dropin } = window.AdyenWeb;

	const checkout = await AdyenCheckout( configuration );

	const dropin = new Dropin(
		checkout,
		{
			/**
			 * The `onSelect` and `onReady` events, since they're not generic events,
			 * should be defined when creating the Drop-in component.
			 *
			 * @see https://github.com/Adyen/adyen-web/issues/973#issuecomment-821148830
			 * @see https://docs.adyen.com/online-payments/migrate-to-web-4-0-0#dropin-configuration
			 * @param {Object} dropin Adyen dropin component.
			 */
			onSelect: ( dropin ) => {
				if ( pronamicPayAdyenCheckout.autoSubmit ) {
					dropin.submit();
				}
			},
		}
	);

	dropin.mount( '#pronamic-pay-checkout' );
} )();
