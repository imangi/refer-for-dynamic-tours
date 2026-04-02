<?php
/**
 * The markup for the 'Options' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
	
<div class="pewc-fields-wrapper">
				
	<?php
	if( apply_filters( 'pewc_show_option_field_params', true, $item, $post_id ) ) {
		include( PEWC_DIRNAME . '/templates/admin/views/option-fields.php' );
	} ?>

</div>

<?php if( apply_filters( 'pewc_show_checkbox_group_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-checkbox-group-fields split-half">

		<div class="product-extra-field ">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Min Number', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional minimum number of options that the user can select for this field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $minchecks = isset( $item['field_minchecks'] ) ? $item['field_minchecks'] : ''; ?>
				<input type="number" class="pewc-field-item pewc-field-minchecks" name="<?php echo esc_attr( $base_name ); ?>[field_minchecks]" value="<?php echo esc_attr( $minchecks ); ?>" data-field-name="field_minchecks">
				
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Max Number', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional maximum number of options that the user can select for this field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $maxchecks = isset( $item['field_maxchecks'] ) ? $item['field_maxchecks'] : ''; ?>
				<input type="number" class="pewc-field-item pewc-field-maxchecks" name="<?php echo esc_attr( $base_name ); ?>[field_maxchecks]" value="<?php echo esc_attr( $maxchecks ); ?>" data-field-name="field_maxchecks">
				
			</div>
		</div>

	</div>

<?php }

do_action( 'pewc_end_options_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );