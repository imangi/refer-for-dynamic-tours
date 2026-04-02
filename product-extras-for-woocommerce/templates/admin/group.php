<?php
/**
 * The markup for a group
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$group_class = '';
if( ! $group_id ) {
	$has_migrated = true;
	// $group_class = 'new-group-row';
}
if( ! isset( $group_title ) ) $group_title = '';
if( ! isset( $group ) ) $group = array(); ?>

<?php printf(
	'<p class="pewc-group-settings-heading">%s&nbsp;<span class="toggle">%s%s</span></p>',
	__( 'Group Settings', 'pewc' ),
	'<span class="dashicons dashicons-arrow-up"></span>',
	'<span class="dashicons dashicons-arrow-down"></span>'
); ?>

<div class="pewc-group-meta-table wc-metabox <?php echo esc_attr( $group_class ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
	
	<div class="pewc-fields-wrapper">
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Group Title', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter a title for this group that will be displayed on the product page. Leave blank if you wish.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="text" class="pewc-group-title" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_title]" value="<?php echo esc_attr( $group_title ); ?>">
			</div>
		</div>

		<div class="product-extra-field">
			<div class="product-extra-field-inner pewc-description">
				<?php $description = pewc_get_group_description( $group_id, $group, $has_migrated ); ?>
				<label>
					<?php _e( 'Group Description', 'pewc' ); ?>
					<?php echo wc_help_tip( 'An optional description for the group', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<textarea class="pewc-group-description" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_description]"><?php echo esc_html( $description ); ?></textarea>
			</div>
		</div>
	</div><!-- pewc-fields-wrapper -->

	<div class="pewc-fields-wrapper split-half">
	
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $group_layout = pewc_get_group_layout( $group_id ); ?>
				<label>
					<?php _e( 'Group Layout', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Choose how to display the fields in this group.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<select class="pewc-group-layout" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_layout]">
					<?php do_action( 'pewc_start_group_layout_options', $group_layout ); ?>
					<option <?php selected( $group_layout, 'ul', true ); ?> value="ul"><?php _e( 'Standard', 'pewc' ); ?></option>
					<option <?php selected( $group_layout, 'table', true ); ?> value="table"><?php _e( 'Table', 'pewc' ); ?></option>
					<option <?php selected( $group_layout, 'cols-2', true ); ?> value="cols-2"><?php _e( 'Two Columns', 'pewc' ); ?></option>
					<option <?php selected( $group_layout, 'cols-3', true ); ?> value="cols-3"><?php _e( 'Three Columns', 'pewc' ); ?></option>
				</select>
			</div>
		</div>
		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<?php $group_class = pewc_get_group_class( $group_id ); ?>
				<label>
					<?php _e( 'Group Class', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Optional classes to add to this group.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<input type="text" class="pewc-group-class" name="_product_extra_groups_<?php echo $group_id; ?>[meta][group_class]" value="<?php echo esc_attr( $group_class ); ?>" />
			</div>
			
		</div>

	</div><!-- pewc-fields-wrapper -->

	<div class="pewc-fields-wrapper">

		<div class="product-extra-field group-conditions-row">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Group Conditions', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner pewc-fields-conditionals">
				<?php include( PEWC_DIRNAME . '/templates/admin/views/group-condition.php' ); ?>
			</div>
		</div>

	</div><!-- pewc-fields-wrapper -->

	<div class="pewc-fields-wrapper">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Always Include in Order', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this option to ensure that information from the fields in this group are always passed to the order, even if the group is hidden by its conditions.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked = pewc_get_group_include_in_order( $group_id ); ?>
				<?php pewc_checkbox_toggle( 'always_include', $checked, $group_id, false, 'pewc-group-always-include' ); ?>
			</div>
		</div>
	
	</div><!-- pewc-fields-wrapper -->

	<?php $checked = pewc_get_group_repeatable( $group_id );
	$repeater_class = $checked ? '' : 'not-repeatable'; ?>
	
	<div class="pewc-fields-wrapper no-gap <?php echo esc_attr( $repeater_class ); ?>">

		<div class="product-extra-field">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Repeatable', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this option to allow this group to be repeatable on the frontend product pages', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php pewc_checkbox_toggle( 'repeatable', $checked, $group_id, false, 'pewc-group-repeatable' ); ?>
			</div>
		</div>

	</div>
	<div class="pewc-fields-wrapper split-half <?php echo esc_attr( $repeater_class ); ?>">

		<div class="product-extra-field pewc-repeatable-options-<?php echo $group_id ?> pewc-repeatable-by-quantity-<?php echo $group_id ?><?php echo empty( $checked ) ? ' hidden' : ''; ?>">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Attach to Quantity', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this option to repeat a group automatically when the product quantity is increased', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $checked_quantity = pewc_get_group_repeatable_by_quantity( $group_id ); ?>
				<?php pewc_checkbox_toggle( 'repeatable_by_quantity', $checked_quantity, $group_id, false, 'pewc-group-repeatable-by-quantity' ); ?>
			</div>
		</div>

		<div class="product-extra-field pewc-repeatable-options-<?php echo $group_id ?> pewc-repeatable-limit-<?php echo $group_id ?><?php echo empty( $checked ) ? ' hidden' : ''; ?>">
			<div class="product-extra-field-inner">
				<label>
					<?php _e( 'Repeat Limit', 'pewc' ); ?>
					<?php echo wc_help_tip( 'The number of times a group can be repeated on the frontend. Enter 0 for unlimited.', 'pewc' ); ?>
				</label>
			</div>
			<div class="product-extra-field-inner">
				<?php $repeat_limit = pewc_get_group_repeatable_limit( $group_id ); ?>
				<input type="number" class="pewc-group-repeatable-limit" name="_product_extra_groups_<?php echo $group_id; ?>[meta][repeatable_limit]" value="<?php echo $repeat_limit; ?>" />
			</div>
		</div>

		<?php do_action( 'pewc_group_extra_fields', $group_id, $group ); ?>

	</div><!-- pewc-fields-wrapper -->
</div>

<?php printf(
	'<p class="pewc-field-heading">%s</p>',
	__( 'Fields', 'pewc' )
); ?>
<ul class="field-list" data-pewc-field-list-group-id="<?php echo esc_attr( $group_id ); ?>"><?php
	// 3.23.1, ul.field-list needs to be empty with no whitespaces, so that the CSS style ul.field-list.ui-sortable:empty works
	if( isset( $group['items'] ) ) {
		$item_count = 0;
		foreach( $group['items'] as $item ) {
			if( isset( $item['field_type'] ) ) {
				include( PEWC_DIRNAME . '/templates/admin/field-item.php' );
				$item_count++;
			}
		}
	}
?></ul>
<p><a href="#" class="button add_new_field"><?php _e( 'Add Field', 'pewc' ); ?></a></p>
