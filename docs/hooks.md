# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### `pronamic_pay_adyen_checkout_head`

*Prints scripts or data in the head tag on the Adyen checkout page.*

This action can be used, for example, to register and print a custom style.

See the following link for an example:
https://github.com/wp-pay-gateways/adyen#pronamic_pay_adyen_checkout_head


**Changelog**

Version | Description
------- | -----------
`1.1` | Added.

Source: [./views/checkout-drop-in.php](../views/checkout-drop-in.php), [line 23](../views/checkout-drop-in.php#L23-L35)

### `pronamic_pay_webhook_log_payment`

*Log Adyen notification request for payment.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$payment` | `\Pronamic\WordPress\Pay\Payments\Payment` | Payment.

Source: [./src/NotificationsController.php](../src/NotificationsController.php), [line 192](../src/NotificationsController.php#L192-L197)

## Filters

### `pronamic_pay_adyen_checkout_configuration`

*Filters the Adyen checkout configuration.*

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$configuration` | `object` | Adyen checkout configuration.

**Changelog**

Version | Description
------- | -----------
`1.2.0` | Added.

Source: [./src/Gateway.php](../src/Gateway.php), [line 417](../src/Gateway.php#L417-L424)

### `pronamic_pay_adyen_merchant_order_reference`

*Filters the Adyen merchant order reference.*

This reference allows linking multiple transactions to each other
for reporting purposes (i.e. order auth-rate). The reference should
be unique per billing cycle. The same merchant order reference
should never be reused after the first authorised attempt. If used,
this field should be supplied for all incoming authorisations.

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$merchant_order_reference` | `string` | Merchant order reference.
`$payment` | `\Pronamic\WordPress\Pay\Payments\Payment` | Payment.

**Changelog**

Version | Description
------- | -----------
`4.5.0` | Added.

Source: [./src/PaymentRequestHelper.php](../src/PaymentRequestHelper.php), [line 38](../src/PaymentRequestHelper.php#L38-L52)

### `pronamic_pay_adyen_payment_metadata`

*Filters the Adyen payment metadata.*

Maximum 20 key-value pairs per request. When exceeding, the "177" error occurs: "Metadata size exceeds limit".

**Arguments**

Argument | Type | Description
-------- | ---- | -----------
`$metadata` | `array` | Payment request metadata.
`$payment` | `\Pronamic\WordPress\Pay\Payments\Payment` | Payment.

**Changelog**

Version | Description
------- | -----------
`1.1.1` | Added.

Source: [./src/PaymentRequestHelper.php](../src/PaymentRequestHelper.php), [line 249](../src/PaymentRequestHelper.php#L249-L259)


<p align="center"><a href="https://github.com/pronamic/wp-documentor"><img src="https://cdn.jsdelivr.net/gh/pronamic/wp-documentor@main/logos/pronamic-wp-documentor.svgo-min.svg" alt="Pronamic WordPress Documentor" width="32" height="32"></a><br><em>Generated by <a href="https://github.com/pronamic/wp-documentor">Pronamic WordPress Documentor</a> <code>1.2.0</code></em><p>

