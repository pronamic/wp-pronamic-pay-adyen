<?php
/**
 * Payment request helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Adyen
 */

namespace Pronamic\WordPress\Pay\Gateways\Adyen;

use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Payment request helper
 *
 * @author  Remco Tolsma
 * @version 1.1.1
 * @since   1.0.0
 */
class PaymentRequestHelper {
	/**
	 * Complement WordPress Pay payment to Adyen payment request.
	 *
	 * @param Payment                $payment WordPress Pay payment to convert.
	 * @param AbstractPaymentRequest $request Adyen payment request.
	 * @return void
	 * @throws \Exception Throws exception on invalid metadata.
	 */
	public static function complement( Payment $payment, AbstractPaymentRequest $request ) {
		// Channel.
		$request->set_channel( Channel::WEB );

		// Shopper.
		$request->set_shopper_statement( $payment->get_description() );

		// Customer.
		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			/*
			 * When sending in the shopper reference we always create a recurring contract. If you would not
			 * like to store the details, we recommend to exclude the shopper reference.
			 *
			 * $user_id = $customer->get_user_id();
			 * $request->set_shopper_reference( \is_null( $user_id ) ? null : \strval( $user_id ) );
			 */
			$request->set_shopper_ip( $customer->get_ip_address() );
			$request->set_shopper_locale( $customer->get_locale() );
			$request->set_telephone_number( $customer->get_phone() );
			$request->set_shopper_email( $customer->get_email() );

			// Shopper name.
			$name = $customer->get_name();

			if ( null !== $name ) {
				$shopper_name = new Name(
					(string) $name->get_first_name(),
					(string) $name->get_last_name(),
					GenderTransformer::transform( $customer->get_gender() )
				);

				$request->set_shopper_name( $shopper_name );
			}

			// Date of birth.
			$request->set_date_of_birth( $customer->get_birth_date() );
		}

		// Billing address.
		$billing_address = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$address = AddressTransformer::transform( $billing_address );

			$request->set_billing_address( $address );
		}

		// Delivery address.
		$shipping_address = $payment->get_shipping_address();

		if ( null !== $shipping_address ) {
			$address = AddressTransformer::transform( $shipping_address );

			$request->set_delivery_address( $address );
		}

		// Lines.
		$lines = $payment->get_lines();

		if ( null !== $lines ) {
			$line_items = $request->new_line_items();

			$i = 1;

			foreach ( $lines as $line ) {
				// Description.
				$description = $line->get_description();

				// Use line item name as fallback for description.
				if ( null === $description ) {
					/* translators: %s: item index */
					$description = sprintf( __( 'Item %s', 'pronamic_ideal' ), $i++ );

					if ( null !== $line->get_name() && '' !== $line->get_name() ) {
						$description = $line->get_name();
					}
				}

				$total_amount = $line->get_total_amount();

				$item = $line_items->new_item(
					(string) $description,
					(int) $line->get_quantity(),
					$total_amount->get_minor_units()->to_int()
				);

				$item->set_id( $line->get_id() );

				// Tax amount.
				if ( $total_amount instanceof TaxedMoney ) {
					$item->set_amount_excluding_tax( $total_amount->get_excluding_tax()->get_minor_units()->to_int() );

					$tax_amount = $total_amount->get_tax_amount();

					if ( null !== $tax_amount ) {
						$item->set_tax_amount( $tax_amount->get_minor_units()->to_int() );
						$item->set_tax_percentage( (int) $total_amount->get_tax_percentage() * 100 );
					}
				}
			}
		}

		/*
		 * Additional data.
		 */
		$additional_data = new AdditionalData();

		// Order date.
		$additional_data->esd_order_date = $payment->get_date();

		// Customer reference (required for Level 2/3).
		$additional_data->esd_customer_reference = '';

		// Tax amount (required for Level 2/3).
		$total_amount = $payment->get_total_amount();

		if ( $total_amount instanceof TaxedMoney ) {
			$tax_amount = $total_amount->get_tax_amount();

			if ( null !== $tax_amount ) {
				$additional_data->esd_total_tax_amount = $tax_amount->get_minor_units()->get_value();
			}
		}

		// Shipping amount.
		$shipping_amount = $payment->get_shipping_amount();

		if ( null !== $shipping_amount ) {
			$additional_data->esd_freight_amount = $shipping_amount->get_minor_units()->get_value();
		}

		// Destination address.
		$shipping_address = $payment->get_shipping_address();

		if ( null !== $shipping_address ) {
			// Postal code (required for Level 2/3 with Amex).
			$postal_code = $shipping_address->get_postal_code();

			if ( ! empty( $postal_code ) ) {
				$additional_data->esd_destination_postal_code = $postal_code;
			}

			// Country code.
			$country_code = $shipping_address->get_country_code();

			if ( ! empty( $country_code ) ) {
				$additional_data->esd_destination_country_code = $country_code;
			}
		}

		// Line items.
		$additional_data->set_line_items( $request->get_line_items() );

		$request->set_additional_data( $additional_data );

		// Metadata.
		$metadata = array();

		/**
		 * Filters the Adyen payment metadata.
		 *
		 * Maximum 20 key-value pairs per request. When exceeding, the "177" error occurs: "Metadata size exceeds limit".
		 *
		 * @param array $metadata Payment request metadata.
		 * @param Payment $payment Payment.
		 * @link https://docs.adyen.com/api-explorer/#/CheckoutService/v64/post/payments__reqParam_metadata
		 * @since 1.1.1 Added.
		 */
		$metadata = apply_filters( 'pronamic_pay_adyen_payment_metadata', $metadata, $payment );

		/*
		 * Maximum 20 key-value pairs per request. When exceeding, the "177" error occurs: "Metadata size exceeds limit".
		 *
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v51/payments__reqParam_metadata
		 * @link https://docs.adyen.com/api-explorer/#/PaymentSetupAndVerificationService/v41/paymentSession__reqParam_metadata
		 */
		if ( ! \is_array( $metadata ) ) {
			throw new \Exception( 'Adyen metadata must be an array.' );
		}

		if ( count( $metadata ) > 20 ) {
			throw new \Exception( 'Adyen metadata exceeds maximum of 20 items.' );
		}

		$request->set_metadata( $metadata );
	}
}
