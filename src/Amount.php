<?php
/**
 * Amount
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Amount
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/amount
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class Amount implements \JsonSerializable {
	/**
	 * Currency.
	 *
	 * @var string
	 */
	private $currency;

	/**
	 * Value.
	 *
	 * @var int
	 */
	private $value;

	/**
	 * Construct amount.
	 *
	 * @param string $currency Currency.
	 * @param int    $value    Value.
	 *
	 * @throws \InvalidArgumentException Throws invalid argument exception when Adyen amount requirements are not met.
	 */
	public function __construct( $currency, $value ) {
		if ( 3 !== strlen( $currency ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'Given currency `%s` not a three-character ISO currency code.',
					$currency
				)
			);
		}

		$this->currency = $currency;
		$this->value    = $value;
	}

	/**
	 * Get currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Get amount.
	 *
	 * @return int
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		return (object) array(
			'currency' => $this->get_currency(),
			'value'    => $this->get_value(),
		);
	}

	/**
	 * JSON serialize.
	 *
	 * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return object
	 */
	public function jsonSerialize() {
		return $this->get_json();
	}

	/**
	 * Create amount from object.
	 *
	 * @param object $object Object.
	 * @return Amount
	 * @throws \JsonSchema\Exception\ValidationException Throws validation exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$object,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/amount.json' ),
			),
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return new self(
			$object->currency,
			$object->value
		);
	}
}
