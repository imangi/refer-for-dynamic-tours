<?php
/**
 * The markup for a field item in the admin
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! isset( $group_id  ) ) {
	$group_id = '';
}
if( ! isset( $group  ) ) {
	$group = array();
}
if( ! isset( $item  ) ) {
	$item = array();
}
if( ! isset( $post_id  ) ) {
	$post_id = false;
}
$item_key = ! empty( $item['field_id'] ) ? $item['field_id'] : false;
$per_char_checked = ! empty( $item['per_character'] );
$show_char_counter_checked = ! empty( $item['show_char_counter'] );

// Update radio-image to image-swatch
$field_type = ! empty(  $item['field_type'] ) ?  $item['field_type'] : '';
if( $field_type == 'radio_image' ) $field_type = 'image_swatch';

$base_name = '_product_extra_groups_' . esc_attr( $group_id ) . '_' . esc_attr( $item_key );

if( ! $item ) {
	$item_classes = array( 'new-field-item', 'field-item' );
} else {
	$item_classes = array(
		'field-item',
		'collapsed-field',
		'field-type-' . esc_attr( $field_type )
	);
	if( $per_char_checked ) {
		$item_classes[] = 'per-char-selected';
	}
	if( $show_char_counter_checked ) {
		$item_classes[] = 'show-char-counter-selected';
	}
	if( ! empty( $item['products_layout'] ) ) {
		$item_classes[] = 'products-layout-' . $item['products_layout'];
	}
	if( ! empty( $item['products_quantities'] ) ) {
		$item_classes[] = 'products-quantities-' . $item['products_quantities'];
	}
	if( ! empty( $item['formula_action'] ) ) {
		$item_classes[] = 'field-action-' . $item['formula_action'];
	}
} ?>

<li id="pewc_group_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-size-count="<?php echo esc_attr( $item_key ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" class="<?php echo join( ' ', $item_classes ); ?>">

	<div class="pewc-fields-wrapper pewc-clickable-heading">
		<?php
		$field_label = ! empty( $item['field_label'] ) ? $item['field_label'] : '';
		$admin_label = ! empty( $item['field_admin_label'] ) ? $item['field_admin_label'] : '';
		$display_label = $admin_label ? $admin_label : $field_label;
		printf(
			'<h3 class="pewc-field-meta-heading">%s <span class="meta-item-id">%s</span>: <span class="pewc-display-field-title">%s</span></h3>',
			__( 'Field', 'pewc' ),
			'&#35;' . $item_key,
			stripslashes( $display_label )
		); ?>

		<?php include( PEWC_DIRNAME . '/templates/admin/field-meta-actions.php' ); ?>

	</div>

	<div class="pewc-field-content-wrapper" data-base-name="<?php echo esc_attr( $base_name ); ?>" data-field-type="<?php echo esc_attr( $field_type ); ?>" data-field-label="<?php echo esc_attr( $field_label ); ?>" data-admin-label="<?php echo esc_attr( $admin_label ); ?>" >

		<ul class="pewc-field-navigation pewc-field-navigation-<?php echo $item_key; ?>" data-field-id="<?php echo $item_key; ?>">
			<li data-id="general" class="pewc-field-navigation-general active"><a href="#"><?php _e( 'General', 'pewc' ); ?></a></li>
			<li data-id="options" class="pewc-field-navigation-hide pewc-field-navigation-options"><a href="#"><?php _e( 'Options', 'pewc' ); ?></a></li>
			<li data-id="calculations" class="pewc-field-navigation-hide pewc-field-navigation-calculations"><a href="#"><?php _e( 'Calculations', 'pewc' ); ?></a></li>
			<li data-id="calendar-list" class="pewc-field-navigation-hide pewc-field-navigation-calendar-list"><a href="#"><?php _e( 'Calendar List', 'pewc' ); ?></a></li>
			<li data-id="products" class="pewc-field-navigation-hide pewc-field-navigation-products"><a href="#"><?php _e( 'Products', 'pewc' ); ?></a></li>
			<li data-id="swatches" class="pewc-field-navigation-hide pewc-field-navigation-swatches"><a href="#"><?php _e( 'Swatches', 'pewc' ); ?></a></li>
			<li data-id="text" class="pewc-field-navigation-hide pewc-field-navigation-text"><a href="#"><?php _e( 'Text', 'pewc' ); ?></a></li>
			<li data-id="number" class="pewc-field-navigation-hide pewc-field-navigation-number"><a href="#"><?php _e( 'Number', 'pewc' ); ?></a></li>
			<li data-id="date" class="pewc-field-navigation-hide pewc-field-navigation-date"><a href="#"><?php _e( 'Date', 'pewc' ); ?></a></li>
			<li data-id="uploads" class="pewc-field-navigation-hide pewc-field-navigation-uploads"><a href="#"><?php _e( 'Uploads', 'pewc' ); ?></a></li>
			<li data-id="color" class="pewc-field-navigation-hide pewc-field-navigation-color"><a href="#"><?php _e( 'Color picker', 'pewc' ); ?></a></li>
			<li data-id="information" class="pewc-field-navigation-hide pewc-field-navigation-information"><a href="#"><?php _e( 'Information', 'pewc' ); ?></a></li>
			<li data-id="pricing" class="pewc-misc-fields"><a href="#"><?php _e( 'Pricing', 'pewc' ); ?></a></li>
			<li data-id="display"><a href="#"><?php _e( 'Display', 'pewc' ); ?></a></li>
			<?php if( pewc_enable_additional_tab() ) { ?>
				<li data-id="additional"><a href="#"><?php _e( 'Additional', 'pewc' ); ?></a></li>
			<?php } ?>
			<li data-id="conditions"><a href="#"><?php _e( 'Conditions', 'pewc' ); ?></a></li>
		</ul>

		<div id="general-section-<?php echo $item_key; ?>" class="pewc-section active">

			<?php do_action( 'pewc_field_general_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="pricing-section-<?php echo $item_key; ?>" class="pewc-section pewc-misc-fields">

			<?php do_action( 'pewc_field_pricing_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="options-section-<?php echo $item_key; ?>" class="pewc-section pewc-section-options">

			<?php do_action( 'pewc_field_options_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="display-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_display_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="calculations-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_calculations_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="calendar-list-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_calendar_list_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="products-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_products_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="swatches-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_swatches_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="text-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_text_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="number-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_number_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="date-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_date_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="uploads-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_uploads_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="color-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_colorpicker_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="information-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_information_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="additional-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_additional_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->

		<div id="conditions-section-<?php echo $item_key; ?>" class="pewc-section">

			<?php do_action( 'pewc_field_conditions_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ); ?>

		</div><!-- .pewc-section -->
	
	</div><!-- .pewc-field-content-wrapper -->

</li>
