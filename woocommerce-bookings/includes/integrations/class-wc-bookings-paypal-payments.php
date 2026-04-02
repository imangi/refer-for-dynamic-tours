<?php
/**
 * PayPal Payments integration class.
 *
 * @package WooCommerce Bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Bookings_PayPal_Payments class.
 *
 * Handles compatibility with PayPal Payments express buttons on product pages.
 *
 * @since 3.2.0
 */
class WC_Bookings_PayPal_Payments {
	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_paypal_payments_product_supports_payment_request_button', array( $this, 'is_product_supported' ), 10, 2 );
	}

	/**
	 * Filter whether to display PayPal express pay buttons on product pages.
	 *
	 * Runs on the `woocommerce_paypal_payments_product_supports_payment_request_button` filter.
	 *
	 * @since 3.2.0
	 *
	 * @param bool        $is_supported Whether express pay buttons are supported on product pages.
	 * @param \WC_Product $product      The product object.
	 *
	 * @return bool Modified support status.
	 */
	public function is_product_supported( $is_supported, $product ) {
		if ( ! is_wc_booking_product( $product ) ) {
			return $is_supported;
		}

		return false;
	}
}
