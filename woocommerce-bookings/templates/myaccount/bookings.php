<?php
/**
 * My Bookings
 *
 * Shows customer bookings on the My Account > Bookings page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/myaccount/bookings.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/introduction-to-woocommerce-bookings/pages-and-emails-customization/
 * @author  Automattic
 * @version 3.3.0
 * @since   1.9.11
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter the columns to display in the My Account > Bookings table.
 *
 * @since 3.3.0
 *
 * @param array $columns_to_display The columns to display.
 * @return array The columns to display.
 */
$columns_to_display = apply_filters( '__experimental_woocommerce_bookings_my_account_columns', array( 'booking-id', 'booked-product', 'order-number', 'booking-start-date', 'booking-end-date', 'booking-status', 'booking-cancel' ) );

if ( ! empty( $tables ) ) : ?>

	<div class="bookings-my-account-notice"></div>

	<?php if ( count( $tables ) > 0 ) : ?>
		<h2><?php esc_html_e( 'Bookings', 'woocommerce-bookings' ); ?></h2>
	<?php endif; ?>

	<?php
	foreach ( $tables as $table ) :
		$i = 0;
		?>

		<h3><?php echo esc_html( $table['header'] ); ?></h3>

		<table class="shop_table shop_table_responsive my_account_bookings">
			<thead>
				<tr>
					<?php if ( in_array( 'booking-id', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-id"><?php esc_html_e( 'ID', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booked-product', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booked-product"><?php esc_html_e( 'Booked', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'order-number', $columns_to_display, true ) ) : ?>
						<th scope="col" class="order-number"><?php esc_html_e( 'Order', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-start-date', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-start-date"><?php esc_html_e( 'Start Date', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-end-date', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-end-date"><?php esc_html_e( 'End Date', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-date', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-date"><?php esc_html_e( 'Date', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-status', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-status"><?php esc_html_e( 'Status', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-total', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-total"><?php esc_html_e( 'Total', 'woocommerce-bookings' ); ?></th>
					<?php endif; ?>
					<?php if ( in_array( 'booking-cancel', $columns_to_display, true ) ) : ?>
						<th scope="col" class="booking-cancel"></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $table['bookings'] as $booking ) :
				$i ++;
				if ( ! $booking ) {
					continue;
				}

				// Skip bookings which were added to detect pagination.
				if ( $i > $bookings_per_page ) {
					break;
				}

				$booking_product    = $booking->get_product();
				$formatted_duration = '';

				if ( $booking_product && is_wc_booking_product( $booking_product ) ) {
					$duration      = $booking->get_end() - $booking->get_start();
					$duration_unit = $booking_product->get_duration_unit();

					if ( 'minute' === $duration_unit ) {
						$duration     = $duration / 60;
						$unit_display = __( 'min', 'woocommerce-bookings' );
					} elseif ( 'hour' === $duration_unit ) {
						$duration     = $duration / 3600;
						$unit_display = _n( 'h', 'hours', $duration, 'woocommerce-bookings' );
					} elseif ( 'day' === $duration_unit ) {
						$duration     = $duration / 86400;
						$unit_display = _n( 'day', 'days', $duration, 'woocommerce-bookings' );
					} elseif ( 'month' === $duration_unit ) {
						// Month durations are normalized to 30 days for display in this template.
						$duration     = (int) round( $duration / 2592000 );
						$unit_display = _n( 'month', 'months', $duration, 'woocommerce-bookings' );
					} else {
						$unit_display = $duration_unit;
					}

					/**
					 * Filter the formatted duration of the booking.
					 *
					 * @since 3.3.0
					 *
					 * @param string    $formatted_duration The formatted duration.
					 * @param int|float $duration The duration.
					 * @param string    $duration_unit The duration unit.
					 */
					$formatted_duration = apply_filters( '__experimental_woocommerce_bookings_get_duration', $duration . ' ' . $unit_display, $duration, $duration_unit );
				}

				$cancellable = 'cancelled' !== $booking->get_status() && 'completed' !== $booking->get_status() && ! $booking->passed_cancel_day();
				?>
				<tr>
					<?php if ( in_array( 'booking-id', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'ID', 'woocommerce-bookings' ); ?>" class="booking-id"><?php echo esc_html( $booking->get_id() ); ?></td>
					<?php endif; ?>
					<?php if ( in_array( 'booked-product', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Booked', 'woocommerce-bookings' ); ?>" class="booked-product">
						<?php if ( $booking_product && is_wc_booking_product( $booking_product ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $booking_product->get_id() ) ); ?>">
								<?php echo esc_html( $booking_product->get_title() ); ?>
							</a>
						<?php endif; ?>
					</td>
					<?php endif; ?>
					<?php if ( in_array( 'order-number', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Order', 'woocommerce-bookings' ); ?>" class="order-number">
						<?php if ( $booking->get_order() ) : ?>
							<a href="<?php echo esc_url( $booking->get_order()->get_view_order_url() ); ?>"><?php echo esc_html( $booking->get_order()->get_order_number() ); ?></a>
						<?php endif; ?>
					</td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-start-date', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Start Date', 'woocommerce-bookings' ); ?>" class="booking-start-date" data-all-day="<?php echo esc_attr( $booking->is_all_day() ? 'yes' : 'no' ); ?>" data-timezone="<?php echo esc_attr( $booking->get_booking_timezone() ); ?>"><?php echo esc_html( $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-end-date', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'End Date', 'woocommerce-bookings' ); ?>" class="booking-end-date" data-all-day="<?php echo esc_attr( $booking->is_all_day() ? 'yes' : 'no' ); ?>" data-timezone="<?php echo esc_attr( $booking->get_booking_timezone() ); ?>"><?php echo esc_html( $booking->get_end_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-date', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Date', 'woocommerce-bookings' ); ?>" class="booking-date" data-all-day="<?php echo esc_attr( $booking->is_all_day() ? 'yes' : 'no' ); ?>" data-timezone="<?php echo esc_attr( $booking->get_booking_timezone() ); ?>"><?php echo esc_html( $booking->get_start_date( null, '', wc_should_convert_timezone( $booking ) ) ) . ', <span class="booking-date-time">' . esc_html( $booking->get_start_date( '', null, wc_should_convert_timezone( $booking ) ) ) . '</span>' . ( $formatted_duration ? ' <span class="booking-date-duration">(' . esc_html( $formatted_duration ) . ')</span>' : '' ); ?></td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-status', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Status', 'woocommerce-bookings' ); ?>" class="booking-status"><span class="wc-bookings-status-label"><?php echo esc_html( wc_bookings_get_status_label( $booking->get_status() ) ); ?></span></td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-total', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Total', 'woocommerce-bookings' ); ?>" class="booking-total"><?php echo wp_kses_post( wc_price( $booking->get_cost() ) ); ?></td>
					<?php endif; ?>
					<?php if ( in_array( 'booking-cancel', $columns_to_display, true ) ) : ?>
					<td data-title="<?php esc_html_e( 'Cancel', 'woocommerce-bookings' ); ?>" class="booking-cancel <?php echo $cancellable ? 'cancellable' : 'not-cancellable'; ?>">
						<?php if ( $cancellable ) : ?>
							<a href="<?php echo esc_url( $booking->get_cancel_url() ); ?>" class="button cancel"><?php esc_html_e( 'Cancel', 'woocommerce-bookings' ); ?></a>
						<?php endif; ?>
					</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_before_account_bookings_pagination' ); ?>

		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'bookings', $page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce-bookings' ); ?></a>
			<?php endif; ?>

			<?php if ( count( $table['bookings'] ) > $bookings_per_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'bookings', $page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce-bookings' ); ?></a>
			<?php endif; ?>
		</div>

		<?php do_action( 'woocommerce_after_account_bookings_pagination' ); ?>

	<?php endforeach; ?>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go Shop', 'woocommerce-bookings' ); ?>
		</a>
		<?php esc_html_e( 'No bookings available yet.', 'woocommerce-bookings' ); ?>
	</div>
<?php endif; ?>
