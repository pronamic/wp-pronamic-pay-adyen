<?php
/**
 * Checkout head.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

?>
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
 * @since 1.1 Added.
 */
do_action( 'pronamic_pay_adyen_checkout_head' );

?>
