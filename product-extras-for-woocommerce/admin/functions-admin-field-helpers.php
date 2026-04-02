<?php
/**
 * Functions for fields
 * @since 4.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output a toggle in place of standard checkbox
 * @since 4.0.0
 */
function pewc_checkbox_toggle( $field_name, $value, $group_id, $field_id, $class='', $label_class='' ) {
	if( ! $field_id && ! $group_id ) {
		// This is a top level setting
		$name = $field_name;
		$id = $field_name;
	} else if( ! $field_id && $group_id ) {
		// This is a group setting
		$name = '_product_extra_groups_' .  esc_attr( $group_id )  . '[meta][' . esc_attr( $field_name ) . ']';
		$id = '_product_extra_groups_' .  esc_attr( $group_id )  . '_' . esc_attr( $field_name );
	} else {
		// This is a field setting
		$name = '_product_extra_groups_' . esc_attr( $group_id ) . '_' . esc_attr( $field_id ) . '[' . $field_name  .']';
		$id = '_product_extra_groups_' . esc_attr( $group_id ) . '_' . esc_attr( $field_id ) . '_' . $field_name;
	}
	$class .= ' pewc-field-item pewc-switch-checkbox'; ?>
	<label class="pewc-switch <?php echo esc_attr( $label_class ); ?>" for="<?php echo esc_attr( $id ); ?>">
		<input <?php checked( $value, 1, true ); ?> type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( $class ); ?>" value="1" data-field-name="<?php echo esc_attr( $field_name ); ?>"/>
		<div class="slider round"></div>
	</label>
	<?php
}

/**
 * Output a toggle in place of standard checkbox
 * @since 4.0.0
 * 'repeatable', $checked, $post->ID, $field['input_class'], $metabox_attributes
 */
function pewc_global_checkbox_toggle( $field_name, $value, $group_id, $class='', $attributes='' ) {
	$class .= ' pewc-switch-checkbox'; ?>
	<label class="pewc-switch" for="<?php echo esc_attr( $field_name ); ?>">
		<input <?php checked( $value, 1, true ); ?> type="checkbox" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" class="<?php echo esc_attr( $class ); ?>" value="1" data-field-name="<?php echo esc_attr( $field_name ); ?>" <?php echo esc_attr( $attributes ); ?> />
		<div class="slider round"></div>
	</label>
	<?php
}

/**
 * Use the 'Additional' tab for custom fields
 * @since 4.0.0
 */
function pewc_enable_additional_tab() {
	return apply_filters( 'pewc_enable_additional_tab', false );
}

/**
 * Get the settings markup for the General tab
 * @since 4.0.0
 */
function pewc_do_general_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'general', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_general_section', 'pewc_do_general_section', 10, 7 );

/**
 * Get the settings markup for the Pricing tab
 * @since 4.0.0
 */
function pewc_do_pricing_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'pricing', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_pricing_section', 'pewc_do_pricing_section', 10, 7 );

/**
 * Get the settings markup for the Options tab
 * @since 4.0.0
 */
function pewc_do_options_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'options', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_options_section', 'pewc_do_options_section', 10, 7 );

/**
 * Get the settings markup for the Display tab
 * @since 4.0.0
 */
function pewc_do_display_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'display', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_display_section', 'pewc_do_display_section', 10, 7 );

/**
 * Get the settings markup for the Calculations tab
 * @since 4.0.0
 */
function pewc_do_calculations_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'calculations', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_calculations_section', 'pewc_do_calculations_section', 10, 7 );

/**
 * Get the settings markup for the Calendar List tab
 * @since 4.1.0
 */
function pewc_do_calendar_list_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'calendar-list', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_calendar_list_section', 'pewc_do_calendar_list_section', 10, 7 );

/**
 * Get the settings markup for the Products tab
 * @since 4.0.0
 */
function pewc_do_products_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'products', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_products_section', 'pewc_do_products_section', 10, 7 );

/**
 * Get the settings markup for the Products tab
 * @since 4.0.0
 */
function pewc_do_swatches_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'swatches', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_swatches_section', 'pewc_do_swatches_section', 10, 7 );

/**
 * Get the settings markup for the Text tab
 * @since 4.0.0
 */
function pewc_do_text_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'text', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_text_section', 'pewc_do_text_section', 10, 7 );

/**
 * Get the settings markup for the Number tab
 * @since 4.0.0
 */
function pewc_do_number_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'number', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_number_section', 'pewc_do_number_section', 10, 7 );

/**
 * Get the settings markup for the Date tab
 * @since 4.0.0
 */
function pewc_do_date_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'date', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_date_section', 'pewc_do_date_section', 10, 7 );

/**
 * Get the settings markup for the Date tab
 * @since 4.0.0
 */
function pewc_do_uploads_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'uploads', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_uploads_section', 'pewc_do_uploads_section', 10, 7 );

/**
 * Get the settings markup for the Colorpicker tab
 * @since 4.0.0
 */
function pewc_do_colorpicker_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'colorpicker', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_colorpicker_section', 'pewc_do_colorpicker_section', 10, 7 );

/**
 * Get the settings markup for the Information tab
 * @since 4.0.0
 */
function pewc_do_information_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'information', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_information_section', 'pewc_do_information_section', 10, 7 );

/**
 * Get the settings markup for the Additional tab
 * @since 4.0.0
 */
function pewc_do_additional_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'additional', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_additional_section', 'pewc_do_additional_section', 10, 7 );

/**
 * Get the settings markup for the Conditions tab
 * @since 4.0.0
 */
function pewc_do_conditions_section( $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {

	if( ! pewc_use_ajax_sections() ) {
		// AJAX is not enabled
		pewc_get_field_section( 'conditions', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	}
	
}
add_action( 'pewc_field_conditions_section', 'pewc_do_conditions_section', 10, 7 );


/**
 * Return the markup for the specified section
 * @since 4.0.0
 */
function pewc_get_field_section( $section, $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id ) {
	$item = pewc_create_item_object( $item_key );
	include( PEWC_DIRNAME . '/templates/admin/sections/' . $section . '.php' );
}

/**
 * Use AJAX to load each section on field settings
 * @since 4.0.0
 */
function pewc_use_ajax_sections() {
	return apply_filters( 'pewc_use_ajax_sections', false );
}

/**
 * Get the settings markup for the specified section using AJAX
 * @since 4.0.0
 */
function pewc_do_section_ajax() {

	if( ! isset( $_REQUEST['security'] ) || ! wp_verify_nonce( $_REQUEST['security'], 'pewc_add_section_ajax' ) ) {
		wp_send_json_error( 'nonce_fail' );
		exit;
	} else if( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( 'capability_fail' );
		exit;
	}
	
	$group_id = absint( $_REQUEST['group_id'] );
	$item_key = absint( $_REQUEST['item_key'] );
	$base_name = sanitize_text_field( $_REQUEST['base_name'] );
	$field_type = sanitize_text_field( $_REQUEST['field_type'] );
	$field_label = sanitize_text_field( $_REQUEST['field_label'] );
	$admin_label = sanitize_text_field( $_REQUEST['admin_label'] );
	$section = sanitize_text_field( $_REQUEST['section'] );
	$post_id = absint( $_REQUEST['post_id'] );

	ob_start();
	echo pewc_get_field_section( $section, $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label, $post_id );
	$section = ob_get_clean();
	$data = array( 'content' => $section );
	wp_send_json( $data );

}
add_action( 'wp_ajax_pewc_do_section_ajax', 'pewc_do_section_ajax' );