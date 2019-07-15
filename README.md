# WordPress Pay Gateway: Adyen

**Adyen driver for the WordPress payment processing library.**

[![Build Status](https://travis-ci.org/wp-pay-gateways/adyen.svg?branch=develop)](https://travis-ci.org/wp-pay-gateways/adyen)
[![Coverage Status](https://coveralls.io/repos/wp-pay-gateways/adyen/badge.svg?branch=develop&service=github)](https://coveralls.io/github/wp-pay-gateways/adyen?branch=develop)
[![Latest Stable Version](https://img.shields.io/packagist/v/wp-pay-gateways/adyen.svg)](https://packagist.org/packages/wp-pay-gateways/adyen)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/wp-pay-gateways/adyen.svg)](https://packagist.org/packages/wp-pay-gateways/adyen)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-pay-gateways/adyen.svg)](https://packagist.org/packages/wp-pay-gateways/adyen)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/wp-pay-gateways/adyen.svg)](https://packagist.org/packages/wp-pay-gateways/adyen)
[![License](https://img.shields.io/packagist/l/wp-pay-gateways/adyen.svg)](https://packagist.org/packages/wp-pay-gateways/adyen)
[![Built with Grunt](https://gruntjs.com/cdn/builtwith.svg)](http://gruntjs.com/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/build-status/develop)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/wp-pay-gateways/adyen/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fadyen.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fadyen?ref=badge_shield)

## Adyen Notifications (webhooks)

The Pronamic Pay Adyen gateway can handle Adyen notifications via the WordPress REST API.

**Route:** `/wp-json/pronamic-pay/adyen/v1/notifications`

The WordPress REST API Adyen notifications endpoint can be tested with for example cURL:

```
curl --request POST --user username:password http://pay.test/wp-json/pronamic-pay/adyen/v1/notifications
```

## WordPress Filters

### pronamic_pay_adyen_checkout_head

```php
add_action( 'pronamic_pay_adyen_checkout_head', 'custom_adyen_checkout_head', 15 );

function custom_adyen_checkout_head() {
	wp_register_style(
		'custom-adyen-checkout-style',
		get_stylesheet_directory_uri() . '/css/adyen-checkout.css',
		array(),
		'1.0.0'
	);

	wp_print_styles( 'custom-adyen-checkout-style' );
}
```

## Production Environment

**Dashboard URL:** https://ca-live.adyen.com/  
**API URL:** https://{LIVE_API_URL_PREFIX}-checkout-live.adyenpayments.com/checkout/v41/

## Test Environment

**Dashboard URL:** https://ca-test.adyen.com/  
**API URL:** https://checkout-test.adyen.com/v41/


## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fadyen.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fwp-pay-gateways%2Fadyen?ref=badge_large)