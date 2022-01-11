<?php
/**
 * Site Health controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use WP_Error;
use WP_REST_Request;

/**
 * Site Health controller
 *
 * @author  Remco Tolsma
 * @version 1.0.5
 * @since   1.0.5
 */
class SiteHealthController {
	/**
	 * HTTP Basic authentication test username.
	 *
	 * @var string
	 */
	const HTTP_BASIC_AUTHENTICATION_TEST_USERNAME = 'test';

	/**
	 * HTTP Basic authentication test password.
	 *
	 * @var string
	 */
	const HTTP_BASIC_AUTHENTICATION_TEST_PASSWORD = '1234';

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		add_filter( 'site_status_tests', array( $this, 'site_status_tests' ) );

		$prefix = 'health-check-';
		$action = 'pronamic-pay-adyen-http-authorization-test';

		add_action( 'wp_ajax_' . $prefix . $action, array( $this, 'wp_ajax_health_check_http_authorization_test' ) );

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Site status tests.
	 *
	 * @link https://make.wordpress.org/core/2019/04/25/site-health-check-in-5-2/
	 * @param array<string, array<string, array<string, string>>> $tests Tests.
	 * @return array<string, array<string, array<string, string>>>
	 */
	public function site_status_tests( $tests ) {
		$tests['async']['pronamic_pay_adyen_http_authorization_test'] = array(
			'label' => __( 'HTTP Authorization header test', 'pronamic_ideal' ),
			'test'  => 'pronamic-pay-adyen-http-authorization-test',
		);

		return $tests;
	}

	/**
	 * Get HTTP authorization header.
	 *
	 * @return string
	 */
	private function get_http_authorization_header() {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Benign reason.
		return 'Basic ' . base64_encode( self::HTTP_BASIC_AUTHENTICATION_TEST_USERNAME . ':' . self::HTTP_BASIC_AUTHENTICATION_TEST_PASSWORD );
	}

	/**
	 * Get HTTP authorization test.
	 *
	 * @return array<string, string|array<string, string>>
	 */
	private function get_http_authorization_test() {
		$result = array(
			'label'       => __( 'HTTP Basic authentication is working', 'pronamic_ideal' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Payments', 'pronamic_ideal' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'HTTP Basic authentication is required to securely receive Adyen notifications.', 'pronamic_ideal' )
			),
			'actions'     => '',
			'test'        => 'pronamic-pay-adyen-http-authorization-test',
		);

		$rest_url = \rest_url( Integration::REST_ROUTE_NAMESPACE . '/http-authorization-test' );

		$request_authorization = $this->get_http_authorization_header();

		$response = wp_remote_get(
			$rest_url,
			array(
				'headers' => array(
					'Authorization' => $request_authorization,
				),
			)
		);

		if ( $response instanceof \WP_Error ) {
			$result['status'] = 'critical';

			$result['label'] = __( 'Could not reach HTTP Authorization header test endpoint.', 'pronamic_ideal' );

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
					'<span class="error"><span class="screen-reader-text">%s</span></span> %s',
					__( 'Error', 'pronamic_ideal' ),
					sprintf(
						/* translators: %s: The error returned by the lookup. */
						__( 'Your site is unable to test the HTTP Authorization header, and returned the error: %s' ),
						$response->get_error_message()
					)
				)
			);

			return $result;
		}

		// Body.
		$body = \wp_remote_retrieve_body( $response );

		// Response.
		$response_code    = \wp_remote_retrieve_response_code( $response );
		$response_message = \wp_remote_retrieve_response_message( $response );

		// Data.
		$data = json_decode( $body );

		// JSON error.
		$json_error = json_last_error();

		if ( \JSON_ERROR_NONE !== $json_error ) {
			$result['status'] = 'critical';

			$result['description'] .= \sprintf(
				'Could not JSON decode response, HTTP response: "%s %s", HTTP body length: "%d", JSON error: "%s".',
				$response_code,
				$response_message,
				\strlen( $body ),
				\json_last_error_msg()
			);

			return $result;
		}

		// Object.
		if ( ! \is_object( $data ) ) {
			$result['status'] = 'critical';

			$result['description'] .= \sprintf(
				'Could not JSON decode response to an object, HTTP response: "%s %s", HTTP body: "%s".',
				$response_code,
				$response_message,
				$body
			);

			return $result;
		}

		if ( ! property_exists( $data, 'authorization' ) ) {
			$result['status'] = 'critical';

			return $result;
		}

		if ( $data->authorization !== $request_authorization ) {
			$result['status'] = 'critical';

			return $result;
		}

		$result['status'] = 'good';

		return $result;
	}

	/**
	 * WordPress AJAX health check HTTP authorization test.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.2.4/wp-admin/includes/class-wp-site-health.php#L1128-L1189
	 * @link https://github.com/WordPress/WordPress/blob/5.2.4/wp-admin/includes/ajax-actions.php#L4865-L4883
	 * @return void
	 */
	public function wp_ajax_health_check_http_authorization_test() {
		check_ajax_referer( 'health-check-site-status' );

		if ( ! current_user_can( 'view_site_health_checks' ) ) {
			wp_send_json_error();
		}

		$result = $this->get_http_authorization_test();

		wp_send_json_success( $result );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 * @return void
	 */
	public function rest_api_init() {
		register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/http-authorization-test',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_http_authorization_test' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * REST API Adyen notifications handler.
	 *
	 * @link https://www.php.net/manual/en/features.http-auth.php
	 * @param WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_http_authorization_test( WP_REST_Request $request ) {
		$data = array(
			'authorization' => $request->get_header( 'Authorization' ),
		);

		$server_keys = array(
			'HTTP_AUTHORIZATION',
			'PHP_AUTH_USER',
			'PHP_AUTH_PW',
			'AUTH_TYPE',
		);

		foreach ( $server_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$data[ $key ] = wp_unslash( $_SERVER[ $key ] );
			}
		}

		return (object) $data;
	}
}
