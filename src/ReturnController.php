<?php
/**
 * Return controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Pay\Payments\FailureReason;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Plugin;
use WP_Error;
use WP_REST_Request;

/**
 * Return controller class
 */
class ReturnController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		// Actions.
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
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
		/**
		 * Adyen return route.
		 * 
		 * @link https://docs.adyen.com/online-payments/web-drop-in#handle-redirect-result
		 */
		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/return/(?P<payment_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_adyen_return' ],
				'permission_callback' => function() {
					return true;
				},
				'args'                => [
					'payment_id'     => [
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'sessionId'      => [
						'description' => __( 'The unique identifier for the shopper\'s payment session.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => false,
					],
					'redirectResult' => [
						'description' => __( 'Details you need to submit to handle the redirect.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => true,
					],
				],
			]
		);

		/**
		 * Adyen redirect route.
		 * 
		 * @link https://docs.adyen.com/online-payments/web-drop-in#handle-redirect-result
		 */
		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/redirect/(?P<payment_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_adyen_redirect' ],
				'permission_callback' => [ $this, 'rest_api_adyen_permission' ],
				'args'                => [
					'payment_id' => [
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'hash'       => [
						'description' => \__( 'Hash.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => true,
					],
					'resultCode' => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);

		/**
		 * Adyen error route.
		 *
		 * @link https://docs.adyen.com/online-payments/web-drop-in#handle-redirect-result
		 */
		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/error/(?P<payment_id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_adyen_error' ],
				'permission_callback' => [ $this, 'rest_api_adyen_permission' ],
				'args'                => [
					'payment_id' => [
						'description' => __( 'Payment ID.', 'pronamic_ideal' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'hash'       => [
						'description' => \__( 'Hash.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => true,
					],
					'name'       => [
						'description' => __( 'Error name.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => true,
					],
					'message'    => [
						'description' => __( 'Error message.', 'pronamic_ideal' ),
						'type'        => 'string',
						'required'    => true,
					],
				],
			]
		);
	}

	/**
	 * REST API Adyen return handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_return( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		// Payment ID.
		if ( null === $payment_id ) {
			return new WP_Error(
				'pronamic-pay-adyen-no-payment-id',
				__( 'No payment ID given in `payment_id` parameter.', 'pronamic_ideal' )
			);
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
					/* translators: %s: payment ID */
					__( 'Could not find payment with ID `%s`.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
			);
		}

		// Gateway.
		$config_id = $payment->get_config_id();

		if ( null === $config_id ) {
			return new WP_Error(
				'pronamic-pay-adyen-no-config',
				__( 'No gateway configuration ID given in payment.', 'pronamic_ideal' )
			);
		}

		$gateway = Plugin::get_gateway( $config_id );

		if ( ! $gateway instanceof Gateway ) {
			return new WP_Error(
				'pronamic-pay-adyen-gateway-not-found',
				sprintf(
					/* translators: %s: Gateway configuration ID */
					__( 'Could not find gateway with ID `%s`.', 'pronamic_ideal' ),
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
					__( 'Could not find client in gateway with ID `%s`.', 'pronamic_ideal' ),
					$config_id
				),
				$config_id
			);
		}

		// Redirect result.
		$redirect_result = $request->get_param( 'redirectResult' );

		// OK.
		$payment_details_request = new PaymentDetailsRequest();

		$payment_details_request->set_details(
			(object) [
				'redirectResult' => $redirect_result,
			] 
		);

		try {
			$payment_details_response = $gateway->client->request_payment_details( $payment_details_request );

			PaymentResponseHelper::update_payment( $payment, $payment_details_response );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'pronamic-pay-adyen-payment-details-exception',
				$e->getMessage()
			);
		}

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		return new \WP_REST_Response( null, 303, [ 'Location' => $payment->get_return_redirect_url() ] );
	}

	/**
	 * REST API Adyen redirect handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_redirect( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		// Payment ID.
		if ( null === $payment_id ) {
			return new WP_Error(
				'pronamic-pay-adyen-no-payment-id',
				__( 'No payment ID given in `payment_id` parameter.', 'pronamic_ideal' )
			);
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
					/* translators: %s: payment ID */
					__( 'Could not find payment with ID `%s`.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
			);
		}

		/**
		 * Result code.
		 * 
		 * @link https://docs.adyen.com/online-payments/payment-result-codes
		 */
		$result_code = $request->get_param( 'resultCode' );

		$status = ResultCode::transform( $result_code );

		if ( null !== $status ) {
			$payment->set_status( $status );
		}

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		return new \WP_REST_Response( null, 303, [ 'Location' => $payment->get_return_redirect_url() ] );
	}

	/**
	 * REST API Adyen permission handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool
	 */
	public function rest_api_adyen_permission( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		if ( empty( $payment_id ) ) {
			return false;
		}

		$hash = $request->get_param( 'hash' );

		if ( empty( $hash ) ) {
			return false;
		}

		return \wp_hash( $payment_id ) === $hash;
	}

	/**
	 * REST API Adyen error handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_adyen_error( WP_REST_Request $request ) {
		$payment_id = $request->get_param( 'payment_id' );

		// Payment ID.
		if ( null === $payment_id ) {
			return new WP_Error(
				'pronamic-pay-adyen-no-payment-id',
				__( 'No payment ID given in `payment_id` parameter.', 'pronamic_ideal' )
			);
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			return new WP_Error(
				'pronamic-pay-adyen-payment-not-found',
				sprintf(
				/* translators: %s: payment ID */
					__( 'Could not find payment with ID `%s`.', 'pronamic_ideal' ),
					$payment_id
				),
				$payment_id
			);
		}

		/**
		 * Error name.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Error
		 * @link https://github.com/Adyen/adyen-web/blob/v5.15.0/packages/lib/src/core/Errors/AdyenCheckoutError.ts
		 */
		$error_name    = $request->get_param( 'name' );
		$error_message = $request->get_param( 'message' );

		$failure_reason = new FailureReason();

		$failure_reason->set_code( $error_name );
		$failure_reason->set_message( $error_message );

		$payment->set_failure_reason( $failure_reason );

		$payment->set_status( PaymentStatus::FAILURE );

		$payment->save();

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		return new \WP_REST_Response( null, 303, [ 'Location' => $payment->get_return_redirect_url() ] );
	}
}
