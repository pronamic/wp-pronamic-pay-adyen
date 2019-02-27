<?php
/**
 * Amount
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Amount
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class Amount {
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
	 */
	public function __construct( $currency, $value ) {
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
	 * Create amount from object.
	 *
	 * @param object $object Object.
	 * @return Amount
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->currency ) ) {
			throw new InvalidArgumentException( 'Object must contain `currency` property.' );
		}

		if ( ! isset( $object->value ) ) {
			throw new InvalidArgumentException( 'Object must contain `value` property.' );
		}

		return new self(
			$object->currency,
			$object->value
		);
	}
}
