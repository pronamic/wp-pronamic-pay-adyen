<?php
/**
 * Security
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

/**
 * Title: Security
 * Description:
 * Copyright: 2005-2022 Pronamic
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 1.0.7
 * @since   1.0.7
 */
class Security {
	/**
	 * Indicator for the begin of an certificate
	 *
	 * @var string
	 */
	const CERTIFICATE_BEGIN = '-----BEGIN CERTIFICATE-----';

	/**
	 * Indicator for the end of an certificate
	 *
	 * @var string
	 */
	const CERTIFICATE_END = '-----END CERTIFICATE-----';

	/**
	 * Get the sha1 fingerprint from the specified certificate
	 *
	 * @param string $certificate Certificate.
	 *
	 * @return null|string Fingerprint or null on failure
	 */
	public static function get_sha_fingerprint( $certificate ) {
		return self::get_fingerprint( $certificate, 'sha1' );
	}

	/**
	 * Get the fingerprint from the specified certificate
	 *
	 * @param string      $certificate Certificate.
	 * @param null|string $hash        Hash.
	 * @return null|string Fingerprint or null on failure.
	 */
	public static function get_fingerprint( $certificate, $hash = null ) {
		// The openssl_x509_read() function will throw an warning if the supplied
		// parameter cannot be coerced into an X509 certificate
		// @codingStandardsIgnoreStart
		$resource = @openssl_x509_read( $certificate );
		// @codingStandardsIgnoreEnd

		if ( false === $resource ) {
			return null;
		}

		$output = '';

		$result = \openssl_x509_export( $resource, $output );

		if ( false === $result ) {
			return null;
		}

		$output = \str_replace( self::CERTIFICATE_BEGIN, '', $output );
		$output = \str_replace( self::CERTIFICATE_END, '', $output );

		$output = \strval( $output );

		// Base64 decode.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$fingerprint = \base64_decode( $output );

		// Hash.
		if ( null !== $hash ) {
			$fingerprint = \hash( $hash, $fingerprint );
		}

		/*
		 * Uppercase
		 *
		 * Cannot find private certificate file with fingerprint: b4845cb5cbcee3e1e0afef2662552a2365960e72
		 * (Note: Some acquirers only accept fingerprints in uppercase. Make the value of "KeyName" in your XML data uppercase.).
		 * https://www.ideal-checkout.nl/simulator/
		 *
		 * @since 1.1.11
		 */
		$fingerprint = \strtoupper( $fingerprint );

		return $fingerprint;
	}
}
