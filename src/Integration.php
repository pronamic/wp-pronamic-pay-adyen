<?php

/**
 * Title: Adyen - Integration
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Adyen_Integration {
	public function __construct() {
		$this->id         = 'adyen';
		$this->name       = 'Adyen';
		$this->url        = 'http://www.adyen.com/';
		$this->provider   = 'adyen';
	}

	public function get_config_factory_class() {

	}

	public function get_config_class() {

	}

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_Adyen_Gateway';
	}
}
