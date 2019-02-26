<?php
/**
 * Line items.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;

/**
 * Line items.
 *
 * @author  Reüel van der Steege
 * @version 1.0.0
 * @since   1.0.0
 */
class LineItems {
	/**
	 * Line items.
	 *
	 * @var array
	 */
	private $line_items;

	/**
	 * Construct line items.
	 *
	 * @param array $items Line items.
	 */
	public function __construct( $items = null ) {
		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->add_item( $item );
			}
		}
	}

	/**
	 * Create and add new line item.
	 *
	 * @param string $name     Name.
	 * @param int    $quantity Quantity.
	 * @param Amount $amount   Amount.
	 * @param string $category Category.
	 *
	 * @return LineItem
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function new_item( $name, $quantity, Amount $amount, $category ) {
		$item = new LineItem( $name, $quantity, $amount, $category );

		$this->add_item( $item );

		return $item;
	}

	/**
	 * Add line item.
	 *
	 * @param LineItem $item Line item.
	 */
	public function add_item( LineItem $item ) {
		$this->line_items[] = $item;
	}

	/**
	 * Get line items.
	 *
	 * @return LineItem[]
	 */
	public function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Get JSON.
	 *
	 * @return array|null
	 */
	public function get_json() {
		$data = array_map(
			function( LineItem $item ) {
				return $item->get_json();
			},
			$this->get_line_items()
		);

		return $data;
	}
}
