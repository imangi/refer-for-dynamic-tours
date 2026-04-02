<?php
/**
 * The markup for the 'General' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
	
<div class="pewc-fields-wrapper">

    <div class="product-extra-field">
        <div class="product-extra-field-inner">
            <input type="hidden" class="pewc-id pewc-hidden-id-field" name="<?php echo esc_attr( $base_name ); ?>[id]" value="pewc_group_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>">
            <input type="hidden" class="pewc-group-id pewc-hidden-id-field" name="<?php echo esc_attr( $base_name ); ?>[group_id]" value="<?php echo esc_attr( $group_id ); ?>">
            <input type="hidden" class="pewc-field-id pewc-hidden-id-field" name="<?php echo esc_attr( $base_name ); ?>[field_id]" value="<?php echo esc_attr( $item_key ); ?>">
            <label>
                <?php _e( 'Field Label', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Enter a label to appear with this field on the front end', 'pewc' ); ?>
            </label>
        </div>
        <div class="product-extra-field-inner">
            <input type="text" class="pewc-field-item pewc-field-label" name="<?php echo esc_attr( $base_name ); ?>[field_label]" value="<?php echo esc_attr( stripslashes( $field_label ) ); ?>" data-field-name="field_label">
        </div>
    </div>

    <div class="product-extra-field">
        <div class="product-extra-field-inner">
            <label>
                <?php _e( 'Admin Label', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Enter an alternative label just for the back end', 'pewc' ); ?>
            </label>
        </div>
        <div class="product-extra-field-inner">
            <input type="text" class="pewc-field-item pewc-field-admin_label" name="<?php echo esc_attr( $base_name ); ?>[field_admin_label]" value="<?php echo esc_attr( stripslashes( $admin_label ) ); ?>" data-field-name="field_admin_label">
        </div>
    </div>

    <div class="product-extra-field">
        <div class="product-extra-field-inner">
            <label>
                <?php _e( 'Field Type', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Select the field type', 'pewc' ); ?>
            </label>
        </div>
        <div class="product-extra-field-inner pewc-field-type-wrapper">
            <?php $type = $field_type; ?>
            <select class="pewc-field-item pewc-field-type" name="<?php echo esc_attr( $base_name ); ?>[field_type]" id="field_type_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-field-type="<?php echo $type; ?>" data-field-name="field_type">
                <?php
                $field_types = pewc_field_types();
                foreach( $field_types as $key=>$value ) {
                    $selected = selected( $type, $key, false );
                    echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
                } ?>
            </select>

            <?php // Used to diplay pro message
            do_action( 'pewc_end_fields_heading', $item ); ?>

            <small>
				<?php printf(
					'<a href="%s" target="_blank">%s</a>',
					'https://pluginrepublic.com/documentation/field-types/',
					__( 'Click here for guidance on each field type', 'pewc' )
				); ?>
			</small>

        </div>
    </div>

</div>

<div class="pewc-fields-wrapper">

    <div class="product-extra-field">
        <div class="product-extra-field-inner">

            <label>
                <?php _e( 'Field Visibility', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Decide on what pages to show the field', 'pewc' ); ?>
            </label>
            
        </div>
        <div class="product-extra-field-inner">

            <select class="pewc-field-item pewc-field-visibility" name="<?php echo esc_attr( $base_name ); ?>[field_visibility]" id="<?php echo esc_attr( $base_name ); ?>_field_visibility" data-field-name="field_visibility">
                <?php
                $options = array(
                    'visible'			=> __( 'Display everywhere', 'pewc' ),
                    'display_product'	=> __( 'Display on product page only', 'pewc' ),
                    'hide_product'		=> __( 'Hide on product page only', 'pewc' ),
                    'hide_customer'		=> __( 'Hide from customer', 'pewc' ),
                );
                $field_visibility = isset( $item['field_visibility'] ) ? $item['field_visibility'] : 'visible';
                foreach( $options as $key=>$value ) {
                    $selected = selected( $field_visibility, $key, false );
                    echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
                } ?>
            </select>
            
        </div>
    </div>

    <div class="product-extra-field pewc-misc-fields">
        <div class="product-extra-field-inner">
            <label class="pewc-checkbox-field-label">
                <?php _e( 'Required Field?', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Enable this option to require this field', 'pewc' ); ?>
            </label>
        </div>
        <div class="product-extra-field-inner">
            <?php $checked = ! empty( $item['field_required'] ); ?>
            <?php pewc_checkbox_toggle( 'field_required', $checked, $group_id, $item_key, 'pewc-field-required' ); ?>
        </div>
    </div>

    <?php $default = pewc_get_field_default( $item ); ?>

    <div class="product-extra-field pewc-default-fields pewc-misc-fields">
        <div class="product-extra-field-inner">

            <?php $checked = ! empty( $item['field_default_hidden'] ) ? 1 : 0; ?>		
            <label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_default">
                <?php _e( 'Default', 'pewc' ); ?>
                <?php echo wc_help_tip( 'Enter a default value', 'pewc' ); ?>
            </label>
            
        </div>
        <div class="product-extra-field-inner">

            <?php pewc_checkbox_toggle( 'field_default', $checked, $group_id, $item_key, 'pewc-field-default pewc-field-default-field-checkbox', 'pewc-field-default-field-checkbox' ); ?>
            <input type="number" class="pewc-field-item pewc-field-default pewc-field-default-field-number" name="<?php echo esc_attr( $base_name ); ?>[field_default]" step="<?php echo apply_filters( 'pewc_number_field_step', '1', $item ); ?>" value="<?php echo is_numeric( $default ) ? esc_attr( $default ) : ''; ?>" data-field-name="field_default">
            <input type="text" class="pewc-field-item pewc-field-default pewc-field-default-field-text" name="<?php echo esc_attr( $base_name ); ?>[field_default]" value="<?php echo esc_attr( $default ); ?>" data-field-name="field_default">
            <?php $default_hidden = isset( $item['field_default_hidden'] ) ? $item['field_default_hidden'] : ''; ?>
            <input type="hidden" class="pewc-field-item pewc-field-default pewc-field-default-hidden" name="<?php echo esc_attr( $base_name ); ?>[field_default_hidden]" value="<?php echo esc_attr( $default ); ?>" data-field-name="field_default_hidden">
            
        </div>
    </div>

</div>

<div class="pewc-fields-wrapper pewc-misc-fields">

    <div class="product-extra-field pewc-desc-image-wrapper">

        <?php $src = wc_placeholder_img_src();
        $image_wrapper_classes = array(
            'pewc-field-image-' . $item_key
        );
        $remove_class = '';
        $field_image = '';
        if( ! empty( $item['field_image'] ) ) {
            $field_image = $item['field_image'];
            $src = wp_get_attachment_image_src( $item['field_image'] );
            $src = $src[0];
            $image_wrapper_classes[] = 'has-image';
            $remove_class = 'remove-image';
        } ?>

        <div class="product-extra-field-inner">
            <label>
                <?php _e( 'Field Image', 'pewc' ); ?>
                <?php echo wc_help_tip( 'An optional image to accompany the field', 'pewc' ); ?>
            </label>
        </div>

        <div class="product-extra-field-inner pewc-field-image <?php echo join( ' ', $image_wrapper_classes ); ?>">
            <div class='image-preview-wrapper'>
                <a href="#" class="pewc-upload-button <?php echo esc_attr( $remove_class ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>">
                    <img data-placeholder="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" src="<?php echo esc_url( $src ); ?>" width="100" height="100" style="max-height: 100px; width: 100px;">
                </a>
            </div>
            <input type="hidden" name="<?php echo esc_attr( $base_name ); ?>[field_image]" class="pewc-field-item pewc-image-attachment-id" value="<?php echo esc_attr( $field_image ); ?>" data-field-name="field_image">
        </div>
    </div>

    <div class="product-extra-field pewc-description">
        <div class="product-extra-field-inner">
            <label>
                <?php _e( 'Field Description', 'pewc' ); ?>
                <?php echo wc_help_tip( 'An optional description for the field', 'pewc' ); ?>
            </label>
        </div>
        <div class="product-extra-field-inner">
            <?php $description = isset( $item['field_description'] ) ? $item['field_description'] : ''; ?>		
            <textarea class="pewc-field-item pewc-field-description" name="<?php echo esc_attr( $base_name ); ?>[field_description]" data-field-name="field_description"><?php echo esc_html( $description ); ?></textarea>
        </div>
    </div>

</div>

<?php do_action( 'pewc_end_general_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );