<?php
/**
 * Line item.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;

/**
 * Line item.
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class LineItem {
	/**
	 * Amount excluding tax.
	 *
	 * @var int|null
	 */
	private $amount_excluding_tax;

	/**
	 * Amount including tax.
	 *
	 * @var int|null
	 */
	private $amount_including_tax;

	/**
	 * Description.
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * Item id.
	 *
	 * @var string|null
	 */
	private $id;

	/**
	 * Quantity.
	 *
	 * @var int|null
	 */
	private $quantity;

	/**
	 * Tax amount.
	 *
	 * @var int|null
	 */
	private $tax_amount;

	/**
	 * Tax category (high, low, none, zero).
	 *
	 * @var string|null
	 */
	private $tax_category;

	/**
	 * Tax percentage.
	 *
	 * @var int|null
	 */
	private $tax_percentage;

	/**
	 * Construct line item.
	 *
	 * @param string $description          Name.
	 * @param int    $quantity             Quantity.
	 * @param int    $amount_including_tax Amount (including tax).
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function __construct( $description, $quantity, $amount_including_tax ) {
		$this->set_description( $description );
		$this->set_quantity( $quantity );
		$this->set_amount_including_tax( $amount_including_tax );
	}

	/**
	 * Get amount excluding tax.
	 *
	 * @return int
	 */
	public function get_amount_excluding_tax() {
		return $this->amount_excluding_tax;
	}

	/**
	 * Set amount excluding tax.
	 *
	 * @param int $amount_excluding_tax Amount excluding tax.
	 */
	public function set_amount_excluding_tax( $amount_excluding_tax = null ) {
		$this->amount_excluding_tax = $amount_excluding_tax;
	}

	/**
	 * Get amount excluding tax.
	 *
	 * @return int
	 */
	public function get_amount_including_tax() {
		return $this->amount_including_tax;
	}

	/**
	 * Set amount including tax.
	 *
	 * @param int $amount_including_tax Amount excluding tax.
	 */
	public function set_amount_including_tax( $amount_including_tax = null ) {
		$this->amount_including_tax = $amount_including_tax;
	}

	/**
	 * Get item description.
	 *
	 * @return string|null
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set item description.
	 *
	 * @param string|null $description Description.
	 * @throws InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 100`.
	 */
	public function set_description( $description = null ) {
		$this->description = $description;
	}

	/**
	 * Get item ID.
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set item ID.
	 *
	 * @param string|null $id ID.
	 */
	public function set_id( $id = null ) {
		$this->id = $id;
	}

	/**
	 * Get quantity.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Get quantity.
	 *
	 * @param int $quantity Quantity.
	 */
	public function set_quantity( $quantity = null ) {
		$this->quantity = $quantity;
	}

	/**
	 * Get tax amount.
	 *
	 * @return int|null
	 */
	public function get_tax_amount() {
		return $this->tax_amount;
	}

	/**
	 * Set tax amount.
	 *
	 * @param int|null $tax_amount Tax amount.
	 */
	public function set_tax_amount( $tax_amount = null ) {
		$this->tax_amount = $tax_amount;
	}

	/**
	 * Get tax category.
	 *
	 * @return int|string
	 */
	public function get_tax_category() {
		return $this->tax_category;
	}

	/**
	 * Set tax category.
	 *
	 * @param int|string $tax_category Tax category.
	 */
	public function set_tax_category( $tax_category ) {
		$this->tax_category = $tax_category;
	}

	/**
	 * Get tax percentage.
	 *
	 * @return int|null
	 */
	public function get_tax_percentage() {
		return $this->tax_percentage;
	}

	/**
	 * Set tax percentage.
	 *
	 * @param int|null $tax_percentage Tax percentage.
	 */
	public function set_tax_percentage( $tax_percentage ) {
		$this->tax_percentage = $tax_percentage;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		if ( null !== $this->amount_excluding_tax ) {
			$object->amountExcludingTax = $this->amount_excluding_tax;
		}

		if ( null !== $this->amount_including_tax ) {
			$object->amountIncludingTax = $this->amount_including_tax;
		}

		if ( null !== $this->description ) {
			$object->description = $this->description;
		}

		if ( null !== $this->id ) {
			$object->id = $this->id;
		}

		if ( null !== $this->quantity ) {
			$object->quantity = $this->quantity;
		}

		if ( null !== $this->tax_amount ) {
			$object->taxAmount = $this->tax_amount;
		}

		if ( null !== $this->tax_category ) {
			$object->taxCategory = $this->tax_category;
		}

		if ( null !== $this->tax_percentage ) {
			$object->taxPercentage = $this->tax_percentage;
		}

		return $object;
	}
}
