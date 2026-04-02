<?php
/**
 * Bookings Email Preview Class
 *
 * @package woocommerce-bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bookings Email Preview Class
 */
class WC_Booking_Email_Previews {
	/**
	 * Init method.
	 */
	public function init() {
		add_filter( 'woocommerce_prepare_email_for_preview', array( $this, 'prepare_email_for_preview' ) );

		// Block editor preview context.
		add_filter( 'woocommerce_email_editor_integration_personalizer_context_data', array( $this, 'add_preview_booking_context' ), 10, 2 );
	}

	/**
	 * Prepare email for preview
	 *
	 * @param WC_Email $email Email object.
	 * @return WC_Email
	 */
	public function prepare_email_for_preview( $email ) {
		// Only modify booking emails.
		if ( false === strpos( get_class( $email ), 'Booking' ) ) {
			return $email;
		}

		// Create a Bookings object.
		$booking = new WC_Booking();
		$booking->set_id( 999999 );
		$booking->set_order_id( $email->object->get_ID() );
		$booking->set_start( strtotime( '+1 week' ) );
		$booking->set_end( strtotime( '+1 week +1 day' ) );
		$booking->set_status( 'email_preview' );

		$email->object = $booking;

		// Modify the email heading.
		$email->subject = str_replace( '{product_title}', 'Dummy Product', $email->subject );

		// Modify the email content.
		if ( isset( $email->settings['subject'] ) ) {
			$email->settings['subject'] = str_replace( '{product_title}', 'Dummy Product', $email->settings['subject'] );
		}

		return $email;
	}

	/**
	 * Add preview booking context for block editor.
	 *
	 * This provides sample booking data for the block editor preview when no real booking is available.
	 *
	 * @since 3.2.0
	 *
	 * @param array    $context The context data.
	 * @param WC_Email $email   The email object.
	 * @return array
	 */
	public function add_preview_booking_context( $context, $email ) {
		// Only add preview context for booking emails.
		if ( ! $email || false === strpos( get_class( $email ), 'Booking' ) ) {
			return $context;
		}

		// Only add preview context if no real booking is set.
		if ( ! empty( $context['booking'] ) || ! empty( $context['booking_id'] ) ) {
			return $context;
		}

		if ( ! isset( $email->object ) ) {
			return $context;
		}

		if ( ! $email->object instanceof WC_Booking ) {
			return $context;
		}

		// Only add preview context if the booking is in preview mode.
		if ( $email->object->get_status() !== 'email_preview' ) {
			return $context;
		}

		// Capture the booking before we overwrite $email->object.
		$booking = $email->object;

		// Create a mock order with dummy customer data.
		// Setting $email->object to this order allows WooCommerce's customer tags to work,
		// since prepare_context_data() sets context['order'] from $email->object after this filter.
		$mock_order = new WC_Order();
		$mock_order->set_billing_first_name( 'John' );
		$mock_order->set_billing_last_name( 'Doe' );
		$mock_order->set_billing_email( 'john.doe@example.com' );

		// Set the email object to the mock order so WooCommerce's customer tags work.
		$email->object = $mock_order;

		// Store the booking in context for our booking-specific tags.
		$context['booking']    = $booking;
		$context['booking_id'] = $booking->get_id();

		return $context;
	}
}
