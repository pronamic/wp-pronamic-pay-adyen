<?php
/**
 * Application info
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Application info
 *
 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo
 * @link https://docs.adyen.com/development-resources/building-adyen-solutions
 *
 * @author  Remco Tolsma
 * @version 1.0.0
 * @since   1.0.0
 */
class ApplicationInfo implements \JsonSerializable {
	/**
	 * Adyen-developed software, such as libraries and plugins, used to interact with the Adyen API. For example, Magento plugin, Java API library, etc.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-adyenLibrary
	 * @var object|null
	 */
	public $adyen_library;

	/**
	 * Adyen-developed software to get payment details. For example, Checkout SDK, Secured Fields SDK, etc.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-adyenPaymentSource
	 * @var object|null
	 */
	public $adyen_payment_source;

	/**
	 * Third-party developed platform used to initiate payment requests. For example, Magento, Zuora, etc.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-externalPlatform
	 * @var object|null
	 */
	public $external_platform;

	/**
	 * Merchant developed software, such as cashier application, used to interact with the Adyen API.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-merchantApplication
	 * @var object|null
	 */
	public $merchant_application;

	/**
	 * Merchant device information.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-merchantDevice
	 * @var object|null
	 */
	public $merchant_device;

	/**
	 * Shopper interaction device, such as terminal, mobile device or web browser, to initiate payment requests.
	 *
	 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_applicationInfo-shopperInteractionDevice
	 * @var object|null
	 */
	public $shopper_interaction_device;

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$properties = Util::filter_null(
			array(
				'adyenLibrary'             => $this->adyen_library,
				'adyenPaymentSource'       => $this->adyen_payment_source,
				'externalPlatform'         => $this->external_platform,
				'merchantApplication'      => $this->merchant_application,
				'merchantDevice'           => $this->merchant_device,
				'shopperInteractionDevice' => $this->shopper_interaction_device,
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
