# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

## [4.5.1] - 2024-03-26

### Commits

- Updated .gitattributes ([28e541d](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/28e541d95d06be05a93af272dfa6d48ffae9c09e))
- Added `if ( ! defined( 'ABSPATH' ) )`. ([21059f5](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/21059f5cbb67114c5f4fc7f99f8abcfeec758a71))
- Added `if ( ! defined( 'ABSPATH' ) )`. ([d5d9811](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/d5d9811082d39a759ed544a0558ec91c71243f79))
- Updated .gitattributes ([5f03af2](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/5f03af26b04742a92d431fee9c2fa0fe98ab6c91))
- Translate. ([b5e0796](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/b5e079659bf9c22633b81b1b7b04b8a01a0e7621))

Full set of changes: [`4.5.0...4.5.1`][4.5.1]

[4.5.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.5.0...v4.5.1

## [4.5.0] - 2024-02-07

### Changed

- The code further complies with (WordPress) coding standards.
- Improved support for PHP 8 and higher.
- Use `wp-scripts` to build JS. [38e2088](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/38e20883505c323bae2647ca2e4c0b385b97e96f) [4398600](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/439860048f90faf72a0fdf9f848d88ff656c9620)
- Updated to Adyen API `v71`. [21da986](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/21da986f66472e006f1b04a9d3b79870ce6f2227)

### Composer

- Changed `automattic/jetpack-autoloader` from `^2.11` to `v3.0.2`.
	Release notes: https://github.com/Automattic/jetpack-autoloader/releases/tag/v3.0.2

Full set of changes: [`4.4.8...4.5.0`][4.5.0]

[4.5.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.8...v4.5.0

## [4.4.8] - 2023-10-30

### Commits

- Simplified options for select. ([08294f1](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/08294f16a1dfaf30894019c5da1dcdc2d65b26aa))

Full set of changes: [`4.4.7...4.4.8`][4.4.8]

[4.4.8]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.7...v4.4.8

## [4.4.7] - 2023-10-13

### Commits

- Use `wp_kses` to allow only <a href=""> element. ([171d841](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/171d84103b4b719c95b734619c7351cf05b756a9))
- No longer use `FILTER_UNSAFE_RAW`, instead use a custom input callback. ([d70823b](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/d70823b38c2f70dc6e65393d7aba56257f8fcdfb))
- The default sanitize function allows dobule quotes. ([ac99766](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/ac99766b9e1505df22aae32ebe247801bec3fdf0))
- No longer use `Server::get()` function, will be removed. ([6f7dda2](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/6f7dda274dff811961aa9e487dd53568082f198d))
- Use callback, since 'description' field type support was removed. ([451bb33](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/451bb33187e372a62462d5d148e8b94349e75e60))

Full set of changes: [`4.4.6...4.4.7`][4.4.7]

[4.4.7]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.6...v4.4.7

## [4.4.6] - 2023-07-12

### Commits

- Updated for removed payment ID fallback in formatted payment string (pronamic/wp-pronamic-pay-adyen#23). ([f4ea7b2](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/f4ea7b2534f8c693ef0db62b54e6fd51e2686f6a))
- Added filter for merchant order reference. ([783900b](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/783900b51c6a969fcb004bb0870d58492af8c23f))

Full set of changes: [`4.4.5...4.4.6`][4.4.6]

[4.4.6]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.5...v4.4.6

## [4.4.5] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([13471a1](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/13471a195870e8a70e6fc5bd1686539f79772bb0))
- Added missing text domain. ([8facc90](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/8facc904a4117c6e55773be1615d7c4e32634bdc))
- Updated .gitattributes ([b62c276](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/b62c276841c12227469de41aa985eb751c4a3faf))
- Removed trailing slash from `.github` directory. ([0f09f3e](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/0f09f3edcc5ce88cb909e40e27ace9cbc5b261bc))

Full set of changes: [`4.4.4...4.4.5`][4.4.5]

[4.4.5]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.4...v4.4.5

## [4.4.4] - 2023-03-27

### Commits

- Set Composer to `wordpress-plugin`. ([8b0f073](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/8b0f07320bb070c6c366925ee19245615229fd9a))
- Added Jetpack autoloader to fix #21. ([53c3e9b](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/53c3e9b32399b5dd050a994e07b78a589e73cbe1))
- Updated .gitattributes ([f78ee40](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/f78ee40a54d685ed7eb6e8fd3ae5a6562a52f6df))

### Composer

- Added `automattic/jetpack-autoloader` `^2.11`.
- Changed `wp-pay/core` from `^4.6` to `v4.8.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.8.0
Full set of changes: [`4.4.3...4.4.4`][4.4.4]

[4.4.4]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.3...v4.4.4

## [4.4.3] - 2023-02-15

### Commits

- Added support for PayPal payment method. ([27e7508](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/27e75089763f71508f461195978cc580015e0ba8))

Full set of changes: [`4.4.2...4.4.3`][4.4.3]

[4.4.3]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.2...v4.4.3

## [4.4.2] - 2023-02-03

### Commits

- Updated "Requires PHP: 7.4" plugin header. ([acbe9c5](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/acbe9c5e4bd4a342c08e054f71147d6d6cdbe15d))

Full set of changes: [`4.4.1...4.4.2`][4.4.2]

[4.4.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.1...v4.4.2

## [4.4.1] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`4.4.0...4.4.1`][4.4.1]

[4.4.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.4.0...v4.4.1

## [4.4.0] - 2022-12-20
- Increased minimum PHP version to version `8` or higher.
- Improved support for PHP `8.1` and `8.2`.
- Removed usage of deprecated constant `FILTER_SANITIZE_STRING`.
- Added support for https://github.com/WordPress/wp-plugin-dependencies. ([b8b2fd0](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/b8b2fd05efca0f1685fca3e1b7e89bd0b8cab71a))
- Updated manual URL to pronamicpay.com (pronamic/pronamic-pay#15). ([43f1d7d](https://github.com/pronamic/wp-pronamic-pay-adyen/commit/43f1d7d8b6e51ae8470f10b3bc1a2e3cd06d0ec8))

Full set of changes: [`4.3.1...4.4.0`][4.4.0]

[4.4.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/v4.3.1...v4.4.0

## [4.3.1] - 2022-11-29
- Redirect API-only payment methods to payment action URL. [#18](https://github.com/pronamic/wp-pronamic-pay-adyen/issues/18)
- Make `redirectResult` no longer required in return endpoint. [#19](https://github.com/pronamic/wp-pronamic-pay-adyen/issues/19)

## [4.3.0] - 2022-11-07
- Added MobilePay payment method. [#16](https://github.com/pronamic/wp-pronamic-pay-adyen/issues/16)

## [4.2.3] - 2022-10-11
- Updated Adyen Drop-in to version `5.27.0` (pronamic/wp-pronamic-pay-adyen#14).
- Fixes error triggered by Adyen drop-in with Swish payment method on mobile.

## [4.2.2] - 2022-09-27
- Updated version number in `readme.txt`.

## [4.2.1] - 2022-09-27
- Update to `wp-pay/core` version `^4.4`.

## [4.2.0] - 2022-09-25
- Updated payment methods registration.

## [4.1.0] - 2022-07-01
### Changed
- Added WordPress network ID and blog ID to merchant reference ([#1](https://github.com/pronamic/wp-pronamic-pay-adyen/issues/1)).

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

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.3.1...HEAD
[4.3.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.3.0...4.3.1
[4.3.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.2.3...4.3.0
[4.2.3]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.2.2...4.2.3
[4.2.2]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.2.1...4.2.2
[4.2.1]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/pronamic/wp-pronamic-pay-adyen/compare/3.1.1...4.0.0
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
