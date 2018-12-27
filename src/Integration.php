<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Integration {
	public function __construct() {
		$this->id         = 'adyen';
		$this->name       = 'Adyen';
		$this->url        = 'http://www.adyen.com/';
		$this->provider   = 'adyen';
	}

	public function get_config_factory_class() {

	}
}
