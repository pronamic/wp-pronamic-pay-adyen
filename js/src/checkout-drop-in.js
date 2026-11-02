/* global pronamicPayAdyen */

/**
 * Adyen checkout error definition.
 *
 * @see https://github.com/Adyen/adyen-web/blob/v5.57.0/packages/lib/src/core/Errors/AdyenCheckoutError.ts
 * @typedef AdyenCheckoutError
 * @type {Object}
 * @property {string} name    - Adyen error name.
 * @property {string} message - Adyen error message.
 */

import { AdyenCheckout, Dropin } from '@adyen/adyen-web/auto';
import '@adyen/adyen-web/styles/adyen.css';
import './checkout-drop-in.css';

/**
 * Render error.
 *
 * Replaces the drop-in with the error, so the customer knows what happened
 * instead of being redirected without explanation.
 *
 * @param {AdyenCheckoutError} error     Adyen checkout error.
 * @param {string|null}        returnUrl Payment return URL.
 */
function renderError( error, returnUrl ) {
	const i18n = pronamicPayAdyen.i18n;

	const container = document.getElementById( 'pronamic-pay-checkout' );

	const title = document.createElement( 'h1' );
	title.textContent = i18n.errorTitle;

	const text = document.createElement( 'p' );
	text.textContent = i18n.errorText;

	const list = document.createElement( 'dl' );

	[
		[ i18n.errorMessage, error.message ],
		[ i18n.errorCode, error.name ],
	].forEach( ( [ term, description ] ) => {
		const dt = document.createElement( 'dt' );
		dt.textContent = term;

		const dd = document.createElement( 'dd' );
		dd.textContent = description;

		list.append( dt, dd );
	} );

	const wrapper = document.createElement( 'div' );
	wrapper.className = 'pronamic-pay-adyen-error alignleft';
	wrapper.append( title, text, list );

	if ( returnUrl ) {
		const link = document.createElement( 'a' );
		link.className = 'pronamic-pay-btn';
		link.href = returnUrl;
		link.textContent = i18n.continue;

		wrapper.append( link );
	}

	container.replaceChildren( wrapper );
}

( async function () {
	/**
	 * Adyen Checkout configuration.
	 */
	const checkoutConfiguration = {
		...pronamicPayAdyen.checkoutConfiguration,
		onPaymentCompleted: ( result ) => {
			const redirectUrl = new URL( pronamicPayAdyen.paymentRedirectUrl );

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
		onError: async ( error ) => {
			if ( 'CANCEL' === error.name ) {
				return;
			}

			let returnUrl = null;

			try {
				const response = await fetch(
					pronamicPayAdyen.paymentErrorUrl,
					{
						method: 'POST',
						body: new URLSearchParams( {
							name: error.name,
							message: error.message,
						} ),
					}
				);

				if ( response.ok ) {
					const data = await response.json();

					returnUrl = data.return_url;
				}
			} catch {
				// Show the error anyway.
			}

			renderError( error, returnUrl );
		},
	};

	const checkout = await AdyenCheckout( checkoutConfiguration );

	/**
	 * Adyen Dropin configuration.
	 */
	const dropinConfiguration = {
		...pronamicPayAdyen.dropinConfiguration,
		/**
		 * The `onSelect` and `onReady` events, since they're not generic events,
		 * should be defined when creating the Drop-in component.
		 *
		 * @see https://github.com/Adyen/adyen-web/issues/973#issuecomment-821148830
		 * @see https://docs.adyen.com/online-payments/migrate-to-web-4-0-0#dropin-configuration
		 * @param {Object} paymentMethod Adyen payment method component.
		 */
		onSelect: ( paymentMethod ) => {
			if ( pronamicPayAdyen.autoSubmit ) {
				paymentMethod.submit();
			}
		},
	};

	const dropin = new Dropin( checkout, dropinConfiguration );

	dropin.mount( '#pronamic-pay-checkout' );
} )();
