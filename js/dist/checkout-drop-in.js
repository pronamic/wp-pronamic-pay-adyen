/* global AdyenCheckout, pronamicPayAdyenCheckout */
'use strict';

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

_asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee() {
  var configuration, checkout, dropinComponent;
  return regeneratorRuntime.wrap(function _callee$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          /**
           * Adyen Checkout.
           */
          configuration = {
            environment: pronamicPayAdyenCheckout.configuration.environment,
            clientKey: pronamicPayAdyenCheckout.configuration.clientKey,
            session: pronamicPayAdyenCheckout.session,
            onPaymentCompleted: function onPaymentCompleted(result, component) {
              console.info(result, component);
            },
            onError: function onError(error, component) {
              console.error(error.name, error.message, error.stack, component);
            },
            paymentMethodsConfiguration: {
              card: {
                hasHolderName: true,
                holderNameRequired: true,
                billingAddressRequired: true
              }
            }
          };
          _context.next = 3;
          return AdyenCheckout(configuration);

        case 3:
          checkout = _context.sent;
          dropinComponent = checkout.create('dropin').mount('#pronamic-pay-checkout');

        case 5:
        case "end":
          return _context.stop();
      }
    }
  }, _callee);
}))();
//# sourceMappingURL=checkout-drop-in.js.map