<?php
/**
 * A products field template for the select layout
 * @since	2.2.0
 * @version	4.0.3
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! pewc_is_pro() ) {
	return;
}

$child_product_wrapper_class = array( 'child-product-wrapper' );
if( ! empty( $item['products_quantities'] ) ) {
	$products_quantities = ! empty( $item['products_quantities'] ) ? $item['products_quantities'] : '';
	$child_product_wrapper_class[] = 'products-quantities-' . $item['products_quantities'];
} ?>

<div class="<?php echo join( ' ', $child_product_wrapper_class ); ?>" data-products-quantities="<?php echo esc_attr( $item['products_quantities'] ); ?>"><?php

	if( $item['child_products'] ) {
		// 4.0.3, moved the <select> tags inside this condition so that it is not displayed if there are no child products
	?>

	<select class="pewc-form-field pewc-child-select-field" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>_child_product">

	<?php
		if( ! empty( $item['select_placeholder'] ) ) {
			// Add the placeholder instruction text
			echo '<option value="">' . esc_html( $item['select_placeholder'] ) . '</option>';
		}

		// 3.27.3
		$after_option = '';

		foreach( $item['child_products'] as $child_product_id ) {

			$child_product = wc_get_product( $child_product_id );
			if( ! is_object( $child_product ) || get_post_status( $child_product_id ) != 'publish' ) {
				continue;
			}

			$child_price = pewc_maybe_include_tax( $child_product, $child_product->get_price() );

			if( ! empty( $item['child_discount'] ) && ! empty( $item['discount_type'] ) ) {
				$discounted_price = pewc_get_discounted_child_price( $child_price, $item['child_discount'], $item['discount_type'] );
				$option_cost = $discounted_price;
			} else {
				$option_cost = $child_price;
			}

			$option_cost = apply_filters( 'pewc_child_product_option_cost', $option_cost, $item, $child_product, $post_id );

			$disabled = '';
			if( ! $child_product->is_purchasable() || ( ! $child_product->is_in_stock() && ! $child_product->backorders_allowed() ) ) {
				$disabled = 'disabled';
			}

			// Check available stock if stock is managed
			$available_stock = '';
			if( $child_product->managing_stock() ) {
				$available_stock = $child_product->get_stock_quantity();
			}
			// 3.27.3
			$available_stock = apply_filters( 'pewc_child_product_available_stock', $available_stock, $child_product, $item, $post_id );

			$selected = ( $value == $child_product_id || ( is_array( $value ) && in_array( $child_product_id, $value ) ) ) ? 'selected' : '';

			// 3.26.0
			if ( $selected && empty( $quantity_field_values ) ) {
				$quantity_field_values = array( 1 );
			}

			$name = apply_filters( 'pewc_child_product_title', get_the_title( $child_product_id ), $child_product, $option_cost, $item );

			// Include prices in option labels
			/**
			 * @since 3.2.5 Handled by pewc_add_child_product_price
			 */
			// if( pewc_display_option_prices_product_page( $item ) ) {
			// 	$name .= apply_filters( 'pewc_option_price_separator', '+', $item ) . pewc_get_semi_formatted_raw_price( $option_cost );
			// }

			// 3.27.3
			$option_class = apply_filters( 'pewc_child_product_option_class', array(), $child_product_id, $item );
			$option_class_str = ! empty( $option_class ) ? implode( ' ', $option_class ) : '';

			printf(
				'<option data-option-cost="%s" %s %s data-field-value="%s" value="%s" data-stock="%s" class="%s">%s</option>',
				apply_filters( 'pewc_option_price', esc_attr( $option_cost ), $item ),
				$disabled,
				$selected,
				esc_attr( get_the_title( $child_product_id ) ),
				esc_attr( $child_product_id ),
				esc_attr( $available_stock ),
				$option_class_str,
				$name
			);

			// 3.27.3
			$after_option .= apply_filters( 'pewc_after_child_product_option', '', $id, $child_product, $child_product_id );

		}
	?>

	</select>

	<?php

	} 

	// 3.27.3
	if ( ! empty( $after_option ) ) {
		echo $after_option;
	}

	// 4.0.3, added ! empty( $child_product_id ) to the condition
	if( $products_quantities == 'independent' && ! empty( $child_product_id ) ) {

		pewc_child_product_independent_quantity_field( $quantity_field_values, $child_product_id, $id, $item );

	} ?>

</div><!-- .child-product-wrapper -->
