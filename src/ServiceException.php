<?php
/**
 * Service exception
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Exception;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Service exception class
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/serviceexception
 */
class ServiceException extends Exception {
	/**
	 * The HTTP response status code.
	 *
	 * @var string
	 */
	private $status;

	/**
	 * The Adyen code that is mapped to the error message.
	 *
	 * @var string
	 */
	private $error_code;

	/**
	 * The type of error that was encountered.
	 *
	 * @var string
	 */
	private $error_type;

	/**
	 * Construct service exception.
	 *
	 * @param string $status     Status.
	 * @param string $error_code Error code.
	 * @param string $message    Message.
	 * @param string $error_type Error type.
	 */
	public function __construct( $status, $error_code, $message, $error_type ) {
		parent::__construct( $message, intval( $error_code ) );

		$this->status     = $status;
		$this->error_code = $error_code;
		$this->error_type = $error_type;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get error code.
	 *
	 * @return string
	 */
	public function get_error_code() {
		return $this->error_code;
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
	 * Get error type.
	 *
	 * @return string
	 */
	public function get_error_type() {
		return $this->error_type;
	}

	/**
	 * Create service exception from object.
	 *
	 * @param object $value Object.
	 * @return ServiceException
	 * @throws ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_object( $value ) {
		$validator = new Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/service-exception.json' ),
			],
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		$data = new ObjectAccess( $value );

		return new self(
			$data->get_property( 'status' ),
			$data->get_property( 'errorCode' ),
			$data->get_property( 'message' ),
			$data->get_property( 'errorType' )
		);
	}
}
