<?php
/**
 * The markup for the 'Calculations' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-fields-wrapper pewc-hide-if-not-pro">

	<div class="product-extra-field pewc-products-extras">
		<div class="product-extra-field-inner">
			<?php $field_price = isset( $item['field_price'] ) ? $item['field_price'] : ''; ?>
			<label>
				<?php _e( 'Child Products', 'pewc' ); ?>
				<?php echo wc_help_tip( 'Select which products you\'d like to associate with this field', 'pewc' ); ?>
			</label>
		</div>
		<div class="product-extra-field-inner">
			<?php // $simple_products = pewc_get_simple_products();
			$child_products = ! empty( $item['child_products'] ) ? $item['child_products'] : array();
			$child_product_method = pewc_child_products_method( $post_id, $item_key );
			if( $child_product_method != 'variable_subscriptions' ) {
				// Use the standard WooCommerce AJAX methods to search for child products and/or child variations ?>
				<select class="pewc-field-item wc-product-search pewc-field-child_products pewc-data-options" data-options="" multiple="multiple" style="width: 100%;" name="<?php echo esc_attr( $base_name ); ?>[child_products][]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose child products', 'pewc' ); ?>" data-action="<?php echo $child_product_method; ?>" data-include="" data-exclude="<?php echo intval( $post_id ); ?>" data-field-name="child_products">
					<?php
					foreach( $child_products as $product_id ) {
						$product = wc_get_product( $product_id );
						// if( is_object( $product ) && $product->is_type( 'simple' ) ) {
						if( is_object( $product ) ) {
							echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
						}
					} ?>
				</select>
			<?php } else {
				// Populate field with subscription variations ?>
				<select class="pewc-field-item pewc-variation-field pewc-field-child_products pewc-data-options" data-options="" multiple="multiple" style="width: 100%;" name="<?php echo esc_attr( $base_name ); ?>[child_products][]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose the child subscription variations', 'pewc' ); ?>" data-field-name="child_products">
					<?php
					$subscription_variations = pewc_get_subscription_variations();
					$child_products = ! empty( $item['child_products'] ) ? $item['child_products'] : array();
					foreach( $subscription_variations as $variation_id=>$variation_name ) {
						// $product = wc_get_product( $product_id );
						$selected = ( is_array( $child_products ) && in_array( $variation_id, $child_products ) ) ? 'selected' : '';
						echo '<option value="' . esc_attr( $variation_id ) . '"' . $selected . '>' . wp_kses_post( $variation_name ) . '</option>';
					} ?>
				</select>
			<?php } ?>
		</div>
	</div>

	<div class="pewc-product-categories-extras">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $field_price = isset( $item['field_price'] ) ? $item['field_price'] : ''; ?>
				<label>
					<?php _e( 'Product Categories', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select which product categories you\'d like to autopopulate this field', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $product_categories = pewc_get_product_categories(); ?>
				<select class="pewc-field-item wc-category-search pewc-field-child_categories pewc-data-options" data-options="" multiple="multiple" style="width: 100%;" name="<?php echo esc_attr( $base_name ); ?>[child_categories][]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Select product categories', 'pewc' ); ?>" data-action="json_search_categories" data-include="" data-exclude="" data-field-name="child_categories">
					<?php
					if( ! empty( $item['child_categories'] ) ) {
						$child_categories = $item['child_categories'];
						foreach( $child_categories as $category_name ) {
							$term = get_term_by('slug', $category_name, 'product_cat');
							$cat_id = is_object($term) && $term->term_id ? $term->term_id : false;
							if( $cat_id && $category_name && $category_name !== '' ) {
								echo '<option value="' . esc_attr( $category_name ) . '"' . selected( true, true, false ) . '>' . esc_html( $term->name ) . '</option>';
							}
						}
					} ?>
				</select>
			</div>
		</div>
	
	</div><!-- pewc-product-categories-extras -->
	
</div><!-- pewc-products-extras -->


<?php if( apply_filters( 'pewc_show_products_params', true, $item, $post_id ) ) { ?>

	<div class="pewc-fields-wrapper pewc-products-extras pewc-product-categories-extras">

		<div class="product-extra-field pewc-products-layout">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Products Layout', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Choose how child products will be displayed.', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $products_layout = isset( $item['products_layout'] ) ? $item['products_layout'] : ''; ?>
				<select class="pewc-field-item pewc-field-products_layout" name="<?php echo esc_attr( $base_name ); ?>[products_layout]" data-field-name="products_layout">
					<option value="checkboxes" <?php selected( $products_layout, 'checkboxes', true ); ?>><?php _e( 'Checkboxes Images', 'pewc' ); ?></option>
					<option value="checkboxes-list" <?php selected( $products_layout, 'checkboxes-list', true ); ?>><?php _e( 'Checkboxes List', 'pewc' ); ?></option>
					<option value="column" <?php selected( $products_layout, 'column', true ); ?>><?php _e( 'Column', 'pewc' ); ?></option>
					<option value="radio" <?php selected( $products_layout, 'radio', true ); ?>><?php _e( 'Radio Images', 'pewc' ); ?></option>
					<option value="radio-list" <?php selected( $products_layout, 'radio-list', true ); ?>><?php _e( 'Radio List', 'pewc' ); ?></option>
					<option value="select" <?php selected( $products_layout, 'select', true ); ?>><?php _e( 'Select', 'pewc' ); ?></option>
					<option value="swatches" <?php selected( $products_layout, 'swatches', true ); ?>><?php _e( 'Swatches', 'pewc' ); ?></option>
					<option value="grid" <?php selected( $products_layout, 'grid', true ); ?>><?php _e( 'Variations Grid', 'pewc' ); ?></option>
					<option value="components" <?php selected( $products_layout, 'components', true ); ?>><?php _e( 'Components List', 'pewc' ); ?></option>
				</select>

				<small>
					<?php printf(
						'<a href="%s" target="_blank">%s</a>',
						'https://pluginrepublic.com/woocommerce-child-products/',
						__( 'This article explains the different layouts', 'pewc' )
					); ?>
				</small>

			</div>
		</div>

		<div class="product-extra-field pewc-products-select-placeholder">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Select Field Placeholder', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter instructional text in here to appear as the first option in the select field.', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $placeholder = ( ! empty( $item['select_placeholder'] ) ) ? $item['select_placeholder'] : ''; ?>
				<input type="text" class="pewc-field-item pewc-field-select_placeholder" name="<?php echo esc_attr( $base_name ); ?>[select_placeholder]" value="<?php echo esc_attr( $placeholder ); ?>" data-field-name="select_placeholder">

			</div>
		</div>

		<!-- Removed allow_none parameter -->

		<div class="product-extra-field pewc-products-quantities">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Products Quantities', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Choose whether to link the quantities of the parent product and child product so that they are always the same, or to limit the quantity of the child product to one only.', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $products_quantities = isset( $item['products_quantities'] ) ? $item['products_quantities'] : ''; ?>
				<select class="pewc-field-item pewc-field-products_quantities" name="<?php echo esc_attr( $base_name ); ?>[products_quantities]" data-field-name="products_quantities">
					<option value="independent" <?php selected( $products_quantities, 'independent', true ); ?>><?php _e( 'Independent', 'pewc' ); ?></option>
					<option value="linked" <?php selected( $products_quantities, 'linked', true ); ?>><?php _e( 'Linked', 'pewc' ); ?></option>
					<option value="one-only" <?php selected( $products_quantities, 'one-only', true ); ?>><?php _e( 'One only', 'pewc' ); ?></option>
				</select>

			</div>
		</div>

	</div>

	<div class="pewc-fields-wrapper pewc-child-product-min-max-extras">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Min Child Products', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Specify a minimum number of products the user must choose from this field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $min_products = ( isset( $item['min_products'] ) ) ? intval( $item['min_products'] ) : ''; ?>
				<input type="number" class="pewc-field-item pewc-min-child-products" name="<?php echo esc_attr( $base_name ); ?>[min_products]" value="<?php echo esc_attr( $min_products ); ?>" min="0" max="" step="1" data-field-name="min_products">

			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Max Child Products', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Specify a maximum number of products the user must choose from this field', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $max_products = ( isset( $item['max_products'] ) ) ? intval( $item['max_products'] ) : ''; ?>
				<input type="number" class="pewc-field-item pewc-max-child-products" name="<?php echo esc_attr( $base_name ); ?>[max_products]" value="<?php echo esc_attr( $max_products ); ?>" min="0" max="" step="1" data-field-name="max_products">

			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				
				<label>
					<?php _e( 'Default Quantity', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Specify a default quantity if you wish', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $default_quantity = ( isset( $item['default_quantity'] ) ) ? intval( $item['default_quantity'] ) : ''; ?>
				<input type="number" class="pewc-field-item pewc-default-quantity" name="<?php echo esc_attr( $base_name ); ?>[default_quantity]" value="<?php echo esc_attr( $default_quantity ); ?>" min="0" max="" step="1" data-field-name="default_quantity">

			</div>
		</div>

		<div class="product-extra-field pewc-components-only">
			<div class="product-extra-field-inner">
				
				<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_force_quantity">
					<?php _e( 'Force Quantity?', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enable this option to prevent the user from changing the default quantity', 'pewc' ); ?>
				</label>

			</div>
			<div class="product-extra-field-inner">

				<?php $checked = ! empty( $item['force_quantity'] ); ?>
				<?php pewc_checkbox_toggle( 'force_quantity', $checked, $group_id, $item_key, 'pewc-force-quantity' ); ?>

			</div>
		</div>

	</div>

<?php }

do_action( 'pewc_before_end_products_settings', $item, $group_id, $item_key ); // 4.0.3, used by Bookings

do_action( 'pewc_end_products_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );