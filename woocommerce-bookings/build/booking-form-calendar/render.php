<?php
/**
 * Render the Booking Form Calendar block.
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

/**
 * I18n month names.
 */
$month_names = array(
	_x( 'January', 'month name', 'woocommerce-bookings' ),
	_x( 'February', 'month name', 'woocommerce-bookings' ),
	_x( 'March', 'month name', 'woocommerce-bookings' ),
	_x( 'April', 'month name', 'woocommerce-bookings' ),
	_x( 'May', 'month name', 'woocommerce-bookings' ),
	_x( 'June', 'month name', 'woocommerce-bookings' ),
	_x( 'July', 'month name', 'woocommerce-bookings' ),
	_x( 'August', 'month name', 'woocommerce-bookings' ),
	_x( 'September', 'month name', 'woocommerce-bookings' ),
	_x( 'October', 'month name', 'woocommerce-bookings' ),
	_x( 'November', 'month name', 'woocommerce-bookings' ),
	_x( 'December', 'month name', 'woocommerce-bookings' ),
);

/**
 * Define all weekday names in order (Sunday to Saturday).
 */
$all_weekday_names = array(
	_x( 'Sun', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Mon', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Tue', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Wed', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Thu', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Fri', 'weekday abbreviation', 'woocommerce-bookings' ),
	_x( 'Sat', 'weekday abbreviation', 'woocommerce-bookings' ),
);

$week_starts_on = (int) get_option( 'start_of_week', 1 ); // Monday.

/**
 * Reorder weekday names based on week start setting.
 */
$weekday_abbrev_names = array();
for ( $i = 0; $i < 7; $i++ ) {
	$day_index              = ( $week_starts_on + $i ) % 7;
	$weekday_abbrev_names[] = $all_weekday_names[ $day_index ];
}

wp_interactivity_config(
	'woocommerce-bookings/booking-form-calendar',
	array(
		'monthNames' => $month_names,
	)
)
?>
<div
	class="wc-bookings-calendar"
	data-wp-interactive="woocommerce-bookings/booking-form-calendar"
	data-wp-on--touchstart="actions.onTouchStart"
	data-wp-on--touchmove="actions.onTouchMove"
	data-wp-on--touchend="actions.onTouchEnd"
	>

	<div class="wc-bookings-calendar__header">
		<button
			class="wc-bookings-calendar__nav wc-bookings-calendar__nav--prev"
			type="button"
			data-wp-on--click="actions.navigateToPreviousMonth"
			data-wp-bind--aria-label="state.prevMonthLabel"
			data-wp-bind--disabled="state.isPreviousMonthDisabled"
			>
				&lsaquo;
			</button>
		<div class="wc-bookings-calendar__title">
			<span data-wp-text="state.viewMonthName"></span>
			<span data-wp-text="state.viewYear"></span>
			<span
				class="wc-bookings-calendar__spinner"
				data-wp-class--visible="woocommerce-bookings/booking-form::state.isBusy"
				>
			</span>
		</div>
		<button
			class="wc-bookings-calendar__nav wc-bookings-calendar__nav--next"
			type="button"
			data-wp-on--click="actions.navigateToNextMonth"
			data-wp-bind--aria-label="state.nextMonthLabel"
			data-wp-bind--disabled="state.isNextMonthDisabled"
			>
				&rsaquo;
			</button>
	</div>

	<div class="wc-bookings-calendar__weekdays">
		<?php foreach ( $weekday_abbrev_names as $weekday_name ) : ?>
			<div class="wc-bookings-calendar__weekday"><?php echo esc_html( $weekday_name ); ?></div>
		<?php endforeach; ?>
	</div>

	<div
		class="wc-bookings-calendar__grid"
		data-wp-bind--key="state.calendarKey"
		data-wp-class--loading="woocommerce-bookings/booking-form::state.isBusy"
		>
		<template
			data-wp-each--day="state.visibleDates"
			data-wp-each-key="context.day.key"
			>
			<div class="wc-bookings-calendar__day-container">
				<button
					class="wc-bookings-calendar__day"
					type="button"
					data-wp-on--click="actions.handleSelectDate"
					data-wp-class--selected="state.dayIsSelected"
					data-wp-class--today="state.dayIsToday"
					data-wp-class--other-month="!context.day.isInViewMonth"
					data-wp-class--disabled="state.dayIsDisabled"
					data-wp-bind--aria-disabled="state.dayIsDisabled"
					data-wp-bind--tabindex="state.dayTabIndex"
					data-wp-bind--aria-label="state.dayAriaLabel"
					data-wp-bind--data-date="context.day.ymd"
					>
						<span data-wp-text="context.day.day"></span>
				</button>
			</div>
		</template>
	</div>
</div>

