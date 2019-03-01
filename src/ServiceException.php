<?php
/**
 * Service exception
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;

/**
 * Service exception
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/serviceexception
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ServiceException {
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
	 * The message, a short explanation of the issue.
	 *
	 * @var string
	 */
	private $message;

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
		$this->status     = $status;
		$this->error_code = $error_code;
		$this->message    = $message;
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
		return $this->message;
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
	 * @param object $object Object.
	 * @return ServiceException
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->status ) ) {
			throw new InvalidArgumentException( 'Object must contain `status` property.' );
		}

		if ( ! isset( $object->errorCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `errorCode` property.' );
		}

		if ( ! isset( $object->message ) ) {
			throw new InvalidArgumentException( 'Object must contain `message` property.' );
		}

		if ( ! isset( $object->errorType ) ) {
			throw new InvalidArgumentException( 'Object must contain `errorType` property.' );
		}

		return new self(
			$object->status,
			$object->errorCode,
			$object->message,
			$object->errorType
		);
	}
}