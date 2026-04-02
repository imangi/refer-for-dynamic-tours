<?php
/**
 * WooCommerce Bookings Blocks Cart Item Data Controller.
 *
 * @package WooCommerce Bookings
 * @since 3.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class is responsible for handling the cart item data display.
 * It replaces the default booking meta (Date/Time/Type) with a single combined format.
 */
class WC_Bookings_Blocks_Cart_Item_Data {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'replace_cart_item_data' ), 20 );
	}

	/**
	 * Remove the default WC_Booking_Cart_Manager::get_item_data callback
	 * and replace it with our customized version.
	 */
	public function replace_cart_item_data() {
		// Remove the default cart item data display.
		remove_filter( 'woocommerce_get_item_data', array( WC_Booking_Cart_Manager::get_instance(), 'get_item_data' ), 10 );

		// Add our simplified cart item data display.
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
	}

	/**
	 * Get cart item data for display.
	 * Replaces all booking meta with a single inline summary.
	 *
	 * @param array $other_data Other cart item data.
	 * @param array $cart_item  The cart item.
	 * @return array Modified cart item data.
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( empty( $cart_item['booking'] ) || empty( $cart_item['booking']['_booking_id'] ) ) {
			return $other_data;
		}

		$booking = get_wc_booking( $cart_item['booking']['_booking_id'] );
		if ( ! $booking ) {
			return $other_data;
		}

		$formatted_value = wc_bookings_get_inline_summary( $booking, array() );

		if ( $formatted_value ) {
			$other_data[] = array(
				'name'    => __( 'Details', 'woocommerce-bookings' ),
				'value'   => $formatted_value,
				'display' => '',
			);
		}

		return $other_data;
	}
}
