<?php
/**
 * The markup for the 'Swatches' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php if( apply_filters( 'pewc_show_date_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-date-fields">

		<?php if( pewc_enable_offset_days( $item ) ) { ?>

			<div class="product-extra-field">
				<div class="product-extra-field-inner">
					<?php $offset_days = isset( $item['offset_days'] ) ? $item['offset_days'] : ''; ?>
					<label>
						<?php _e( 'Offset days', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enter a value to offset the minimum date by the set number', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<input type="number" class="pewc-field-item pewc-field-offset-days" name="<?php echo esc_attr( $base_name ); ?>[offset_days]" value="<?php echo esc_attr( $offset_days ); ?>" data-field-name="offset_days">
				</div>
			</div>

		<?php } else { ?>

			<div class="product-extra-field">
				<div class="product-extra-field-inner">
					<label for="<?php echo esc_attr( $base_name ); ?>_min_date_today">
						<?php _e( 'Min date today?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Select this to prevent entering a date in the past', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<?php $checked = ! empty( $item['min_date_today'] ); ?>
					<?php pewc_checkbox_toggle( 'min_date_today', $checked, $group_id, $item_key, 'pewc-field-min_date_today' ); ?>
				</div>
			</div>

			<div class="product-extra-field">
				<div class="product-extra-field-inner">
					<?php $mindate = isset( $item['field_mindate'] ) ? $item['field_mindate'] : ''; ?>
					<label>
						<?php _e( 'Min date', 'pewc' ); ?>
						<?php echo wc_help_tip( 'The earliest allowable date', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<input type="text" class="pewc-field-item pewc-date-field pewc-field-mindate" name="<?php echo esc_attr( $base_name ); ?>[field_mindate]" value="<?php echo esc_attr( $mindate ); ?>" data-field-name="field_mindate" >
				</div>
			</div>

		<?php } ?>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $maxdate = isset( $item['field_maxdate'] ) ? $item['field_maxdate'] : ''; ?>
				<?php $maxdate_ymd = isset( $item['field_maxdate_ymd'] ) ? $item['field_maxdate_ymd'] : ''; ?>
				<label>
					<?php _e( 'Max date', 'pewc' ); ?>
					<?php echo wc_help_tip( 'The latest allowable date', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="text" class="pewc-field-item pewc-date-field pewc-field-maxdate" name="<?php echo esc_attr( $base_name ); ?>[field_maxdate]" value="<?php echo esc_attr( $maxdate ); ?>" data-field-name="field_maxdate">
				<input type="hidden" class="pewc-field-item pewc-date-field pewc-field-maxdate-ymd" name="<?php echo esc_attr( $base_name ); ?>[field_maxdate_ymd]" value="<?php echo esc_attr( $maxdate_ymd ); ?>" data-field-name="field_maxdate_ymd">
			</div>
		</div>

	</div><!-- .pewc-fields-wrapper -->

	<?php } ?>

	<?php if( pewc_show_days_of_the_week( $item ) ) { ?>

		<div class="pewc-fields-wrapper pewc-date-fields">

			<div class="product-extra-field">
				<div class="product-extra-field-inner">
					<label>
						<?php _e( 'Disable days of the week?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Select weekdays below to disable them on the datepicker calendar', 'pewc' ); ?>
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
								<?php $checked = ! empty( $item['weekdays'][$index] ); ?>
								<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-weekdays" id="_product_extra_groups_weekdays_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $index ); ?>" name="<?php echo esc_attr( $base_name ); ?>[weekdays][<?php echo esc_attr( $index ); ?>]" value="1" data-field-name="weekdays" >
								<label for="_product_extra_groups_weekdays_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>_<?php echo esc_attr( $index ); ?>">
									<?php echo $day; ?>
								</label>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

		</div><!-- .pewc-fields-wrapper -->

	<?php } ?>

	<?php if( pewc_enable_blocked_dates( $item ) ) { ?>

		<div class="pewc-date-fields">

			<div class="product-extra-field">
				<div class="product-extra-field-inner">
					<label>
						<?php $blocked = isset( $item['blocked_dates'] ) ? $item['blocked_dates'] : ''; ?>
						<?php _e( 'Blocked dates', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enter a comma-separated list of blocked dates using the YYYY-MM-DD format', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<textarea class="pewc-field-item pewc-field-blocked-dates" name="<?php echo esc_attr( $base_name ); ?>[blocked_dates]" data-field-name="blocked_dates"><?php echo esc_html( $blocked ); ?></textarea>
				</div>

			</div>

		</div><!-- .pewc-fields-wrapper -->

<?php }

do_action( 'pewc_end_date_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );