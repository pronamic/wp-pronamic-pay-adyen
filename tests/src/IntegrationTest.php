<?php

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Title: Adyen - Integration test
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase {
	public function test_config() {
		$integration = new Integration();

		$expected = __NAMESPACE__ . '\ConfigFactory';

		$class = $integration->get_config_factory_class();

		$this->assertEquals( $expected, $class );
		$this->assertTrue( class_exists( $class ) );
	}
}
