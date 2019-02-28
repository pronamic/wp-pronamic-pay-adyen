<?php
/**
 * Redirect information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Redirect information
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/payments
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class RedirectInformation {
	/**
	 * When the redirect URL must be accessed via POST, use this data to post to the redirect URL.
	 *
	 * @var object
	 */
	private $data;

	/**
	 * The web method that you must use to access the redirect URL.
	 *
	 * Possible values: GET, POST.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * The URL, to which you must redirect a shopper to complete a payment
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Construct redirect information.
	 *
	 * @param object $data   Data.
	 * @param string $method Method.
	 * @param string $url    URL.
	 */
	public function __construct( $data, $method, $url ) {
		$this->data   = $data;
		$this->method = $method;
		$this->url    = $url;
	}

	/**
	 * Get data.
	 *
	 * @return object
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get method.
	 *
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Get URL.
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Create redirect information from object.
	 *
	 * @param object $object Object.
	 * @return RedirectInformation
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->data ) ) {
			throw new InvalidArgumentException( 'Object must contain `data` property.' );
		}

		if ( ! isset( $object->method ) ) {
			throw new InvalidArgumentException( 'Object must contain `method` property.' );
		}

		if ( ! isset( $object->url ) ) {
			throw new InvalidArgumentException( 'Object must contain `url` property.' );
		}

		return new self(
			$object->data,
			$object->method,
			$object->url
		);
	}
}
