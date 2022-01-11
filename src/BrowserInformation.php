<?php
/**
 * Browser information
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Browser information
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_browserInfo
 * @link https://docs.adyen.com/development-resources/building-adyen-solutions
 *
 * @author  Reüel van der Steege
 * @version 1.1.1
 * @since   1.1.1
 */
class BrowserInformation implements \JsonSerializable {
	/**
	 * The accept header value of the shopper's browser.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_browserInfo-acceptHeader
	 * @var string|null
	 */
	private $accept_header;

	/**
	 * The color depth of the shopper's browser in bits per pixel. This should be obtained by using the browser's
	 * screen.colorDepth property. Accepted values: 1, 4, 8, 15, 16, 24, 32 or 48 bit color depth.
	 *
	 * @link
	 * @var int|null
	 */
	private $color_depth;

	/**
	 * Boolean value indicating if the shopper's browser is able to execute Java.
	 *
	 * @link
	 * @var bool|null
	 */
	private $java_enabled;

	/**
	 * Boolean value indicating if the shopper's browser is able to execute JavaScript.
	 * A default 'true' value is assumed if the field is not present.
	 *
	 * @link
	 * @var bool|null
	 */
	private $javascript_enabled;

	/**
	 * The navigator.language value of the shopper's browser (as defined in IETF BCP 47).
	 *
	 * @link
	 * @var string|null
	 */
	private $language;

	/**
	 * The total height of the shopper's device screen in pixels.
	 *
	 * @link
	 * @var int|null
	 */
	private $screen_height;

	/**
	 * The total width of the shopper's device screen in pixels.
	 *
	 * @link
	 * @var int|null
	 */
	private $screen_width;

	/**
	 * Time difference between UTC time and the shopper's browser local time, in minutes.
	 *
	 * @link
	 * @var int|null
	 */
	private $timezone_offset;

	/**
	 * The user agent value of the shopper's browser.
	 *
	 * @link
	 * @var string|null
	 */
	private $user_agent;

	/**
	 * Get accept header.
	 *
	 * @return string|null
	 */
	public function get_accept_header() {
		return $this->accept_header;
	}

	/**
	 * Set accept header.
	 *
	 * @param string|null $accept_header Accept header.
	 * @return void
	 */
	public function set_accept_header( $accept_header ) {
		$this->accept_header = $accept_header;
	}

	/**
	 * Get color depth.
	 *
	 * @return int|null
	 */
	public function get_color_depth() {
		return $this->color_depth;
	}

	/**
	 * Set color depth.
	 *
	 * @param int|null $color_depth Color depth.
	 * @return void
	 */
	public function set_color_depth( $color_depth ) {
		$this->color_depth = $color_depth;
	}

	/**
	 * Get java enabled.
	 *
	 * @return bool|null
	 */
	public function get_java_enabled() {
		return $this->java_enabled;
	}

	/**
	 * Set java enabled.
	 *
	 * @param bool|null $java_enabled Java enabled.
	 * @return void
	 */
	public function set_java_enabled( $java_enabled ) {
		$this->java_enabled = $java_enabled;
	}

	/**
	 * Get javascript enabled.
	 *
	 * @return bool|null
	 */
	public function get_javascript_enabled() {
		return $this->javascript_enabled;
	}

	/**
	 * Set javascript enabled.
	 *
	 * @param bool|null $javascript_enabled Javascript enabled.
	 * @return void
	 */
	public function set_javascript_enabled( $javascript_enabled ) {
		$this->javascript_enabled = $javascript_enabled;
	}

	/**
	 * Get language.
	 *
	 * @return string|null
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * Set language.
	 *
	 * @param string|null $language Language.
	 * @return void
	 */
	public function set_language( $language ) {
		$this->language = $language;
	}

	/**
	 * Get screen height.
	 *
	 * @return int|null
	 */
	public function get_screen_height() {
		return $this->screen_height;
	}

	/**
	 * Set screen height.
	 *
	 * @param int|null $screen_height Screen height.
	 * @return void
	 */
	public function set_screen_height( $screen_height ) {
		$this->screen_height = $screen_height;
	}

	/**
	 * Get screen width.
	 *
	 * @return int|null
	 */
	public function get_screen_width() {
		return $this->screen_width;
	}

	/**
	 * Set screen width.
	 *
	 * @param int|null $screen_width Screen width.
	 * @return void
	 */
	public function set_screen_width( $screen_width ) {
		$this->screen_width = $screen_width;
	}

	/**
	 * Get timezone offset.
	 *
	 * @return int|null
	 */
	public function get_timezone_offset() {
		return $this->timezone_offset;
	}

	/**
	 * Set timezone offset.
	 *
	 * @param int|null $timezone_offset Timezone offset.
	 * @return void
	 */
	public function set_timezone_offset( $timezone_offset ) {
		$this->timezone_offset = $timezone_offset;
	}

	/**
	 * Get user agent.
	 *
	 * @return string|null
	 */
	public function get_user_agent() {
		return $this->user_agent;
	}

	/**
	 * Set user agent.
	 *
	 * @param string|null $user_agent User agent.
	 * @return void
	 */
	public function set_user_agent( $user_agent ) {
		$this->user_agent = $user_agent;
	}

	/**
	 * Create browser information from object.
	 *
	 * @param object $object Object.
	 * @return BrowserInformation
	 */
	public static function from_object( $object ) {
		$browser_information = new self();

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		if ( isset( $object->acceptHeader ) ) {
			$browser_information->set_accept_header( $object->acceptHeader );
		}

		if ( isset( $object->colorDepth ) ) {
			$browser_information->set_color_depth( $object->colorDepth );
		}

		if ( isset( $object->javaEnabled ) ) {
			$browser_information->set_java_enabled( $object->javaEnabled );
		}

		if ( isset( $object->javaScriptEnabled ) ) {
			$browser_information->set_javascript_enabled( $object->javaScriptEnabled );
		}

		if ( isset( $object->language ) ) {
			$browser_information->set_language( $object->language );
		}

		if ( isset( $object->screenHeight ) ) {
			$browser_information->set_screen_height( $object->screenHeight );
		}

		if ( isset( $object->screenWidth ) ) {
			$browser_information->set_screen_width( $object->screenWidth );
		}

		if ( isset( $object->timeZoneOffset ) ) {
			$browser_information->set_timezone_offset( $object->timeZoneOffset );
		}

		if ( isset( $object->userAgent ) ) {
			$browser_information->set_user_agent( $object->userAgent );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Adyen JSON object.

		return $browser_information;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'acceptHeader'      => $this->get_accept_header(),
				'colorDepth'        => $this->get_color_depth(),
				'javaEnabled'       => $this->get_java_enabled(),
				'javaScriptEnabled' => $this->get_javascript_enabled(),
				'language'          => $this->get_language(),
				'screenHeight'      => $this->get_screen_height(),
				'screenWidth'       => $this->get_screen_width(),
				'timeZoneOffset'    => $this->get_timezone_offset(),
				'userAgent'         => $this->get_user_agent(),
			)
		);

		$object = (object) $properties;

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
