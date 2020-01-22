<?php
/**
 * Payment method SEPA Direct Debit
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment method SEPA Direct Debit
 *
 * @author  Reüel van der Steege
 * @version 1.1.0
 * @since   1.1.0
 */
class PaymentMethodSepaDirectDebit extends PaymentMethod {
	/**
	 * IBAN.
	 *
	 * @var string
	 */
	private $iban;

	/**
	 * Owner name.
	 *
	 * @var string
	 */
	private $owner_name;

	/**
	 * Construct a payment method.
	 *
	 * @param string $type       Adyen payment method type.
	 * @param string $iban       IBAN.
	 * @param string $owner_name Owner name.
	 */
	public function __construct( $type, $iban, $owner_name ) {
		parent::__construct( $type );

		$this->iban       = $iban;
		$this->owner_name = $owner_name;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = parent::get_json();

		// Properties.
		$object->iban      = $this->iban;
		$object->ownerName = $this->owner_name;

		// Return object.
		return $object;
	}
}
