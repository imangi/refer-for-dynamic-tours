<?php
/**
 * The markup for the 'Display' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-fields-wrapper no-gap">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			
			<label>
				<?php _e( 'Field Class', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Optional class for the field wrapper element', 'pewc' ); ?>
			</label>

		</div>
		<div class="product-extra-field-inner">

			<?php $class = ! empty( $item['field_class'] ) ? $item['field_class'] : ''; ?>
			<input type="text" class="pewc-field-item pewc-field-class" name="<?php echo esc_attr( $base_name ); ?>[field_class]" value="<?php echo $class; ?>" data-field-name="field_class">

		</div>
	</div>
</div>

<div class="pewc-fields-wrapper pewc-radio-image-extras pewc-number-columns-wrapper no-gap">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			
			<label>
				<?php _e( 'Number Columns', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Choose how many columns to display your images in', 'pewc' ); ?>
			</label>

		</div>
		<div class="product-extra-field-inner">

			<?php $number_columns = ( ! empty( $item['number_columns'] ) ) ? intval( $item['number_columns'] ) : 3;
			$number_columns = max( 1, $number_columns ); ?>
			<input type="number" class="pewc-field-item pewc-number-columns" name="<?php echo esc_attr( $base_name ); ?>[number_columns]" value="<?php echo esc_attr( $number_columns ); ?>" min="1" max="10" step="1" data-field-name="number_columns">

		</div>
	</div>

</div>

<div class="pewc-fields-wrapper pewc-radio-image-extras">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_hide_labels">
				<?php _e( 'Hide Labels?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option to just display the images with no text', 'pewc' ); ?>
			</label>

		</div>
		<div class="product-extra-field-inner">

			<?php $checked = ! empty( $item['hide_labels'] ); ?>
			<?php pewc_checkbox_toggle( 'hide_labels', $checked, $group_id, $item_key, 'pewc-hide-labels' ); ?>

		</div>
	</div>

</div>

<div class="pewc-fields-wrapper pewc-misc-fields pewc-text-swatch">
	
	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_display_as_swatch">
				<?php _e( 'Display as Swatch?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option if you want to display the options as swatches', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $checked = ! empty( $item['field_display_as_swatch'] ); ?>
			<?php pewc_checkbox_toggle( 'field_display_as_swatch', $checked, $group_id, $item_key, 'pewc-field-display_as_swatch' ); ?>
		</div>
	</div>

</div>
<div class="pewc-fields-wrapper pewc-misc-fields pewc-number-range">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_enable_range_slider">
				<?php _e( 'Display as Slider?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option if you want to display the number field as a range slider', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $checked = ! empty( $item['field_enable_range_slider'] ); ?>
			<?php pewc_checkbox_toggle( 'field_enable_range_slider', $checked, $group_id, $item_key, 'pewc-field-enable_range_slider' ); ?>
		</div>
	</div>
	
</div><!-- .pewc-fields-wrapper -->

<?php do_action( 'pewc_end_display_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );