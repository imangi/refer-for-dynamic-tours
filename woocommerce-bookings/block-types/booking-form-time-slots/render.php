<?php
/**
 * Render the Booking Time Slots block.
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

try {
	// 1. Get the Safe Timezone String.
	$tz_string = wp_timezone_string();
	$timezone  = new DateTimeZone( $tz_string );
	$dt        = new DateTime( 'now', $timezone );

	// 2. Prepare the Offset Display (e.g., +05:30).
	$offset_string    = $dt->format( 'P' );
	$offset_formatted = '+00:00' === $offset_string ? 'GMT' : 'GMT' . str_replace( ':00', '', $offset_string );

	// 3. Try to get the "Pretty Name" via Intl.
	$full_name = '';

	if ( class_exists( 'IntlDateFormatter' ) ) {
		$formatter = new IntlDateFormatter(
			'en_US',
			IntlDateFormatter::NONE,
			IntlDateFormatter::NONE,
			$timezone,
			IntlDateFormatter::GREGORIAN,
			'zzzz' // Full generic non-location name (e.g., Pacific Daylight Time).
		);
		$full_name = $formatter->format( $dt );
	}

	// 4. Fallback if Intl is missing or failed (returns false/empty).
	if ( empty( $full_name ) ) {
		// Fallback A: Use the abbreviation (e.g., EST, CET).
		$full_name = $dt->format( 'T' );

		// Fallback B: If abbreviation is an offset (like +03), just use the City Name.
		if ( preg_match( '/^[+-]\d/', $full_name ) || 'UTC' === $full_name ) {
			$full_name = $tz_string;
		}
	}

	/* translators: 1: Timezone name, 2: Timezone offset. */
	$timezone_string_format = esc_html__( 'Time Zone: %1$s (%2$s)', 'woocommerce-bookings' );
	$timezone_string        = sprintf( $timezone_string_format, $full_name, $offset_formatted );

} catch ( Exception $e ) {
	// Ultimate fallback if DateTime throws an error (e.g., invalid timezone string).
	$timezone_string = esc_html__( 'Time Zone: UTC', 'woocommerce-bookings' );
}

$context = array(
	'currentPage'  => 1,
	'slotsPerPage' => 6,
);
?>
<div
	class="wc-bookings-time-slots"
	data-wp-interactive="woocommerce-bookings/booking-time-slots"
	data-wp-watch--selectFirstSlotWhenDateSelected="callbacks.selectFirstSlotWhenDateSelected"
	data-wp-bind--hidden="!state.isVisible"
	data-wp-class--loading="woocommerce-bookings/booking-form::state.isBusy"
	<?php echo wp_interactivity_data_wp_context( $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<div
		data-wp-bind--hidden="!state.shouldShowPlaceholder"
		class="wc-bookings-time-slots__placeholder"
	>
		<p class="wc-bookings-time-slots__placeholder-text"><?php echo esc_html__( 'No times are available for the selected date.', 'woocommerce-bookings' ); ?></p>
		<div class="wp-block-buttons alignfull">
			<div class="wp-block-button has-custom-width wp-block-button__width-100">
				<button
					type="button"
					class="wp-block-button__link wp-element-button"
					data-wp-on--click="woocommerce-bookings/booking-form::actions.findNextAvailable"
				>
					<?php echo esc_html__( 'Find next available', 'woocommerce-bookings' ); ?>
				</button>
			</div>
		</div>
	</div>
	<div
		class="wc-bookings-time-slots__container"
		data-wp-bind--hidden="state.shouldShowPlaceholder"
		data-wp-on--touchstart="actions.onTouchStart"
		data-wp-on--touchmove="actions.onTouchMove"
		data-wp-on--touchend="actions.onTouchEnd"
		>

		<div
			class="wc-bookings-time-slots__navigation-arrows previous"
			data-wp-bind--hidden="!state.shouldShowPagination"
			>
			<button
				type="button"
				data-wp-on--click="actions.prevPage"
				data-wp-bind--disabled="state.isPreviousPageDisabled"
				>
					&lsaquo;
			</button>
		</div>

		<div
			class="wc-bookings-time-slots__grid"
			>
			<template
				data-wp-each--slot="state.slotsForPage"
				data-wp-each-key="context.slot.time"
				>
				<button
					class="wc-bookings-time-slots__slot"
					type="button"
					data-wp-on--click="actions.handleSelectTime"
					data-time="context.slot.time"
					data-wp-class--selected="state.slotIsSelected"
					data-wp-bind--data-time="context.slot.time"
					data-wp-bind--aria-label="state.slotAriaLabel"
					>
					<span data-wp-text="state.slotTimeString"></span>
				</button>
			</template>
		</div>

		<div
			class="wc-bookings-time-slots__navigation-arrows next"
			data-wp-bind--hidden="!state.shouldShowPagination"
			>
			<button
				type="button"
				data-wp-on--click="actions.nextPage"
				data-wp-bind--disabled="state.isNextPageDisabled"
				>
				&rsaquo;
			</button>
		</div>
	</div>
	<div class="wc-bookings-time-slots__pagination" data-wp-bind--hidden="!state.shouldShowPagination">
		<div class="wc-bookings-time-slots__pagination-pages">
			<template data-wp-each--page="state.pages">
				<button
					type="button"
					data-wp-bind--data-pageNumber="context.page.pageNumber"
					data-wp-on--click="actions.handleGoToPage"
					data-wp-class--selected="context.page.isSelected"
					data-wp-bind--aria-label="context.page.ariaLabel"
				>
				</button>
			</template>
		</div>
	</div>
	<div
		class="wc-bookings-time-slots__timezone"
		data-wp-bind--hidden="state.shouldShowPlaceholder"
		>
		<?php echo esc_html( $timezone_string ); ?>
	</div>
</div>
