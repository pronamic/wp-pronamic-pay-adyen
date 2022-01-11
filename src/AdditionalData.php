<?php
/**
 * Additional data.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Additional data.
 *
 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v67/post/payments__reqParam_additionalData
 * @link https://docs.adyen.com/payment-methods/cards/send-additional-data-for-cards
 * @link https://docs.adyen.com/development-resources/test-cards/test-card-numbers#test-submitting-level-2-3-data
 *
 * @author  Reüel van der Steege
 * @version 1.4.0
 * @since   1.4.0
 */
class AdditionalData implements \JsonSerializable {
	/**
	 * Customer code, if supplied by a customer. Max length: 25 characters.
	 * Required for Level 2 and Level 3 data.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-customerReference
	 * @var string|null
	 */
	public $esd_customer_reference;

	/**
	 * Destination country code. Max length: 3 characters.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-destinationCountryCode
	 * @var string|null
	 */
	public $esd_destination_country_code;

	/**
	 * The postal code of a destination address. Max length: 10 characters.
	 * Required for American Express.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-destinationPostalCode
	 * @var string|null
	 */
	public $esd_destination_postal_code;

	/**
	 * Destination state or province code.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-destinationStateProvinceCode
	 * @var string|null
	 */
	public $esd_destination_state_province_code;

	/**
	 * Duty amount, in minor units.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-dutyAmount
	 * @var string|null
	 */
	public $esd_duty_amount;

	/**
	 * Shipping amount, in minor units. Max length: 12 characters.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-freightAmount
	 * @var string|null
	 */
	public $esd_freight_amount;

	/**
	 * Order date. Format: `ddMMyy`. Max length: 6 characters.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-orderDate
	 * @var \DateTime|null
	 */
	public $esd_order_date;

	/**
	 * The postal code of a "ship-from" address. Max length: 10 characters.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v67/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-shipFromPostalCode
	 * @var string|null
	 */
	public $esd_ship_from_postal_code;

	/**
	 * Total tax amount, in minor units. Max length: 12 characters.
	 * Required for Level 2 and Level 3 data.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v67/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-totalTaxAmount
	 * @var string|null
	 */
	public $esd_total_tax_amount;

	/**
	 * Line items.
	 *
	 * @var LineItems|null
	 */
	public $line_items;

	/**
	 * Get line_items.
	 *
	 * @return LineItems|null
	 */
	public function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Set line items.
	 *
	 * @param LineItems|null $line_items Line items.
	 * @return void
	 */
	public function set_line_items( $line_items ) {
		$this->line_items = $line_items;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = array(
			'enhancedSchemeData.customerReference'      => $this->esd_customer_reference,
			'enhancedSchemeData.destinationCountryCode' => $this->esd_destination_country_code,
			'enhancedSchemeData.destinationPostalCode'  => $this->esd_destination_postal_code,
			'enhancedSchemeData.destinationStateProvinceCode' => $this->esd_destination_state_province_code,
			'enhancedSchemeData.dutyAmount'             => $this->esd_duty_amount,
			'enhancedSchemeData.freightAmount'          => $this->esd_freight_amount,
			'enhancedSchemeData.orderDate'              => \is_null( $this->esd_order_date ) ? null : $this->esd_order_date->format( 'dmy' ),
			'enhancedSchemeData.shipFromPostalCode'     => $this->esd_ship_from_postal_code,
			'enhancedSchemeData.totalTaxAmount'         => $this->esd_total_tax_amount,
		);

		if ( null !== $this->line_items ) {
			$line_items = $this->line_items->get_line_items();

			$index = 1;

			foreach ( $line_items as $item ) {
				$item_properties = array(
					/**
					 * Item description. Max length: 26 characters.
					 *
					 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-itemDetailLine---itemNr----description
					 * @var string|null
					 */
					'enhancedSchemeData.itemDetailLine' . $index . '.description' => $item->get_description(),

					/**
					 * Product code. Max length: 12 characters.
					 *
					 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-itemDetailLine---itemNr----productCode
					 * @var string|null
					 */
					'enhancedSchemeData.itemDetailLine' . $index . '.productCode' => $item->get_id(),

					/**
					 * Quantity, specified as an integer value. Value must be greater than 0. Max length: 12 characters.
					 *
					 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-itemDetailLine---itemNr----quantity
					 * @var string|null
					 */
					'enhancedSchemeData.itemDetailLine' . $index . '.quantity'    => $item->get_quantity(),

					/**
					 * Total amount, in minor units. For example, 2000 means USD 20.00. Max length: 12 characters.
					 *
					 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_additionalData-AdditionalDataLevel23-enhancedSchemeData-itemDetailLine---itemNr----totalAmount
					 * @var string|null
					 */
					'enhancedSchemeData.itemDetailLine' . $index . '.totalAmount' => $item->get_amount_including_tax(),
				);

				$item_properties = Util::filter_null( $item_properties );

				$item_properties = \array_map( '\strval', $item_properties );

				$properties = array_merge( $properties, $item_properties );

				$index++;
			}
		}

		$object = (object) Util::filter_null( $properties );

		return $object;
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
}
