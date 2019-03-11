<?php
/**
 * Payment method iDEAL
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment method iDEAL
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class PaymentMethodIDeal extends PaymentMethod {
	/**
	 * Issuer.
	 *
	 * @var string
	 */
	private $issuer;

	/**
	 * Construct a payment method.
	 *
	 * @param string $type   Adyen payment method type.
	 * @param string $issuer Adyen iDEAL issuer.
	 */
	public function __construct( $type, $issuer ) {
		parent::__construct( $type );

		$this->issuer = $issuer;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = parent::get_json();

		$properties = (array) $object;

		// Issuer.
		$properties['issuer'] = $this->issuer;

		// Return object.
		$object = (object) $properties;

		return $object;
	}
}
