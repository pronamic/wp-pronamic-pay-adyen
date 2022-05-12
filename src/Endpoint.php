<?php
/**
 * Endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Endpoint class
 *
 * @link https://docs.adyen.com/developers/development-resources/live-endpoints
 */
class Endpoint {
	/**
	 * API endpoint test URL.
	 *
	 * @var string
	 */
	const API_URL_TEST = 'https://checkout-test.adyen.com/[version]/[method]';

	/**
	 * API endpoint live URL.
	 *
	 * @var string
	 */
	const API_URL_LIVE = 'https://[prefix]-checkout-live.adyenpayments.com/checkout/[version]/[method]';

	/**
	 * Mode.
	 *
	 * These endpoints differ for test and live accounts.
	 *
	 * @var string
	 */
	private $mode;

	/**
	 * Live URL prefix.
	 *
	 * This prefix is the combination of the [random] and [company name] from the live
	 * endpoint.
	 *
	 * @link https://docs.adyen.com/development-resources/live-endpoints#live-url-prefix
	 * @var string|null
	 */
	public $live_url_prefix;

	/**
	 * Construct endpoint.
	 *
	 * @param string      $mode            Mode.
	 * @param string|null $live_url_prefix Live URL prefix.
	 */
	public function __construct( $mode, $live_url_prefix ) {
		$this->mode            = $mode;
		$this->live_url_prefix = $live_url_prefix;
	}

	/**
	 * Get API URL.
	 *
	 * @param string $version Version.
	 * @param string $method  API method.
	 * @return string
	 * @throws \Exception Throws exception when mode is live and API live URL prefix is empty.
	 */
	public function get_api_url( $version, $method ) {
		if ( 'test' === $this->mode ) {
			return \strtr(
				self::API_URL_TEST,
				[
					'[version]' => $version,
					'[method]'  => $method,
				]
			);
		}

		if ( empty( $this->live_url_prefix ) ) {
			throw new \Exception( 'Adyen API Live URL prefix is required for live configurations.' );
		}

		return \strtr(
			self::API_URL_LIVE,
			[
				'[prefix]'  => $this->live_url_prefix,
				'[version]' => $version,
				'[method]'  => $method,
			]
		);
	}
}
