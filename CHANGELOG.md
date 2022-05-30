# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

## [4.0.0] - 2022-05-30
### Removed
- ⚠️ Removed the web SDK gateway, which functioned without origin key, manual [migration to client key](https://docs.adyen.com/development-resources/client-side-authentication/migrate-from-origin-key-to-client-key) is required!
- ⚠️ Removed support for Adyen origin key, manual [migration to client key](https://docs.adyen.com/development-resources/client-side-authentication/migrate-from-origin-key-to-client-key) is required!
- ⚠️ Removed support for your own Apple Pay certificate, because the [benefits of using Adyen's Apple Pay certificate](https://docs.adyen.com/payment-methods/apple-pay/web-drop-in):
  - A faster way to add Apple Pay to your integration.
  - There is less configuration required.
  - You get access to new features.
  - Apple Pay enabled by default for your Pay by Link integration, if you have one.

### Changed
- Switched to [Adyen web drop-in version `v5.14.0`](https://github.com/Adyen/adyen-web/releases/tag/v5.14.0).
- Switched to [Adyen Checkout API version `v68`](https://docs.adyen.com/api-explorer/#/CheckoutService/v68/overview).
- The Pronamic Pay payment method is updated from the Adyen webhook notification item.

### Added
- Added REST API endpoint `pronamic-pay/adyen/v1/return/<payment_id>`, to handle customers who come back after payment.
- Added REST API endpoint `pronamic-pay/adyen/v1/redirect/<payment_id>`, to redirect customers in the web drop-in.
- Added REST API endpoint `pronamic-pay/adyen/v1/error/<payment_id>`, to redirect errors in the web drop-in.

## [3.1.1] - 2022-04-12
- Updated version number in `readme.txt`.

## [3.1.0] - 2022-04-11
- Set payment failure reason and redirect Drop-in on refusal (resolves #2).
- Only set `applePayMerchantValidationUrl` when certificate is configured.

## [3.0.1] - 2022-02-16
- Added support for Klarna Pay Now and Klarna Pay Over Time.
- Added support for Afterpay and the Adyen `afterpaytouch` payment method indicator.
- Updated drop-in error handling ([#2](https://github.com/pronamic/wp-pronamic-pay-adyen/issues/2)).

## [3.0.0] - 2022-01-11
### Changed
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.
- Make notifications only update payment status if not already completed (fixes [pronamic/wp-pronamic-pay#245](https://github.com/pronamic/wp-pronamic-pay/issues/245)).
- Use Drop-in with auto submit for Swish payment method (instead of direct API integration, because of redirect to mobile app on desktop).
- Removed guessing country code with `\Locale::getRegion()` (can result in e.g. `EN` as invalid country code).
- Improved error handling on payment creation ([pronamic/wp-pronamic-pay#278](https://github.com/pronamic/wp-pronamic-pay/issues/278)).
- Removed `pronamicPayAdyenProcessing` in favor of setting drop status to `loading`.
- Clarified Afterpay.
- Disable Application Passwords for routes within integration REST route namespace as it interferes with our HTTP Basic authorization permission check.

### Added
- Added BLIK and MB WAY payment methods.
- Added support for TWINT payment method.

### Fixed
- Move script to inside HTML body.

## [2.0.4] - 2021-09-16
- Added support for the PayPal payment method.
- Added country code to Apple Pay payment method configuration.

## [2.0.3] - 2021-09-03
- Set pending payment status on payment start.

## [2.0.2] - 2021-08-19
- Adyen drop-in gateway supports Klarna Pay Later payment method.

## [2.0.1] - 2021-08-17
- No longer require PHP `intl` extensie.
- Simplified exception handling.

## [2.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core`  version `3.0.0`.
- Updated to `pronamic/wp-money`  version `2.0.0`.
- Changed `TaxedMoney` to `Money`, no tax info.
- Switched to `pronamic/wp-coding-standards`.
- Set additional data for Level 2/3 card payments (pronamic/wp-pronamic-pay#167).
- Added (partial) line items to additional data (pronamic/wp-pronamic-pay#167).
- Updated hooks documentation.

## [1.3.2] - 2021-06-18
- Updated to API version 64 and Drop-in SDK version 3.15.0 (adds support for ACH Direct Debit payment method).
- Updated documentation of the `pronamic_pay_adyen_checkout_head` action.

## [1.3.1] - 2021-04-26
- Added support for Swish and Vipps payment methods.
- Updated redirect/checkout pages.
- Started using `pronamic/wp-http`.

## [1.3.0] - 2021-01-14
- Fix some calls.
- Use new HTTP facade.
- Removed @see Plugin::start() reference.
- Use new filter for gateway configuration display value.

## [1.2.1] - 2020-11-19
- Removed unused configuration to store card details.

## [1.2.0] - 2020-11-09
- Added REST route permission callbacks.

## [1.1.2] - 2020-07-08
- Fixed possible conflicting payments caused by double clicking submit button.
- Removed empty meta data from payment request JSON.

## [1.1.1] - 2020-04-20
- Fixed not using billing address country code on drop-in payment redirect page.
- Added support for payment metadata via `pronamic_pay_adyen_payment_metadata` filter.
- Added advanced gateway configuration setting for `merchantOrderReference` parameter.
- Added browser information to payment request.
- Removed shopper reference from payment request.
- Removed payment status request from drop-in gateway supported features.

## [1.1.0] - 2020-03-19
- Fixed unnecessarily showing additional payment details screen in some cases.
- Only create controllers and actions when dependencies are met.
- Added Google Pay support.
- Added Apple Pay support.

## [1.0.6] - 2020-02-03
- Added support for Drop-in implementation (requires 'Origin Key' in gateway settings).
- Added application info support.

## [1.0.5] - 2019-12-22
- Added Site Health test for HTTP authorization header.
- Added URL to manual in gateway settings.
- Added shopper email to payment request.
- Improved support for PHP 5.6.

## [1.0.4] - 2019-10-04
- Improved some exception messages.

## [1.0.3] - 2019-09-10
- Added context to the 'notification' translatable strings.

## [1.0.2] - 2019-08-27
- Set country from billing address.
- Added action `pronamic_pay_adyen_checkout_head`.
- Added `pronamic_pay_adyen_config_object` filter and improved documentation.

## [1.0.1] - 2019-05-14
- Remove path from origin URL in payment session request.
- Fix API live URL prefix setting not saved.

## 1.0.0 - 2019-03-28
- First release.

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.0.0-RC-1...HEAD
[4.0.0-RC-1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/3.1.1...4.0.0-RC-1
[3.1.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/3.0.1...3.1.0
[3.0.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/2.0.4...3.0.0
[2.0.4]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.3.2...2.0.0
[1.3.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.1.2...1.2.0
[1.1.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.6...1.1.0
[1.0.6]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/1.0.0...1.0.1
