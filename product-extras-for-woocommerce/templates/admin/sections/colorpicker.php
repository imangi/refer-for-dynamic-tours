<?php
/**
 * The markup for the 'Colorpicker' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php if( apply_filters( 'pewc_show_color_picker_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-color-picker-fields">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $color = isset( $item['field_color'] ) ? $item['field_color'] : ''; ?>
				<label>
					<?php _e( 'Default color', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Optionally select a default color for this field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="text" class="pewc-field-item pewc-field-color" name="<?php echo esc_attr( $base_name ); ?>[field_color]" value="<?php echo esc_attr( $color ) ?>" data-field-name="field_color">
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $width = isset( $item['field_width'] ) ? $item['field_width'] : ''; ?>
				<label>
					<?php _e( 'Element width', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Optionally chose a different width for the color-picker dropdown (px)', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" class="pewc-field-item pewc-field-width" name="<?php echo esc_attr( $base_name ); ?>[field_width]" value="<?php echo esc_attr( $width ) ?>" data-field-name="field_width" >
			</div>
		</div>
		
	</div>

	<div class="pewc-fields-wrapper pewc-color-picker-fields">

		<div class="pewc-show product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_show">
					<?php _e( 'Show by default?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option if you want to show the color-picker dropdown by default.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = ! empty( $item['field_show'] ); ?>
				<?php pewc_checkbox_toggle( 'field_show', $checked, $group_id, $item_key, 'pewc-field-show' ); ?>
			</div>
		</div>

		<div class="pewc-palettes product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_palettes">
					<?php _e( 'Display common palettes?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option to display a row of common palette colors. This is particularly useful in situations where the currently selected color seems to make no colors available.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = ! empty( $item['field_palettes'] ); ?>
				<?php pewc_checkbox_toggle( 'field_palettes', $checked, $group_id, $item_key, 'pewc-field-palettes' ); ?>
			</div>
		</div>

	</div><!-- .pewc-fields-wrapper -->

<?php }

do_action( 'pewc_end_colorpicker_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );