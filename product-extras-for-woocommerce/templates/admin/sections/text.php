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

<?php if( apply_filters( 'pewc_show_character_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-char-fields split-half no-gap">
		
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Min Characters', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional minimum number of characters for this field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $min_chars = ! empty( $item['field_minchars'] ) ? $item['field_minchars'] : ''; ?>
				<input type="number" class="pewc-field-item pewc-field-minchars" name="<?php echo esc_attr( $base_name ); ?>[field_minchars]" value="<?php echo esc_attr( $min_chars ); ?>" data-field-name="field_minchars">
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $max_chars = ! empty( $item['field_maxchars'] ) ? $item['field_maxchars'] : ''; ?>
				<label>
					<?php _e( 'Max Characters', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional maximum number of characters for this field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" class="pewc-field-item pewc-field-maxchars" name="<?php echo esc_attr( $base_name ); ?>[field_maxchars]" value="<?php echo esc_attr( $max_chars ); ?>" data-field-name="field_maxchars">
			</div>
		</div>

	</div>
	<div class="pewc-fields-wrapper pewc-char-fields">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_show_char_counter">
					<?php _e( 'Show Counter?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this if you want to show the character counter under the field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $show_char_counter_checked = ! empty( $item['show_char_counter'] ); ?>
				<?php pewc_checkbox_toggle( 'show_char_counter', $show_char_counter_checked, $group_id, $item_key, 'pewc-field-show-char-counter' ); ?>
			</div>
		</div>
		
	</div><!-- .pewc-fields-wrapper -->

	<div class="pewc-fields-wrapper pewc-extrachar-fields no-gap">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $field_freechars = ! empty( $item['field_freechars'] ) ? $item['field_freechars'] : ''; ?>
				<label>
					<?php _e( 'Free Characters', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional number of free characters to allow before pricing per character kicks in', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="number" min="0" class="pewc-field-item pewc-field-freechars" name="<?php echo esc_attr( $base_name ); ?>[field_freechars]" value="<?php echo esc_attr( $field_freechars ); ?>" data-field-name="field_freechars">
			</div>
		</div>

	</div>
	<div class="pewc-fields-wrapper pewc-extrachar-fields split-half">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_alphanumeric">
					<?php _e( 'Only Allow Alphanumeric?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this to allow only alphanumeric characters in this field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = ! empty( $item['field_alphanumeric'] ); ?>
				<?php pewc_checkbox_toggle( 'field_alphanumeric', $checked, $group_id, $item_key, 'pewc-field-alphanumeric' ); ?>
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_alphanumeric_charge">
					<?php _e( 'Only Charge Alphanumeric?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this to charge just the alphanumeric characters in the field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = ! empty( $item['field_alphanumeric_charge'] ); ?>
				<?php pewc_checkbox_toggle( 'field_alphanumeric_charge', $checked, $group_id, $item_key, 'pewc-field-alphanumeric-charge' ); ?>
			</div>
		</div>

	</div><!-- .pewc-fields-wrapper -->

<?php }

do_action( 'pewc_field_item_extra_text_fields', $group_id, $item_key, $item, $post_id );
do_action( 'pewc_end_text_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );