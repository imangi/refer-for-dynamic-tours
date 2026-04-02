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

<div class="pewc-fields-wrapper pewc-fields-conditionals">

	<div class="product-extra-field">	
		<div class="product-extra-field-inner">
			<label><?php _e( 'Conditions', 'pewc' ); ?></label>
		</div>
		<div class="product-extra-field-inner">
			<?php include( PEWC_DIRNAME . '/templates/admin/condition.php' ); ?>
		</div>
	</div>

</div><!-- .pewc-fields-wrapper -->

<?php $product = wc_get_product( $post_id );
if( $product && $product->is_type( 'variable' ) ) { ?>
	<div class="pewc-fields-wrapper pewc-fields-variations show_if_variable">
		<div class="product-extra-field">	
			<div class="product-extra-field-inner">
				<label><?php _e( 'Variations', 'pewc' ); ?></label>
			</div>
			<div class="product-extra-field-inner">
				<?php $variations = $product->get_children();
				include( PEWC_DIRNAME . '/templates/admin/variation.php' );
				printf(
					'<p>%s</p>',
					__( 'If you only want to show this field for certain variations, enter the variation IDs below. Leave empty to show for all variations.', 'pewc' )
				); ?>
			</div>
		</div>	
	</div><!-- .pewc-fields-wrapper -->
<?php
}

do_action( 'pewc_end_conditions_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );