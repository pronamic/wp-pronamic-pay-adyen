<?php
/**
 * Name
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use InvalidArgumentException;

/**
 * Name
 *
 * @link https://docs.adyen.com/developers/api-reference/common-api/name
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class Name {
	/**
	 * First name.
	 *
	 * @var string
	 */
	private $first_name;

	/**
	 * Gender.
	 *
	 * @var string
	 */
	private $gender;

	/**
	 * The name's infix, if applicable.
	 *
	 * @var string|null
	 */
	private $infix;

	/**
	 * Last name.
	 *
	 * @var string
	 */
	private $last_name;

	/**
	 * Construct shopper name.
	 *
	 * @param string $first_name First name.
	 * @param string $last_name  Last name.
	 * @param string $gender     Gender.
	 */
	public function __construct( $first_name, $last_name, $gender ) {
		$this->set_first_name( $first_name );
		$this->set_last_name( $last_name );
		$this->set_gender( $gender );
	}

	/**
	 * Get first name.
	 *
	 * @return string
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * Set first name.
	 *
	 * @param string $first_name First name.
	 */
	public function set_first_name( $first_name ) {
		$this->first_name = $first_name;
	}

	/**
	 * Get gender.
	 *
	 * @return string
	 */
	public function get_gender() {
		return $this->gender;
	}

	/**
	 * Set gender.
	 *
	 * @param string $gender Gender.
	 */
	public function set_gender( $gender ) {
		$this->gender = $gender;
	}

	/**
	 * Get infix.
	 *
	 * @return string|null
	 */
	public function get_infix() {
		return $this->infix;
	}

	/**
	 * Set infix.
	 *
	 * @param string|null $infix Infix.
	 */
	public function set_infix( $infix ) {
		if ( null !== $infix && mb_strlen( $infix ) > 20 ) {
			throw new InvalidArgumentException(
				sprintf(
					'Given infix `%s` is longer then 20 characters.',
					$infix
				)
			);
		}

		$this->infix = $infix;
	}

	/**
	 * Get last name.
	 *
	 * @return string
	 */
	public function get_last_name() {
		return $this->last_name;
	}

	/**
	 * Set last name.
	 *
	 * @param string $last_name Last name.
	 */
	public function set_last_name( $last_name ) {
		$this->last_name = $last_name;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		// First name.
		$object->firstName = $this->get_first_name();

		// Gender.
		$object->gender = $this->get_gender();

		// Infix.
		$infix = $this->get_infix();

		if ( null !== $infix ) {
			$object->infix = $infix;
		}

		// Last name.
		$object->lastName = $this->get_last_name();

		// Return object.
		return $object;
	}
}
