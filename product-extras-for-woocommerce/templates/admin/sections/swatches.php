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

<?php if( apply_filters( 'pewc_show_image_swatch_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-hide-if-not-pro">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Swatch width', 'pewc' ); ?>
					<?php echo wc_help_tip( 'The max width in pixels for each swatch - leave empty to use image size', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $swatchwidth = isset( $item['field_swatchwidth'] ) ? $item['field_swatchwidth'] : ''; ?>
				<input type="number" class="pewc-field-item pewc-field-swatchwidth" name="<?php echo esc_attr( $base_name ); ?>[field_swatchwidth]" value="<?php echo esc_attr( $swatchwidth ); ?>" data-field-name="field_swatchwidth">
				
			</div>
		</div>

	</div>

	<div class="pewc-fields-wrapper pewc-hide-if-not-pro pewc-swatch-extras">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_replace_main_image">
					<?php _e( 'Replace main image', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option to replace the main product image with the selected swatch', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $replace_main_image = ! empty( $item['replace_main_image'] ); ?>
				<?php pewc_checkbox_toggle( 'replace_main_image', $replace_main_image, $group_id, $item_key ); ?>

			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_layered_images">
					<?php _e( 'Layer images?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option to layer each selected swatch on the main image', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $checked = ! empty( $item['layered_images'] ); ?>
				<?php pewc_checkbox_toggle( 'layered_images', $checked, $group_id, $item_key, 'pewc-layered-images' ); ?>

			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Parent Swatch ID', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter the field ID of your main swatch field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $parent_swatch_id = ! empty( $item['parent_swatch_id'] ) ? $item['parent_swatch_id'] : false; ?>
				<input type="number" class="pewc-field-item pewc-parent-swatch-id" name="<?php echo esc_attr( $base_name ); ?>[parent_swatch_id]" value="<?php echo $parent_swatch_id; ?>" data-field-name="parent_swatch_id">

			</div>
		</div>

		<div class="product-extra-field pewc-allow-multiple-wrapper">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Allow Multiple?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option to allow multiple selections (checkbox instead of radio)', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $checked = ! empty( $item['allow_multiple'] ); ?>
				<?php pewc_checkbox_toggle( 'allow_multiple', $checked, $group_id, $item_key, 'pewc-allow-multiple' ); ?>
				
			</div>
		</div>

	</div>
	
<?php }

do_action( 'pewc_end_swatches_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );