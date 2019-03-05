# WordPress Pay Gateway: Adyen

**Adyen driver for the WordPress payment processing library.**

[![Built with Grunt](https://cdn.gruntjs.com/builtwith.png)](http://gruntjs.com/)

## Adyen Notifications (webhooks)

The Pronamic Pay Adyen gateway can handle Adyen notifications via the WordPress REST API.

**Route:** `/wp-json/pronamic-pay/adyen/v1/notifications`

The WordPress REST API Adyen notifications endpoint can be tested with for example cURL:

```
curl --request POST --user username:password http://pay.test/wp-json/pronamic-pay/adyen/v1/notifications
```

## Production Environment

**Dashboard URL:** https://ca-live.adyen.com/  
**Payment Server URL:** https://live.adyen.com/hpp/pay.shtml  

## Test Environment

**Dashboard URL:** https://ca-test.adyen.com/  
**Payment Server URL:** https://test.adyen.com/hpp/pay.shtml  
