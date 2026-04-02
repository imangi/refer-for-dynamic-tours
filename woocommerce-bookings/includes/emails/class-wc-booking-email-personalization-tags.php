<?php
/**
 * WooCommerce Bookings Email Personalization Tags
 *
 * Registers personalization tags for the WooCommerce block email editor.
 *
 * @package WooCommerce Bookings
 * @since   3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\EmailEditor\Engine\PersonalizationTags\Personalization_Tag;

/**
 * WC_Booking_Email_Personalization_Tags class.
 *
 * Handles registration and rendering of booking-related personalization tags
 * for the WooCommerce block email editor.
 */
class WC_Booking_Email_Personalization_Tags {

	/**
	 * Initialize the personalization tags.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'woocommerce_email_editor_register_personalization_tags', array( $this, 'register_personalization_tags' ) );
	}

	/**
	 * Register booking personalization tags.
	 *
	 * @since 3.2.0
	 *
	 * @param object $personalization_tags_registry The personalization tags registry.
	 * @return object The personalization tags registry.
	 */
	public function register_personalization_tags( $personalization_tags_registry ) {
		// Check if the Personalization_Tag class exists (WooCommerce 9.0+).
		if ( ! class_exists( Personalization_Tag::class ) ) {
			return $personalization_tags_registry;
		}

		// Booking admin link.
		$personalization_tags_registry->register(
			new Personalization_Tag(
				__( 'Admin Edit Link', 'woocommerce-bookings' ),
				'woocommerce-bookings/admin-link',
				__( 'Booking', 'woocommerce-bookings' ),
				array( $this, 'get_booking_admin_link' ),
				array(),
				null,
				array( 'woo_email' )
			)
		);

		return $personalization_tags_registry;
	}

	/**
	 * Get booking from context.
	 *
	 * @since 3.2.0
	 *
	 * @param array $context The context data.
	 * @return WC_Booking|null The booking object or null.
	 */
	private function get_booking_from_context( $context ) {
		if ( ! empty( $context['booking'] ) && $context['booking'] instanceof WC_Booking ) {
			return $context['booking'];
		}

		if ( ! empty( $context['booking_id'] ) && function_exists( 'get_wc_booking' ) ) {
			$booking = get_wc_booking( $context['booking_id'] );
			if ( $booking ) {
				return $booking;
			}
		}

		return null;
	}

	/**
	 * Get booking admin link.
	 *
	 * @since 3.2.0
	 *
	 * @param array $context The context data.
	 * @return string
	 */
	public function get_booking_admin_link( $context ) {
		$booking = $this->get_booking_from_context( $context );

		if ( ! $booking ) {
			return '#';
		}

		return '<a href="' . esc_url( wc_bookings_get_edit_booking_url( $booking ) ) . '">' . __( 'Edit booking', 'woocommerce-bookings' ) . '</a>';
	}
}
