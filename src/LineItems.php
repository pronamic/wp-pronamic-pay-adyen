<?php
/**
 * Line items
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Line items class
 */
class LineItems implements \JsonSerializable {
	/**
	 * Line items.
	 *
	 * @var array<int, LineItem>
	 */
	private $line_items;

	/**
	 * Construct line items.
	 *
	 * @param LineItem[] $items Line items.
	 */
	public function __construct( $items = null ) {
		$this->line_items = [];

		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->add_item( $item );
			}
		}
	}

	/**
	 * Create and add new line item.
	 *
	 * @param string $description          Name.
	 * @param int    $quantity             Quantity.
	 * @param int    $amount_including_tax Amount (including tax).
	 *
	 * @return LineItem
	 *
	 * @throws \InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function new_item( $description, $quantity, $amount_including_tax ) {
		$item = new LineItem( $description, $quantity, $amount_including_tax );

		$this->add_item( $item );

		return $item;
	}

	/**
	 * Add line item.
	 *
	 * @param LineItem $item Line item.
	 * @return void
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
	 * @return array<object>
	 */
	public function get_json() {
		$data = array_map(
			/**
			 * Get line item JSON.
			 *
			 * @param LineItem $item Line item.
			 * @return object
			 */
			function ( LineItem $item ) {
				return $item->get_json();
			},
			$this->get_line_items()
		);

		return $data;
	}

	/**
	 * JSON serialize.
	 *
	 * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return array<object>
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->get_json();
	}
}
