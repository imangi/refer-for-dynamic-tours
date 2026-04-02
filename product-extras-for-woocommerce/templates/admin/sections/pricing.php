<?php
/**
 * The markup for the 'Pricing' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-fields-wrapper">
	
	<div class="product-extra-field pewc-field-price-wrapper">

		<div class="product-extra-field-inner">
			<?php $field_price = isset( $item['field_price'] ) ? $item['field_price'] : ''; ?>
			<label>
				<?php _e( 'Field Price', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enter the amount that will be added to the price if the user enters a value for this field', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php 
				// 3.26.0
				if ( pewc_formulas_in_prices_enabled( $post_id ) ) { ?>
				<input type="text" class="pewc-field-item pewc-field-price" name="<?php echo esc_attr( $base_name ); ?>[field_price]" value="<?php echo esc_attr( $field_price ); ?>" data-field-name="field_price">
			<?php } else { ?>
				<input type="number" class="pewc-field-item pewc-field-price" name="<?php echo esc_attr( $base_name ); ?>[field_price]" value="<?php echo esc_attr( $field_price ); ?>" step="<?php echo apply_filters( 'pewc_field_item_price_step', '0.01', $item ); ?>" data-field-name="field_price">
			<?php } ?>
		</div>
	</div>

	<div class="product-extra-field pewc-char-fields">
		<div class="product-extra-field-inner">
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_per_character">
				<?php _e( 'Price Per Character?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Select this if you want to charge per character', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $per_char_checked = ! empty( $item['per_character'] ); ?>
			<?php pewc_checkbox_toggle( 'per_character', $per_char_checked, $group_id, $item_key, 'pewc-field-per-character' ); ?>
		</div>
	</div>

	<div class="product-extra-field pewc-upload-fields">
		<div class="product-extra-field-inner">
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_multiply_price">
				<?php _e( 'Price per upload?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option to multiply the field price by the number of uploaded files', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php $checked = ! empty( $item['multiply_price'] ); ?>
			<?php pewc_checkbox_toggle( 'multiply_price', $checked, $group_id, $item_key, 'pewc-multiply-price' ); ?>
		</div>
	</div>

</div>

<?php include( PEWC_DIRNAME . '/templates/admin/views/role-based-prices.php' ); ?>

<?php include( PEWC_DIRNAME . '/templates/admin/views/price-visibility.php' ); ?>

<div class="pewc-fields-wrapper">

	<div class="product-extra-field pewc-flatrate">
		<div class="product-extra-field-inner">
			
			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_flatrate">
				<?php _e( 'Flat Rate?', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Enable this option if you only want to charge for this field once, irrespective of how many times it\'s added to the cart', 'pewc' ); ?>
			</label>

		</div>
		<div class="product-extra-field-inner">

			<?php $checked = ! empty( $item['field_flatrate'] ); ?>
			<?php pewc_checkbox_toggle( 'field_flatrate', $checked, $group_id, $item_key, 'pewc-field-flatrate' ); ?>
			
		</div>
	</div>

	<?php if( pewc_is_pro() ) { ?>

		<div class="product-extra-field pewc-percentage">
			<div class="product-extra-field-inner">
				
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_percentage">
					<?php _e( 'Percentage?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option for the field price to be set as a percentage of the product price', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $checked = ! empty( $item['field_percentage'] ); ?>
				<?php pewc_checkbox_toggle( 'field_percentage', $checked, $group_id, $item_key, 'pewc-field-percentage' ); ?>
				
			</div>
		</div>

	<?php } ?>

</div>

<?php if( pewc_is_pro() ) { ?>

	<div class="pewc-fields-wrapper pewc-child-product-discount-extras">
		
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Discount', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter a discount for products purchased in this field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $child_discount = ( isset( $item['child_discount'] ) ) ? floatval( $item['child_discount'] ) : ''; ?>
				<input type="number" class="pewc-field-item pewc-child-discount" name="<?php echo esc_attr( $base_name ); ?>[child_discount]" value="<?php echo esc_attr( $child_discount ); ?>" min="0" max="" step="0.01" data-field-name="child_discount">

			</div>
		</div>
	
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Discount Type', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Choose how the discount is calculated', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $discount_type = ( isset( $item['discount_type'] ) ) ? $item['discount_type'] : ''; ?>
				<select class="pewc-field-item pewc-child-discount-type" name="<?php echo esc_attr( $base_name ); ?>[discount_type]" data-field-name="discount_type">
					<option value="fixed" <?php selected( $discount_type, 'fixed', true ); ?>><?php _e( 'Fixed Amount', 'pewc' ); ?></option>
					<option value="percentage" <?php selected( $discount_type, 'percentage', true ); ?>><?php _e( 'Percentage', 'pewc' ); ?></option>
				</select>

			</div>
		</div>

	</div>

<?php }

do_action( 'pewc_end_checkbox_row', $item, $group_id, $item_key ); // Used by Bookings

do_action( 'pewc_end_pricing_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );