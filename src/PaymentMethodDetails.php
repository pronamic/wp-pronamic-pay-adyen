<?php
/**
 * Payment method
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use JsonSerializable;

/**
 * Payment method details class
 */
class PaymentMethodDetails implements JsonSerializable {
	/**
	 * Type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Construct a payment method details object.
	 *
	 * @param string $type Type.
	 */
	public function __construct( $type ) {
		$this->type = $type;
	}

	/**
	 * JSON serialize.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return (object) get_object_vars( $this );
	}
}
