<?php
/**
 * Drop-in gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use InvalidArgumentException;
use Locale;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;
use WP_Error;

/**
 * Drop-in gateway
 *
 * @link https://github.com/adyenpayments/php/blob/master/generatepaymentform.php
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.0
 */
class DropInGateway extends AbstractGateway {
	/**
	 * Web SDK version.
	 *
	 * @link https://docs.adyen.com/developers/checkout/web-sdk/release-notes-web-sdk
	 *
	 * @var string
	 */
	const SDK_VERSION = '3.4.0';

	/**
	 * Payment redirect.
	 *
	 * @param Payment $payment Payment.
	 *
	 * @return void
	 */
	public function payment_redirect( Payment $payment ) {
		$url_script = sprintf(
			'https://checkoutshopper-%s.adyen.com/checkoutshopper/sdk/%s/adyen.js',
			( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			self::SDK_VERSION
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
		wp_register_script(
			'pronamic-pay-adyen-checkout',
			$url_script,
			array(),
			null,
			false
		);

		$url_stylesheet = sprintf(
			'https://checkoutshopper-%s.adyen.com/checkoutshopper/sdk/%s/adyen.css',
			( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			self::SDK_VERSION
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Version is part of URL.
		wp_register_style(
			'pronamic-pay-adyen-checkout',
			$url_stylesheet,
			array(),
			null
		);

		/**
		 * Payment methods.
		 */
		$request = new PaymentMethodsRequest( $this->config->get_merchant_account() );

		$request->set_amount( AmountTransformer::transform( $payment->get_total_amount() ) );

		$payment_methods = $this->client->get_payment_methods( $request );

		/**
		 * Adyen checkout configuration.
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web
		 * @link https://docs.adyen.com/checkout/components-web
		 */
		$configuration = (object) array(
			'locale'                 => 'en-US',
			'environment'            => ( self::MODE_TEST === $payment->get_mode() ? 'test' : 'live' ),
			'originKey'              => $this->config->origin_key,
			'paymentMethodsResponse' => $payment_methods->get_original_object(),
		);

		/**
		 * Filters the Adyen checkout configuration.
		 *
		 * @param object $configuration Adyen checkout configuration.
		 * @since 1.2.0
		 */
		$configuration = apply_filters( 'pronamic_pay_adyen_checkout_configuration', $configuration );

		wp_localize_script(
			'pronamic-pay-adyen-checkout',
			'pronamicPayAdyenCheckout',
			array(
				'configuration' => $configuration,
			)
		);

		// Add checkout head action.
		add_action( 'pronamic_pay_adyen_checkout_head', array( $this, 'checkout_head' ) );

		// No cache.
		Core_Util::no_cache();

		require __DIR__ . '/../views/checkout-drop-in.php';

		exit;
	}

	/**
	 * Checkout head.
	 *
	 * @return void
	 */
	public function checkout_head() {
		wp_print_styles( 'pronamic-pay-redirect' );

		wp_print_scripts( 'pronamic-pay-adyen-checkout' );

		wp_print_styles( 'pronamic-pay-adyen-checkout' );
	}
}
