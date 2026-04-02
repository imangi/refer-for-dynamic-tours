<?php
/**
 * Render the Booking Form block.
 *
 * @package WooCommerce\Bookings
 * @since 3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = wc_get_product();
if ( ! is_wc_booking_product( $product ) ) {
	return;
}

$form_id = 'booking-form-' . $product->get_id();

// Canonical month key (YYYY-MM format to match API response keys).
$today      = new DateTimeImmutable( 'now', wp_timezone() );
$view_month = $today->format( 'Y-m' );

// Booking window data (value + unit arrays for dynamic calculation).
$booking_window_data = array(
	'start' => $product->get_min_date(), // Value + Unit array. Because this can be html cached and the booking window needs to dynamically update.
	'end'   => $product->get_max_date(), // Value + Unit array. Because this can be html cached and the booking window needs to dynamically update.
);

$context = array(
	// identity.
	'formId'                => $form_id,

	// selections.
	'selectedTeamId'        => null,
	'selectedDate'          => null,
	'selectedSlotKey'       => null,
	'requiresTimeSelection' => $product->requires_time_selection(),

	// calendar view state.
	'viewMonth'             => $view_month,

	// slots.
	'slotsCurrentPage'      => 1,

	// constraints.
	'bookingWindowData'     => $booking_window_data,

	// caches (keyed by cacheKey, not monthKey).
	'availabilityCache'     => new stdClass(), // {} in JSON - stores { cacheKey: { monthKey, data: {...} } }
	'cacheMeta'             => new stdClass(), // {} in JSON - stores { cacheKey: { fetchedAt, ttlMs } }

	// request lifecycle (keyed by cacheKey).
	'inFlight'              => new stdClass(), // {} in JSON - tracks in-flight requests by cacheKey
	'requestVersion'        => new stdClass(), // {} in JSON - tracks request versions by cacheKey for race safety
	'retryCount'            => new stdClass(), // {} in JSON - tracks retry attempts by cacheKey

	// misc.
	'lastError'             => null,
	'isBusy'                => false,
);

wp_interactivity_config(
	'woocommerce-bookings/booking-form',
	array(
		'isPermalinksPlain' => '' === get_option( 'permalink_structure' ),
		'weekStartsOn'      => (int) get_option( 'start_of_week', 1 ), // Monday.
	)
);

?>
<div
	data-wp-interactive="woocommerce-bookings/booking-form"
	data-wp-init--preselectToday="callbacks.preSelectToday"
	data-wp-watch--fetchWhenModalOpen="callbacks.fetchWhenModalOpen"
	<?php echo wp_interactivity_data_wp_context( $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<!-- Your calendar/time/team blocks render inside here and can use context.* bindings -->
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>

