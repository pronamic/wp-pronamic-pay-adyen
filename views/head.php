<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

<title><?php esc_html_e( 'Checkout', 'pronamic_ideal' ); ?></title>

<?php

/**
 * Prints scripts or data in the head tag on the Adyen checkout page.
 * 
 * This action can be used, for example, to register and print a custom style.
 *
 * See the following link for an example:
 * https://github.com/wp-pay-gateways/adyen#pronamic_pay_adyen_checkout_head
 *
 * @link https://github.com/WordPress/WordPress/blob/5.7/wp-includes/general-template.php#L3004-L3009
 *
 * @since 1.1
 */
do_action( 'pronamic_pay_adyen_checkout_head' );

?>
