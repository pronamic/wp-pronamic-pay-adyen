/* global AdyenCheckout, pronamicPayAdyenCheckout */
'use strict';

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

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
          configuration = _objectSpread(_objectSpread({}, pronamicPayAdyenCheckout.configuration), {}, {
            onPaymentCompleted: function onPaymentCompleted(result, component) {
              var redirectUrl = new URL(pronamicPayAdyenCheckout.paymentRedirectUrl);
              redirectUrl.searchParams.set('resultCode', result.resultCode);
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
            onError: function onError(error) {
              if ('CANCEL' === error.name) {
                return;
              }

              var redirectUrl = new URL(pronamicPayAdyenCheckout.paymentErrorUrl);
              redirectUrl.searchParams.set('name', error.name);
              redirectUrl.searchParams.set('message', error.message);
              window.location.href = redirectUrl;
            }
          });
          _context.next = 3;
          return AdyenCheckout(configuration);

        case 3:
          checkout = _context.sent;
          dropinComponent = checkout.create('dropin', {
            /**
             * The `onSelect` and `onReady` events, since they're not generic events,
             * should be defined when creating the Drop-in component.
             *
             * @link https://github.com/Adyen/adyen-web/issues/973#issuecomment-821148830
             * @link https://docs.adyen.com/online-payments/migrate-to-web-4-0-0#dropin-configuration
             */
            onSelect: function onSelect(dropin) {
              if (pronamicPayAdyenCheckout.autoSubmit) {
                dropin.submit();
              }
            }
          }).mount('#pronamic-pay-checkout');

        case 5:
        case "end":
          return _context.stop();
      }
    }
  }, _callee);
}))();
//# sourceMappingURL=checkout-drop-in.js.map