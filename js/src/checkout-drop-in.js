/* global AdyenCheckout, pronamicPayAdyenCheckout */
( function () {
	'use strict';

	const checkout = new AdyenCheckout( pronamicPayAdyenCheckout.configuration );

	const validate_http_status = response => {
		if ( response.status >= 200 && response.status < 300 ) {
			return Promise.resolve( response );
		}

		return Promise.reject( new Error( response.statusText ) );
	};

	const get_json = response => response.json();

	if ( pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay ) {
		pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay.onValidateMerchant = ( resolve, reject, validationUrl ) => {
			send_request( pronamicPayAdyenCheckout.applePayMerchantValidationUrl, { validation_url: validationUrl } )
				.then( validate_http_status )
				.then( get_json )
				.then( data => {
					// Handle Pronamic Pay error.
					if ( data.error ) {
						return Promise.reject( new Error( data.error ) );
					}

					// Handle Adyen error.
					if ( data.statusMessage ) {
						return Promise.reject( new Error( data.statusMessage ) );
					}

					return resolve( data );
				} )
				.catch( error => {
					dropin.setStatus( 'error', { message: error.message } );

					setTimeout( () => {dropin.setStatus( 'ready' );}, 5000 );

					return reject();
				} );
		};
	}

	if ( pronamicPayAdyenCheckout.paymentMethodsConfiguration.paypal ) {
		pronamicPayAdyenCheckout.paymentMethodsConfiguration.paypal.onCancel = ( data, dropin ) => {
			dropin.setStatus( 'ready' );
		};
	}

	/**
	 * Parse JSON and check response status.
	 * 
	 * @link https://stackoverflow.com/questions/47267221/fetch-response-json-and-response-status
	 */
	const validate_response = response => {
		return response.json().then( data => {
			if ( 200 !== response.status ) {
				throw new Error( data.message, {
					cause: data
				} );
			}

			return data;
		} );
	};

	const dropin = checkout.create( 'dropin', {
		paymentMethodsConfiguration: pronamicPayAdyenCheckout.paymentMethodsConfiguration,
		onSelect: ( dropin ) => {
			let configuration = pronamicPayAdyenCheckout.configuration;

			if ( Object.prototype.hasOwnProperty.call( configuration, 'showPayButton' ) && false === configuration.showPayButton ) {
				dropin.submit();
			}
		},
		onSubmit: ( state, dropin ) => {
			// Set loading status to prevent duplicate submits.
			dropin.setStatus( 'loading' );

			send_request( pronamicPayAdyenCheckout.paymentsUrl, state.data )
			.then( validate_response )
			.then( data => {
				// Handle action object.
				if ( data.action ) {
					dropin.handleAction( data.action );

					return;
				}

				// Handle result code.
				if ( data.resultCode ) {
					paymentResult( data );
				}
			} )
			.catch( error => {
				dropin.setStatus( 'error', { message: error.message } );
			} );
		},
		onAdditionalDetails: ( state, dropin ) => {
			send_request( pronamicPayAdyenCheckout.paymentsDetailsUrl, state.data )
			.then( validate_http_status )
			.then( get_json )
			.then( data => {
				// Handle action object.
				if ( data.action ) {
					dropin.handleAction( data.action );
				}

				// Handle result code.
				if ( data.resultCode ) {
					paymentResult( data );
				}
			} )
			.catch( error => {
				throw Error( error );
			} );
		}
	} )
	.mount( '#pronamic-pay-checkout' );

	const send_request = ( url, data ) => {
		return fetch(
			url,
			{
				method: 'POST',
				cache: 'no-cache',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( data )
			}
		);
	};

	const paymentResult = ( response ) => {
		/*
		 * Handle payment result
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web#step-6-present-payment-result
		 */
		switch ( response.resultCode ) {
			case 'Authorised':
				// The payment was successful.
				dropin.setStatus( 'success', { message: pronamicPayAdyenCheckout.paymentAuthorised } );

				/*
				 * Inform the shopper that the payment was successful.
				 */
				window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;

				break;
			case 'Error':
				// Inform the shopper that there was an error processing their payment.

				/*
				 * You'll receive a `refusalReason` in the same response, indicating the cause of the error.
				 */
				if ( response.refusalReason ) {
					dropin.setStatus( 'error', { message: response.refusalReason } );
				}

				break;
			case 'Pending':
				// The shopper has completed the payment but the final result is not yet known.

				/*
				 * Inform the shopper that you've received their order, and are waiting for the payment to be completed.
				 */
				window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;

				break;
			case 'PresentToShopper':
				// Present the voucher or the QR code to the shopper.

				/*
				 * For a voucher payment method, inform the shopper that you are waiting for their payment. You will receive the final result of the payment in an AUTHORISATION notification.
				 *
				 * For a qrCode payment method, wait for the AUTHORISATION notification before presenting the payment result to the shopper.
				 *
				 * @todo
				 */
				break;
			case 'Refused':
				// The payment was refused.

				/*
				 * Inform the shopper that the payment was refused. Ask the shopper to try the payment again using a different payment method or card.
				 */
				dropin.setStatus( 'error', { message: pronamicPayAdyenCheckout.paymentRefused + ' (' + response.refusalReason + ')' } );

				setTimeout(
					() => {
						dropin.setStatus( 'ready' );
					},
					8000
				);

				break;
			case 'Received':
				// For some payment methods, it can take some time before the final status of the payment is known.
				dropin.setStatus( 'success', { message: pronamicPayAdyenCheckout.paymentReceived } );

				/*
				 * Inform the shopper that you've received their order, and are waiting for the payment to clear.
				 */
				setTimeout(
					() => {
						window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;
					},
					3000
				);

				break;
		}
	};
} )();
