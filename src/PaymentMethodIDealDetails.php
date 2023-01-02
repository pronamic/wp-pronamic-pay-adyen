<?php
/**
 * Payment method iDEAL details
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Payment method iDEAL details class
 */
class PaymentMethodIDealDetails extends PaymentMethodDetails {
	/**
	 * The iDEAL issuer value of the shopper's selected bank.
	 *
	 * @var string
	 */
	protected $issuer;

	/**
	 * Construct a payment method iDEAL details object.
	 *
	 * @param string $issuer The iDEAL issuer value of the shopper's selected bank.
	 */
	public function __construct( $issuer ) {
		parent::__construct( PaymentMethodType::IDEAL );

		$this->issuer = $issuer;
	}
}
