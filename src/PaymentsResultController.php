<?php
/**
 * Payments result controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSchema\Exception\ValidationException;
use Pronamic\WordPress\Pay\Core\Statuses as PaymentStatus;
use Pronamic\WordPress\Pay\Plugin;
use WP_Error;
use WP_REST_Request;

/**
 * Payments result controller
 *
 * @link https://docs.adyen.com/developers/checkout/web-sdk/customization/logic#beforecomplete
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentsResultController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 *
	 * @return void
	 */
	public function rest_api_init() {
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/payments/result/(?P<config_id>\d+)',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'rest_api_adyen_payments_result' ),
				'args'     => array(
					'config_id'  => array(
						'description' => __( 'Gateway configuration ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
					),
					'payload'    => array(
						'description' => __( 'Payload.', 'pronamic_ideal' ),
						'type'        => 'string',
					),
					'resultCode' => array(
						'description' => __( 'Result code.', 'pronamic_ideal' ),
						'type'        => 'string',
					),
					'resultText' => array(
						'description' => __( 'Result text.', 'pronamic_ideal' ),
						'type'        => 'string',
					),
				),
			)
		);
	}

	/**
	 * REST API Adyen payments result handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_payments_result( WP_REST_Request $request ) {
		$config_id = $request->get_param( 'config_id' );
		$payload   = $request->get_param( 'payload' );

		// Gateway.
		$gateway = Plugin::get_gateway( $config_id );

		if ( empty( $gateway ) ) {
			return new WP_Error(
				'pronamic-pay-adyen-gateway-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not found gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		if ( ! isset( $gateway->client ) ) {
			return new WP_Error(
				'pronamic-pay-adyen-client-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not found client in gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		// Client.
		$payment_result_request = new PaymentResultRequest( $payload );

		$payment_result_response = $gateway->client->get_payment_result( $payment_result_request );

		$merchant_reference = $payment_result_response->get_merchant_reference();

		// Payment.
		$payment = get_pronamic_payment( $merchant_reference );

		if ( empty( $payment ) ) {
			return new WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
					/* translators: %s: Adyen merchant reference */
					__( 'Could not found payment with ID `%s`.', 'pronamic_ideal' ),
					$merchant_reference
				),
				$payment_result_response
			);
		}

		PaymentResultHelper::update_payment( $payment, $payment_result_response );

		// Return payment result response.
		return $payment_result_response->get_json();
	}
}
