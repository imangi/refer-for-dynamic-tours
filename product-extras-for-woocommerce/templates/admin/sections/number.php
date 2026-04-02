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

<?php if( apply_filters( 'pewc_show_number_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-num-fields split-half no-gap">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $field_minval = ( ! empty( $item['field_minval'] ) || ( isset( $item['field_minval'] ) && $item['field_minval'] === '0' ) ) ? $item['field_minval'] : ''; ?>
				<label>
					<?php _e( 'Min Value', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional minimum value for the field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" step="<?php echo apply_filters( 'pewc_min_max_val_step', '1', $item ); ?>" class="pewc-field-item pewc-field-minval" name="<?php echo esc_attr( $base_name ); ?>[field_minval]" value="<?php echo esc_attr( $field_minval ); ?>" data-field-name="field_minval">
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $field_maxval = ! empty( $item['field_maxval'] ) ? $item['field_maxval'] : ''; ?>
				<label>
					<?php _e( 'Max Value', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional maximum value for the field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" step="<?php echo apply_filters( 'pewc_min_max_val_step', '1', $item ); ?>" class="pewc-field-item pewc-field-maxval" name="<?php echo esc_attr( $base_name ); ?>[field_maxval]" value="<?php echo esc_attr( $field_maxval ); ?>" data-field-name="field_maxval">
			</div>
		</div>

	</div>
	<div class="pewc-fields-wrapper pewc-num-fields split-half">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $field_step = ! empty( $item['field_step'] ) ? $item['field_step'] : ''; ?>
				<label>
					<?php _e( 'Decimal places', 'pewc' ); ?>
					<?php echo wc_help_tip( 'The number of decimal places for the number field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" step="1" class="pewc-field-item pewc-field-step" name="<?php echo esc_attr( $base_name ); ?>[field_step]" min="0" value="<?php echo esc_attr( $field_step ); ?>" data-field-name="field_step">
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_multiply">
					<?php _e( 'Multiply Price?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this to multiply the value of the field by its price', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = ! empty( $item['multiply'] ); ?>
				<?php pewc_checkbox_toggle( 'multiply', $checked, $group_id, $item_key, 'pewc-field-multiply' ); ?>
			</div>
		</div>

	</div><!-- .pewc-fields-wrapper -->

<?php }

do_action( 'pewc_end_number_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );