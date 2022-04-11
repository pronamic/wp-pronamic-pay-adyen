"use strict";

/* global AdyenCheckout, pronamicPayAdyenCheckout */
(function () {
  'use strict';
  /**
   * Send request using Fetch API.
   */

  var send_request = function send_request(url, data) {
    return fetch(url, {
      method: 'POST',
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });
  };
  /**
   * Parse JSON and check response status.
   *
   * @param response Fetch request response.
   * @link https://stackoverflow.com/questions/47267221/fetch-response-json-and-response-status
   */


  var validate_response = function validate_response(response) {
    return response.json().then(function (data) {
      if (200 !== response.status) {
        throw new Error(data.message, {
          cause: data
        });
      }

      return data;
    });
  };
  /**
   * Process response.
   *
   * @param data Object from JSON response.
   */


  var process_response = function process_response(data) {
    // Handle action object.
    if (data.action) {
      dropin.handleAction(data.action);
      return;
    } // Handle result code.


    if (data.resultCode) {
      paymentResult(data);
    }
  };
  /**
   * Handle error.
   *
   * @param error
   */


  var handle_error = function handle_error(error) {
    // Check syntax error name.
    if ('SyntaxError' === error.name) {
      error.message = pronamicPayAdyenCheckout.syntaxError;
    } // Show error message.


    dropin.setStatus('error', {
      message: error.message
    });
  };
  /**
   * Get payment methods configuration.
   *
   * @return object
   */


  var getPaymentMethodsConfiguration = function getPaymentMethodsConfiguration() {
    /**
     * Complement Apple Pay configuration with `onValidateMerchant`
     * callback when using your own Apple Pay certificate.
     * 
     * @link https://github.com/pronamic/wp-pronamic-pay-adyen/issues/5#issue-1154083692
     */
    if (pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay && pronamicPayAdyenCheckout.applePayMerchantValidationUrl) {
      pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay.onValidateMerchant = function (resolve, reject, validationUrl) {
        send_request(pronamicPayAdyenCheckout.applePayMerchantValidationUrl, {
          validation_url: validationUrl
        }).then(validate_response).then(function (data) {
          // Handle Apple error.
          if (data.statusMessage) {
            throw new Error(data.statusMessage, {
              cause: data
            });
          }

          resolve(data);
        }).catch(function (error) {
          handle_error(error); // Reject to dismiss Apple Pay overlay.

          reject(error);
        });
      };
    } // Compliment PayPal configuration.


    if (pronamicPayAdyenCheckout.paymentMethodsConfiguration.paypal) {
      pronamicPayAdyenCheckout.paymentMethodsConfiguration.paypal.onCancel = function (data, dropin) {
        dropin.setStatus('ready');
      };
    }

    return pronamicPayAdyenCheckout.paymentMethodsConfiguration;
  };
  /**
   * Adyen Checkout.
   */


  var checkout = new AdyenCheckout(pronamicPayAdyenCheckout.configuration);
  var dropin = checkout.create('dropin', {
    paymentMethodsConfiguration: getPaymentMethodsConfiguration(),
    onSelect: function onSelect(dropin) {
      var configuration = pronamicPayAdyenCheckout.configuration;

      if (false === configuration.showPayButton) {
        dropin.submit();
      }
    },
    onSubmit: function onSubmit(state) {
      // Set loading status to prevent duplicate submits.
      dropin.setStatus('loading');
      send_request(pronamicPayAdyenCheckout.paymentsUrl, state.data).then(validate_response).then(process_response).catch(handle_error);
    },
    onAdditionalDetails: function onAdditionalDetails(state) {
      send_request(pronamicPayAdyenCheckout.paymentsDetailsUrl, state.data).then(validate_response).then(process_response).catch(handle_error);
    }
  }).mount('#pronamic-pay-checkout');
  /**
   * Handle payment result.
   *
   * @param response Object from JSON response data.
   * @link https://docs.adyen.com/checkout/drop-in-web#step-6-present-payment-result
   */

  var paymentResult = function paymentResult(response) {
    switch (response.resultCode) {
      case 'Authorised':
        // The payment was successful.
        dropin.setStatus('success', {
          message: pronamicPayAdyenCheckout.paymentAuthorised
        });
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
        if (response.refusalReason) {
          if (pronamicPayAdyenCheckout.refusalRedirectUrl) {
            window.location.href = pronamicPayAdyenCheckout.refusalRedirectUrl;
          }

          throw new Error(response.refusalReason);
        }

        throw new Error(pronamicPayAdyenCheckout.unknownError);

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
        if (pronamicPayAdyenCheckout.refusalRedirectUrl) {
          window.location.href = pronamicPayAdyenCheckout.refusalRedirectUrl;
        }
        /*
         * Inform the shopper that the payment was refused. Ask the shopper to try the payment again using a different payment method or card.
         */


        if (response.refusalReason) {
          throw new Error(pronamicPayAdyenCheckout.paymentRefused + ' (' + response.refusalReason + ')');
        }

        throw new Error(pronamicPayAdyenCheckout.paymentRefused);

      case 'Received':
        // For some payment methods, it can take some time before the final status of the payment is known.
        dropin.setStatus('success', {
          message: pronamicPayAdyenCheckout.paymentReceived
        });
        /*
         * Inform the shopper that you've received their order, and are waiting for the payment to clear.
         */

        setTimeout(function () {
          window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;
        }, 3000);
        break;
    }
  };
})();
//# sourceMappingURL=checkout-drop-in.js.map