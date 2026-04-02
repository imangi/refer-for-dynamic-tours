<?php
/**
 * The markup for a field item in the admin
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>


<div class="product-extra-field pewc-option-fields-wrapper">
	<div class="product-extra-field-inner">
		
		<label>
			<?php _e( 'Options', 'pewc' ); ?>
			<?php echo wc_help_tip( 'Click the Add Option button to add a new option', 'pewc' ); ?>
		</label>

	</div>
	<div class="product-extra-field-inner">

		<table id="pewc_option_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" class="pewc-option-fields">

			<thead>

				<tr>
					<th class="pewc-option-image">&nbsp;</th>
					<th class="pewc-option-image alt">
						<?php printf( '<span>%s</span>', __( 'Alt', 'pewc' ) ); ?>
					</th>
					<th class="pewc-option-hex">
						<?php printf( '<span>%s</span>', __( 'Color', 'pewc' ) ); ?>
					</th>
					<th class="pewc-option-option">
						<?php printf( '<span>%s</span>', __( 'Label', 'pewc' ) ); ?>
					</th>
					<th class="pewc-option-price">
						<?php printf( '<span>%s</span>', __( 'Price', 'pewc' ) ); ?>
					</th>

					<?php do_action( 'pewc_after_option_params_titles', $group_id, $item_key, $item ); ?>

					<th class="pewc-actions pewc-select-actions">&nbsp;</th>
				</tr>

			</thead>

			<?php // Add option data to wrapper
			$option_count = 0;
			$data = array();
			if( ! empty( $item['field_options'] ) ) {
				foreach( $item['field_options'] as $key=>$value ) {
					// Escaped this 2.4.5
					$data[] = isset( $value['value'] ) ? $value['value'] : '';
				}
			}
			$data = json_encode( $data ); ?>

			<tbody class="pewc-field-options-wrapper pewc-data-options" data-options='<?php echo esc_attr( $data ); ?>'>

				<?php $option_count = 0;
				if( ! empty( $item['field_options'] ) ) {
					foreach( $item['field_options'] as $key=>$value ) {
						include( PEWC_DIRNAME . '/templates/admin/views/option.php' );
						$option_count++;
					}
				} ?>

			</tbody>

			<tfoot>

				<tr>
					<td colspan="3"><a href="#" class="button add_new_option"><?php _e( 'Add Option', 'pewc' ); ?></a></td>
				</tr>

			</tfoot>

		</table>

	</div>	

</div><!-- .pewc-extra-field -->

<div class="product-extra-field pewc-select-field-only">
	<div class="product-extra-field-inner">
		<label class="pewc-checkbox-field-label">
			<?php _e( 'First field is instruction only', 'pewc' ); ?>
			<?php echo wc_help_tip( 'Select this if your first option is an instruction to the user, e.g. "Pick an item"', 'pewc' ); ?>
		</label>
	</div>
	<div class="product-extra-field-inner">
		<?php $checked = ! empty( $item['first_field_empty'] ); ?>
		<?php pewc_checkbox_toggle( 'first_field_empty', $checked, $group_id, $item_key, 'pewc-first-field-empty' ); ?>
	</div>
</div>
