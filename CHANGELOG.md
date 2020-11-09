# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased][unreleased]

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

[unreleased]: https://github.com/wp-pay-gateways/adyen/compare/1.2.0...HEAD
[1.2.0]: https://github.com/wp-pay-gateways/adyen/compare/1.1.2...1.2.0
[1.1.2]: https://github.com/wp-pay-gateways/adyen/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wp-pay-gateways/adyen/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-gateways/adyen/compare/1.0.6...1.1.0
[1.0.6]: https://github.com/wp-pay-gateways/adyen/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/wp-pay-gateways/adyen/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/wp-pay-gateways/adyen/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/wp-pay-gateways/adyen/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-gateways/adyen/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-gateways/adyen/compare/1.0.0...1.0.1
