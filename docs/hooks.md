# Hooks

- [Actions](#actions)
- [Filters](#filters)

## Actions

### pronamic_pay_webhook_log_payment

Argument | Type | Description
-------- | ---- | -----------
`$payment` |  | 

Source: [src/NotificationsController.php](../src/NotificationsController.php), [line 157](../src/NotificationsController.php#L157-L157)

## Filters

### pronamic_pay_adyen_checkout_configuration

*Filters the Adyen checkout configuration.*



Argument | Type | Description
-------- | ---- | -----------
`$configuration` | `object` | Adyen checkout configuration.

Source: [src/DropInGateway.php](../src/DropInGateway.php), [line 268](../src/DropInGateway.php#L268-L274)

### pronamic_pay_adyen_payment_metadata

*Filters the Adyen checkout configuration.*



Argument | Type | Description
-------- | ---- | -----------
`$metadata` | `array` | Payment request metadata.
`$payment` |  | 

Source: [src/PaymentRequestHelper.php](../src/PaymentRequestHelper.php), [line 134](../src/PaymentRequestHelper.php#L134-L140)

### pronamic_pay_adyen_config_object

*Filters the Adyen config object.*



Argument | Type | Description
-------- | ---- | -----------
`$config_object` | `object` | Adyen config object.

Source: [src/WebSdkGateway.php](../src/WebSdkGateway.php), [line 257](../src/WebSdkGateway.php#L257-L269)


