<?php
/**
 * Template for calendar list field
 * @since 4.1.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo $open_td;

if( isset( $item['field_rows'] ) ) {
	
  	$offset_index = 0; // Relative to the current day

	$number_columns = ( isset( $item['number_columns'] ) ) ? $item['number_columns'] : 3;
	$radio_wrapper_classes = array(
		'pewc-radio-images-wrapper',
		'pewc-calendar-list-wrapper'
	);
	$radio_wrapper_classes[] = 'pewc-columns-' . intval( $number_columns );
	$weekdays = is_array( $item['cl_weekdays'] ) ? array_keys( $item['cl_weekdays'] ) : array();
	$blocked_dates = ! empty( $item['cl_blocked_dates'] ) ? explode( ',', $item['cl_blocked_dates'] ) : array(); ?>

	<div class="<?php echo join( ' ', $radio_wrapper_classes ); ?>">

  	<?php if( ! empty( $item['field_cl_options'] ) ) {
			// Use this to track the next day to check
		$count_the_days = 0;
	
		foreach( $item['field_cl_options'] as $option_index=>$list_item ) {

			$date = new DateTime();

			if( ! isset( $list_item['value'] ) || $list_item['value'] === false ) {
				// If the offset isn't specified, skip
				continue;
			} else {
				// This is the number of days ahead of the current day, e.g. 0 is the current day, 1 is the day after the current day
				$offset = absint( $list_item['value'] );
			}

			if( $option_index === 0 ) {
				// Set the count to the first offset value
				$count_the_days = $offset;
			} else {
				// $count_the_days += $offset;
			}

			$option_date = pewc_get_calendar_list_date( $date, $count_the_days );
			
			$date_available = false;
			// Start from current day, check if day is available
			// If not, count up 1 more, check again
			// Example: current day is Thursday 26th. Offset is 2
			// Sat and Sun are blocked so next available day needs to be Monday 30th
			while( $date_available == false ) {				
				// Check if this is a blocked day
				if( pewc_is_calendar_list_date_allowed( $option_date, $weekdays, $blocked_dates ) ) {
					// Date is available
					$count_the_days++;
					$date_available = true;
				} else {
					// Not available so count up another day
					$count_the_days++;
					$option_date->modify( '+1 day' );
				}
			}
			
			$nice_day = $option_date->format( 'D' );
			$nice_date = $option_date->format( 'j' );

			$wrapper_classes = array(
				'pewc-cl-wrapper',
				'pewc-radio-image-wrapper',
				'pewc-radio-checkbox-image-wrapper',
				'pewc-radio-image-wrapper-' . $option_index
			);

			$time = '';
			$hour = '';
			$minute = '';
			if( $option_index == 0 ) {
				$hour = ! empty( $item['field_latest_hour'] ) ? $item['field_latest_hour'] : '';
				$minute = ! empty( $item['field_latest_minute'] ) ?  $item['field_latest_minute'] : '';
				$time_label = ! empty( $item['field_time_label'] ) ?  $item['field_time_label'] : '';
				if( $hour && $minute ) {
					$hour = str_pad( $hour, 2, '0', STR_PAD_LEFT );
					$minute = str_pad( $minute, 2, '0', STR_PAD_LEFT );
					$time = sprintf(
						'%s&nbsp;%s:%s',
						$time_label,
						$hour,
						$minute
					);
				}
				$wrapper_classes[] = 'pewc-cl-has-time';
			}

			$price = ! empty( $list_item['price'] ) ? $list_item['price'] : 0;
			$option_price = pewc_get_option_price( $list_item, $item, $product );

			$classes = array( 'pewc-radio-form-field' );
			$classes = apply_filters( 'pewc_calendar_list_option_classes', $classes, $item, $offset, $price, $option_index );

			$radio_id = $id . '_' . strtolower( str_replace( ' ', '_', $list_item['value'] ) );

			printf(
				'<div class="%s" data-hour="%s" data-minute="%s" data-today="%s">',
				join( ' ', apply_filters( 'pewc_calendar_list_wrapper_classes', $wrapper_classes, $offset, $price, $option_index ) ),
				$hour,
				$minute,
				$date->format('Y-m-d')
			);

			printf(
				'<label for="%s" class="%s">',
				esc_attr( $nice_date ),
				join( ' ', apply_filters( 'pewc_calendar_list_label_classes', array(), $item, $offset, $option_index ) ),
			);

			printf(
				'<input data-option-cost="%s" type="radio" name="%s[]" id="%s" class="%s" data-date="%s" value="%s">',
				esc_attr( $option_price ),
				esc_attr( $id ),
				esc_attr( $radio_id ),
				join( ' ', $classes ),
				$option_date->format( 'Y-m-d' ),
				esc_attr( $option_index )
			); ?>

			<span class="pewc-cl-date-box">
				<span class="pewc-cl-item pewc-cl-nice-day"><?php echo esc_html( $nice_day ); ?></span>
				<span class="pewc-cl-item pewc-cl-nice-date"><?php echo esc_html( $nice_date ); ?></span>
			</span>
			<span class="pewc-cl-info-box">
				<span class="pewc-cl-price-box">
					<span class="pewc-cl-item pewc-cl-option-price"><?php echo wc_price( $option_price ); ?></span>
				</span>
				<span class="pewc-cl-time-box">
					<span class="pewc-cl-item pewc-cl-time"><?php echo esc_html( $time ); ?></span>
				</span>
			</span>

			<?php echo '</label>'; // End label
			echo '</div>'; // End wrapper classes

	  }

	} ?>

</div><!-- .pewc-radio-images-wrapper -->

<?php
	printf(
		'<input type="hidden" id="%s" name="%s" value="%s">',
		'pewc_cl_' . $field_id,
		'pewc_cl_' . $field_id,
		''
	);
	printf(
		'<input type="hidden" id="%s" name="%s" value="%s">',
		'pewc_cl_price_' . $field_id,
		'pewc_cl_price_' . $field_id,
		''
	);
}

echo $close_td;