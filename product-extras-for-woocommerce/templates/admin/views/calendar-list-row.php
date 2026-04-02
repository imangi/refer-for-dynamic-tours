<?php
/**
 * The markup for an information row
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( ! isset( $group_id ) ) {
	$name = '';
} else {
	$offset_name = '_product_extra_groups_' . $group_id . '_' . $item_key . '[field_cl_options][' . $row_count . '][value]';
	$price_name = '_product_extra_groups_' . $group_id . '_' . $item_key . '[field_cl_options][' . $row_count . '][price]';
}  ?>


<div class="product-extra-row-wrapper pewc-field-calendar-list-wrapper" data-row-count="<?php echo esc_attr( $row_count ); ?>">

	<div>
		<?php $offset = ( isset( $key ) && isset( $item['field_cl_options'][esc_attr( $key )]['value'] ) ) ? $item['field_cl_options'][esc_attr( $key )]['value'] : ''; ?>
		<input type="number" class="pewc-field-row-offset" name="<?php echo $offset_name; ?>" value="<?php echo esc_attr( $offset ); ?>" min="0">
	</div>
	<div>
		<?php $price = ( isset( $key ) && isset( $item['field_cl_options'][esc_attr( $key )]['price'] ) ) ? $item['field_cl_options'][esc_attr( $key )]['price'] : ''; ?>
		<input type="text" class="pewc-field-row-price wc_input_price" name="<?php echo $price_name; ?>" value="<?php echo esc_attr( $price ); ?>">
	</div>
	<div class="pewc-actions pewc-select-actions">
		<span class="sort-option pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-row pewc-action"><span class="dashicons dashicons-trash"></span></span>
	</div>

</div>
