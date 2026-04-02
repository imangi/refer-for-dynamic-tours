<?php
/**
 * The markup for the 'Calendar List' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
				
<div class="pewc-fields-wrapper pewc-calendar-list-fields">

	<div class="product-extra-field">

		<div class="product-extra-field-inner">

			<label>
				<?php _e( 'Days', 'pewc' ); ?>
				<?php echo wc_help_tip( 'List prices for each day offset from the current day - e.g. Offset 0 is the current day, Offset 1 is the day after the current day, etc', 'pewc' ); ?>
			</label>

		</div>

		<div class="product-extra-field-inner">

			<div class="pewc-calendar-list-wrapper">

				<div class="pewc-calendar-list-headers pewc-field-calendar-list-wrapper">
					<div>
						<?php _e( 'Offset', 'pewc' ); ?>
					</div>
					<div>
						<?php _e( 'Price', 'pewc' ); ?>
					</div>
					<div class="pewc-actions pewc-select-actions">&nbsp;</div>
				</div>

					<?php $row_count = 0;
					if( ! empty( $item['field_cl_options'] ) ) {
						foreach( $item['field_cl_options'] as $key=>$value ) {
							include( PEWC_DIRNAME . '/templates/admin/views/calendar-list-row.php' );
							$row_count++;
						}

					} ?>
				
			</div>

			<p><a href="#" class="button add_new_cl_row"><?php _e( 'Add Item', 'pewc' ); ?></a></p>

		</div>

	</div>

</div><!-- .pewc-fields-wrapper -->

<div class="pewc-fields-wrapper pewc-calendar-list-fields">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label>
				<?php _e( 'Disable days of the week?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Select weekdays below to disable from the calendar list', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<div class="pewc-weekdays-wrapper">
				<?php $weekdays = array(
					__( 'Sunday', 'pewc' ),
					__( 'Monday', 'pewc' ),
					__( 'Tuesday', 'pewc' ),
					__( 'Wednesday', 'pewc' ),
					__( 'Thursday', 'pewc' ),
					__( 'Friday', 'pewc' ),
					__( 'Saturday', 'pewc' )
				);
				foreach( $weekdays as $index=>$day ) { ?>
					<div class="pewc-weekday">
						<?php $checked = ! empty( $item['cl_weekdays'][$index] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-cl_weekdays" id="_product_extra_groups_cl_weekdays_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $index ); ?>" name="<?php echo esc_attr( $base_name ); ?>[cl_weekdays][<?php echo esc_attr( $index ); ?>]" value="1" data-field-name="cl_weekdays" >
						<label for="_product_extra_groups_cl_weekdayss_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $index ); ?>">
							<?php echo $day; ?>
						</label>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label>
				<?php $blocked = isset( $item['cl_blocked_dates'] ) ? $item['cl_blocked_dates'] : ''; ?>
				<?php _e( 'Blocked dates', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enter a comma-separated list of blocked dates using the YYYY-MM-DD format', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<textarea class="pewc-field-item pewc-field-blocked-dates" name="<?php echo esc_attr( $base_name ); ?>[cl_blocked_dates]" data-field-name="cl_blocked_dates"><?php echo esc_html( $blocked ); ?></textarea>
		</div>
	</div>

</div>
<div class="pewc-fields-wrapper split-half">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label>
				<?php _e( 'Latest time', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enter an optional last time for the current day', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner pewc-latest-time">
			<?php $hours = range( 0, 23 );
			$minutes = range( 0, 59 );
			$latest_hour = isset( $item['field_latest_hour'] ) ? $item['field_latest_hour'] : '';
			$latest_minute = isset( $item['field_latest_minute'] ) ? $item['field_latest_minute'] : ''; ?>
            <select class="pewc-field-item pewc-field-latest-hour" name="<?php echo esc_attr( $base_name ); ?>[field_latest_hour]" id="field_latest_hour_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-field-name="field_latest_hour">
                <?php
				echo '<option value="">--</option>';
                foreach( $hours as $hour ) {
                    $selected = selected( $hour, $latest_hour, false );
                    echo '<option ' . $selected . ' value="' . esc_attr( $hour ) . '">' . esc_html( str_pad( $hour, 2, '0', STR_PAD_LEFT ) ) . '</option>';
                } ?>
            </select>

			<select class="pewc-field-item pewc-field-latest-minute" name="<?php echo esc_attr( $base_name ); ?>[field_latest_minute]" id="field_latest_minute_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-field-name="field_latest_minute">
                <?php
				echo '<option value="">--</option>';
                foreach( $minutes as $minute ) {
                    $selected = selected( $minute, $latest_minute, false );
                    echo '<option ' . $selected . ' value="' . esc_attr( $minute ) . '">' . esc_html( str_pad( $minute, 2, '0', STR_PAD_LEFT ) ) . '</option>';
                } ?>
            </select>
		</div>

	</div>
	
	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label>
				<?php _e( 'Latest time label', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enter text to precede the time', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $time_label = ! empty( $item['field_time_label'] ) ? $item['field_time_label'] : ''; ?>
			<input type="text" class="pewc-field-item pewc-field-time_label" name="<?php echo esc_attr( $base_name ); ?>[field_time_label]" value="<?php echo esc_attr( stripslashes( $time_label ) ); ?>" data-field-name="field_time_label">
		</div>

	</div>

</div>

<?php

do_action( 'pewc_end_calendar_list_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );