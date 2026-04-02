<?php
/**
 * WooCommerce Bookings Blocks Order Confirmation Controller.
 *
 * @package WooCommerce Bookings
 * @since 3.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class is responsible for handling the order confirmation template.
 */
class WC_Bookings_Blocks_Order_Confirmation {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Remove the default booking_display hook and add our customized version.
		add_action( 'init', array( $this, 'replace_booking_display' ), 20 );
	}

	/**
	 * Remove the default WC_Booking_Order_Manager::booking_display callback
	 * and replace it with our customized version.
	 */
	public function replace_booking_display() {
		// Hint: For this to work, this class needs to be instantiated after WC_Booking_Order_Manager.
		remove_action( 'woocommerce_order_item_meta_end', array( WC_Booking_Order_Manager::instance(), 'booking_display' ) );

		// Add booking summary display.
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'booking_summary_display' ), 10, 3 );
	}

	/**
	 * Display simplified booking information.
	 * Format: "Booking #42 · Wednesday, January 8, 2026 at 12:00 pm with Marianne"
	 *
	 * @param int           $item_id The order item ID.
	 * @param WC_Order_Item $item    The order item.
	 * @param WC_Order      $order   The order.
	 */
	public function booking_summary_display( $item_id, $item, $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! $item_id || ! $item || ! $item->get_id() || ! $order || ! $order->get_id() ) {
			return;
		}

		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );
		if ( empty( $booking_ids ) ) {
			return;
		}

		foreach ( $booking_ids as $booking_id ) {
			try {
				$booking = new WC_Booking( $booking_id );
			} catch ( Exception $e ) {
				continue;
			}

			$formatted_string = wc_bookings_get_inline_summary(
				$booking,
				array(
					'include_booking_id' => false,
				)
			);

			if ( $formatted_string ) {
				printf(
					'<div class="wc-booking-summary-inline">%1$s</div>',
					esc_html( $formatted_string )
				);
			}
		}
	}
}
