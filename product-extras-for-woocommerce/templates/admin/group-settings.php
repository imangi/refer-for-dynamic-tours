<?php
/**
 * The markup for options under groups
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! pewc_is_pro() ) {
	// return;
} ?>

<div class="options_group pewc-group-settings">

	<?php printf(
		'<h2><strong>%s</strong></h2>',
		__( 'Product Settings', 'pewc' )
	); ?>

	<div class="pewc-group-section">

		<div class="pewc-fields-wrapper">

			<div class="product-extra-field">	
				<div class="product-extra-field-inner">
					<label for="pewc_global_groups_by_product">
						<?php _e( 'Display groups as', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Choose a layout for the groups on the front end', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<?php $args = array(
						'id'			=> 'pewc_display_groups',
						'class' 		=> 'pewc-display-groups',
						'label'			=> __( 'Display groups as', 'pewc' ),
						'wrapper_class'	=> '',
						'options'		=> array(
							'standard'		=> __( 'Standard', 'pewc' ),
							'accordion'		=> __( 'Accordion', 'pewc' ),
							'lightbox'		=> __( 'Lightbox', 'pewc' ),
							'steps'			=> __( 'Steps', 'pewc' ),
							'tabs'			=> __( 'Tabs', 'pewc' ),
						)
					);
					woocommerce_wp_select( $args ); ?>
				</div>
			</div>

			<?php // Use specific global groups for this product ?>
			<div class="product-extra-field">	
				<div class="product-extra-field-inner">
					<label for="pewc_global_groups_by_product">
						<?php _e( 'Assign global groups', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Specify global groups to be used on this product', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<?php $global_group_ids = pewc_get_global_groups_list();
					$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false;
					$pewc_global_groups_by_product = get_post_meta( $post_id, 'pewc_global_groups_by_product', true );
					$pewc_global_groups_by_product = explode( ',', $pewc_global_groups_by_product ); ?>
					<select multiple="multiple" class="pewc-pewc_global_groups_by_product-groups pewc-multiselect" name="pewc_global_groups_by_product[]" id="pewc_global_groups_by_product">';
						<?php foreach( $global_group_ids as $group_id=>$group_label ) {
							$group_label = str_replace( '()', '', $group_label );
							$selected = ( isset( $pewc_global_groups_by_product ) && is_array( $pewc_global_groups_by_product ) && in_array( trim( $group_id ), $pewc_global_groups_by_product ) ) ? 'selected="selected"' : ''; ?>
							<option <?php echo $selected; ?> value="<?php echo trim( $group_id ); ?>"><?php echo trim( $group_label ); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="product-extra-field">	
				<div class="product-extra-field-inner">
					<label for="pewc_hide_quantity">
						<?php _e( 'Hide quantity field', 'pewc' ); ?> 
						<?php echo wc_help_tip( 'Hide the main quantity field on the frontend', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-inner">
					<?php $pewc_hide_quantity = get_post_meta( $post_id, 'pewc_hide_quantity', true );
					$checked = ( $pewc_hide_quantity == 'yes' || $pewc_hide_quantity == true ) ? 1 : 0;
					pewc_checkbox_toggle( 'pewc_hide_quantity', $checked, false, false ); ?>
				</div>
			</div>

		</div><!-- .pewc-fields-wrapper -->

	</div>

</div>

<?php
if( ! apply_filters( 'pewc_enable_assign_duplicate_groups', false ) ) {
	return;
} ?>

<div class="options_group">

	<div class="pewc-group-options-wrap">
		<?php printf(
			'<h3 class="pewc-group-meta-heading">%s</h3>',
			__( 'Assign groups to other products', 'pewc' )
		); ?>
	</div>

	<p class="form-field">
		<?php printf(
			'<label>%s</label>',
			__( 'Assign to products', 'pewc' )
		); ?>
		<select class="wc-product-search" data-options="" multiple="multiple" style="width: 100%;" name="pewc_assign_to_products[]" id="pewc_assign_to_products" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose the products', 'pewc' ); ?>" data-action="woocommerce_json_search_products" data-include="" data-exclude="">
		</select>
	</p>

	<?php $args = array(
		'id'		=> 'pewc_replace_existing_groups',
		'class' 	=> 'pewc-replace-existing-groups',
		'label'		=> __( 'Replace existing groups', 'pewc' )
	);
	woocommerce_wp_checkbox( $args ); ?>

	<p>
		<?php printf(
			'<a href="#" class="pewc_assign_groups_to_products button button-primary" id="pewc_assign_groups_to_products">%s</a>',
			__( 'Assign', 'pewc' )
		); ?>
	</p>

</div>
