<?php
/**
 * Payment response action
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment response action class
 */
class PaymentResponseAction extends ResponseObject {
	/**
	 * Enum that specifies the action that needs to be taken by the client.
	 *
	 * @var string|null
	 */
	private $type;

	/**
	 * The URL, to which you must redirect a shopper to complete a payment
	 *
	 * @var string|null
	 */
	private $url;

	/**
	 * Get type.
	 *
	 * @return string|null
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get URL.
	 *
	 * @return string|null
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Create action information from object.
	 *
	 * @param object $value Object.
	 * @return PaymentResponseAction
	 * @throws \JsonSchema\Exception\ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $value ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$value,
			(object) [
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/payment-response-action.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		$action = new self();

		if ( isset( $value->type ) ) {
			$action->type = $value->type;
		}

		if ( isset( $value->url ) ) {
			$action->url = $value->url;
		}

		$action->set_original_object( $value );

		return $action;
	}
}
