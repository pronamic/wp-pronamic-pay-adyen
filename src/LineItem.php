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
	 * Item id.
	 *
	 * @var string|null
	 */
	private $id;

	/**
	 * Item name (required).
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Description.
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * Quantity (required).
	 *
	 * @var int
	 */
	private $quantity;

	/**
	 * Amount (required).
	 *
	 * @var Amount
	 */
	private $amount;

	/**
	 * Tax.
	 *
	 * @var Amount|null
	 */
	private $tax;

	/**
	 * Category; physical or digital (required).
	 *
	 * @var string
	 */
	private $category;

	/**
	 * VAT category.
	 *
	 * @var int|null
	 */
	private $vat_category;

	/**
	 * Construct line item.
	 *
	 * @param string $name     Name.
	 * @param int    $quantity Quantity.
	 * @param Amount $amount   Amount.
	 * @param string $category Category.
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function __construct( $name, $quantity, Amount $amount, $category ) {
		$this->set_name( $name );
		$this->quantity = $quantity;
		$this->amount   = $amount;
		$this->set_category( $category );
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
	 * Get item name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set item name.
	 *
	 * @param string $name Name.
	 * @throws InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 50`.
	 */
	public function set_name( $name ) {
		DataHelper::validate_an( $name, 50 );

		$this->name = $name;
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
	public function set_description( $description ) {
		if ( null !== $description ) {
			DataHelper::validate_an( $description, 100 );
		}

		$this->description = $description;
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
	 * Get amount.
	 *
	 * @return Amount
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Get tax.
	 *
	 * @return Amount|null
	 */
	public function get_tax() {
		return $this->tax;
	}

	/**
	 * Set tax.
	 *
	 * @param Amount|null $tax Tax.
	 */
	public function set_tax( Amount $tax = null ) {
		$this->tax = $tax;
	}

	/**
	 * Get category.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Set category.
	 *
	 * @param string $category Product category: PHYSICAL or DIGITAL.
	 * @throws InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 8`.
	 */
	public function set_category( $category ) {
		DataHelper::validate_an( $category, 8 );

		$this->category = $category;
	}

	/**
	 * Get VAT category.
	 *
	 * @return int|null
	 */
	public function get_vat_category() {
		return $this->vat_category;
	}

	/**
	 * Set VAT category.
	 *
	 * @param int|null $vat_category VAT category.
	 */
	public function set_vat_category( $vat_category ) {
		$this->vat_category = $vat_category;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		if ( null !== $this->id ) {
			$object->id = $this->id;
		}

		$object->name = $this->name;

		if ( null !== $this->description ) {
			$object->description = $this->description;
		}

		$object->quantity = $this->quantity;
		$object->amount   = $this->amount->get_json();

		if ( null !== $this->tax ) {
			$object->tax = $this->tax->get_json();
		}

		$object->category = $this->category;

		if ( null !== $this->vat_category ) {
			$object->vatCategory = $this->vat_category;
		}

		return $object;
	}
}
