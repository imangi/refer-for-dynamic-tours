<?php
/**
 * Cron job handler.
 *
 * @package WooCommerce Bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Booking_Cron_Manager class.
 *
 * Handles cron jobs for bookings.
 */
class WC_Booking_Cron_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wc-booking-reminder', array( $this, 'send_booking_reminder' ) );
		add_action( 'wc-booking-complete', array( $this, 'maybe_mark_booking_complete' ) );
		add_action( 'wc-booking-remove-inactive-cart', array( $this, 'remove_inactive_booking_from_cart' ) );
		add_action( 'woocommerce_bookings_add_missing_attendance_status', array( $this, 'add_missing_attendance_status_batch' ) );
	}

	/**
	 * Send booking reminder email
	 */
	public function send_booking_reminder( $booking_id ) {
		$booking = get_wc_booking( $booking_id );
		if ( ! is_a( $booking, 'WC_Booking' ) || ! $booking->is_active() ) {
			return;
		}

		$mailer   = WC()->mailer();
		$reminder = $mailer->emails['WC_Email_Booking_Reminder'];
		$reminder->trigger( $booking_id );
	}

	/**
	 * Change the booking status if it wasn't previously cancelled
	 */
	public function maybe_mark_booking_complete( $booking_id ) {
		$booking = get_wc_booking( $booking_id );

		//Don't procede if id is not of a valid booking
		if ( ! is_a( $booking, 'WC_Booking' ) ) {
			return;
		}

		if ( 'cancelled' === get_post_status( $booking_id ) ) {
			$booking->schedule_events();
		} else {
			$this->mark_booking_complete( $booking );
		}
	}

	/**
	 * Change the booking status to complete
	 */
	public function mark_booking_complete( $booking ) {

		if ( wc_bookings_maybe_mark_completed_bookings_as_attended( $booking ) ) {
			$booking->set_attendance_status( 'attended' );
		}
		$booking->update_status( 'complete' );
	}

	/**
	 * Remove inactive booking
	 */
	public function remove_inactive_booking_from_cart( $booking_id ) {
		$booking = $booking_id ? get_wc_booking( $booking_id ) : false;
		if ( $booking_id && $booking && $booking->has_status( array( 'in-cart', 'was-in-cart' ) ) ) {
			wp_delete_post( $booking_id );
		}

		// Delete transient of this booking product to free up the slots.
		if ( $booking ) {
			WC_Bookings_Cache::delete_booking_slots_transient( $booking->get_product_id() );
		}
	}

	/**
	 * Process a batch of bookings to add missing attendance_status meta.
	 */
	public function add_missing_attendance_status_batch() {
		global $wpdb;

		// Batch size - process 50 bookings at a time.
		$batch_size = 50;

		// Query bookings that don't have the attendance_status meta.
		$booking_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_booking_attendance_status'
				WHERE p.post_type = 'wc_booking'
				AND pm.meta_id IS NULL
				ORDER BY p.ID ASC
				LIMIT %d",
				$batch_size
			)
		);

		if ( empty( $booking_ids ) ) {
			return;
		}

		foreach ( $booking_ids as $booking_id ) {
			$booking = get_wc_booking( $booking_id );

			if ( ! is_a( $booking, 'WC_Booking' ) ) {
				continue;
			}

			$status = $booking->get_status();

			// Determine attendance status based on booking status.
			$attendance_status = 'unattended';

			if ( 'complete' === $status ) {
				$attendance_status = 'attended';
			}

			// Set the attendance status.
			$booking->set_attendance_status( $attendance_status );
			$booking->save();
		}

		// Schedule the next batch if we processed a full batch.
		if ( count( $booking_ids ) === $batch_size ) {
			// Check if Action Scheduler is available.
			if ( function_exists( 'as_schedule_single_action' ) ) {
				as_schedule_single_action(
					time(),
					'woocommerce_bookings_add_missing_attendance_status',
					array(),
					'woocommerce-bookings'
				);
			}
		}
	}
}
