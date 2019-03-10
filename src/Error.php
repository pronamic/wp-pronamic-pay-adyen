<?php
/**
 * Error
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Error
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Error extends Exception {
	/**
	 * The requested URI.
	 *
	 * @var string
	 */
	private $requested_uri;

	/**
	 * Construct error.
	 *
	 * @param int    $code          Code.
	 * @param string $message       Message.
	 * @param string $requested_uri Requested URI.
	 */
	public function __construct( $code, $message, $requested_uri ) {
		parent::__construct( $message, $code );

		$this->requested_uri = $requested_uri;
	}

	/**
	 * Get code.
	 *
	 * @return int
	 */
	public function get_code() {
		return intval( $this->getCode() );
	}

	/**
	 * Get message.
	 *
	 * @return string
	 */
	public function get_message() {
		return $this->getMessage();
	}

	/**
	 * Get requested URI.
	 *
	 * @return string
	 */
	public function get_requested_uri() {
		return $this->requested_uri;
	}

	/**
	 * Create error from object.
	 *
	 * @param object $object Object.
	 * @return Error
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $object ) {
		$validator = new Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/error.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		return new self(
			$object->code,
			$object->message,
			$object->{'requested URI'}
		);
	}
}
