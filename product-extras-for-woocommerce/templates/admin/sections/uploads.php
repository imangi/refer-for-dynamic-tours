<?php
/**
 * The markup for the 'Uploads' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-fields-wrapper pewc-upload-fields split-half">

	<div class="product-extra-field">
		<div class="product-extra-field-inner">
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_multiple_uploads">
				<?php _e( 'Allow multiple uploads?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option to allow the user to upload multiple files', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $checked = ! empty( $item['multiple_uploads'] ); ?>
			<?php pewc_checkbox_toggle( 'multiple_uploads', $checked, $group_id, $item_key, 'pewc-multiple-uploads' ); ?>
		</div>
	</div>

	<div class="product-extra-field pewc-ajax-upload-only">
		<div class="product-extra-field-inner">
			<?php $max_files = ! empty( $item['max_files'] ) ? $item['max_files'] : ''; ?>
			<label>
				<?php _e( 'Max Files', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Maximum number of files if multiple files uploads are enabled', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<input type="number" class="pewc-field-item pewc-field-max-files" name="<?php echo esc_attr( $base_name ); ?>[max_files]" min="1" value="<?php echo esc_attr( $max_files ); ?>" data-field-name="max_files">
		</div>
	</div>

</div><!-- .pewc-upload-fields -->

<?php do_action( 'pewc_after_uploads_fields', $group_id, $item_key, $item, $post_id );
do_action( 'pewc_end_uploads_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );