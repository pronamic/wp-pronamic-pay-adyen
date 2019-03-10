<?php
/**
 * Redirect information test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Redirect information test
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class RedirectInformationTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test redirect information.
	 */
	public function test_redirect_information() {
		$redirect_information = new RedirectInformation( 'GET', 'https://test.adyen.com/' );

		$this->assertEquals( 'GET', $redirect_information->get_method() );
		$this->assertEquals( 'https://test.adyen.com/', $redirect_information->get_url() );
	}

	/**
	 * Test redirect data.
	 */
	public function test_redirect_data() {
		$redirect_information = new RedirectInformation( 'POST', 'https://test.adyen.com/' );
		$redirect_information->set_data( (object) array() );

		$this->assertEquals( 'GET', $redirect_information->get_method() );
		$this->assertEquals( 'https://test.adyen.com/', $redirect_information->get_url() );
		$this->assertEquals( (object) array(), $redirect_information->get_data() );
	}
}
