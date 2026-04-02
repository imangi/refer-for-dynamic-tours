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

<div class="pewc-fields-wrapper">

	<?php if( pewc_enable_user_fields() ) {
		$user_field_id = isset( $item['field_user_field_id'] ) ? $item['field_user_field_id'] : ''; ?>
		<div class="pewc-user-field-id product-extra-field">	
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label pewc-field-user-field-id-label">
					<?php _e( 'User Field ID', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter the user field ID here if you wish to update user meta with this field value', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="text" class="pewc-field-item pewc-field-field-user-field-id" name="<?php echo esc_attr( $base_name ); ?>[field_user_field_id]" value="<?php echo esc_attr( $user_field_id ); ?>" data-field-name="field_user_field_id">
			</div>
		</div>
	<?php } ?>

	<?php // Add your own stuff here
	do_action( 'pewc_end_product_extra_field', $group_id, $item_key, $item, $post_id ); ?>

	<?php
	do_action( 'pewc_field_item_extra_fields', $group_id, $item_key, $item, $post_id ); /* DWS */ ?>

</div>

<?php do_action( 'pewc_end_uploads_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );