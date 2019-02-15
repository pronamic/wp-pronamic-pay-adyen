<?php
/**
 * Notice Pronamic Pay required.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div class="error">
	<p>
		<strong><?php esc_html_e( 'Pronamic Pay Adyen Add-On', 'pronamic_ideal' ); ?></strong> —
		<?php

		esc_html_e( 'The Adyen gateway add-on requires the Pronamic Pay plugin to receive payments.', 'pronamic_ideal' );

		?>
	</p>
</div>
