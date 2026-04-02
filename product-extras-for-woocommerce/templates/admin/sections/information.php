<?php
/**
 * The markup for the 'Information' tab's settings
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-fields-wrapper pewc-information-fields">

	<div class="product-extra-field">

		<div class="product-extra-field-inner">

			<label class="pewc-checkbox-field-label" for="<?php echo esc_attr( $base_name ); ?>_field_palettes">
				<?php _e( 'Information', 'pewc' ); ?>
				<?php echo wc_help_tip( 'List information in rows with labels and images', 'pewc' ); ?>
			</label>

		</div>

		<div class="product-extra-field-inner">

			<div class="pewc-information-wrapper">

				<div class="pewc-information-headers pewc-field-information-wrapper">
					<div>&nbsp;</div>
					<div>
						<?php _e( 'Label', 'pewc' ); ?>
					</div>
					<div>
						<?php printf( '<div class="pewc-label">%s</div>', __( 'Data', 'pewc' ) ); ?>
					</div>
					<div class="pewc-actions pewc-select-actions">&nbsp;</div>
				</div>

					<?php $row_count = 0;
					if( ! empty( $item['field_rows'] ) ) {
						foreach( $item['field_rows'] as $key=>$value ) {
							include( PEWC_DIRNAME . '/templates/admin/views/information-row.php' );
							$row_count++;
						}

					} ?>
				
			</div>

			<p><a href="#" class="button add_new_row"><?php _e( 'Add Row', 'pewc' ); ?></a></p>

		</div>

	</div>

</div><!-- .pewc-fields-wrapper -->

<?php
do_action( 'pewc_end_information_settings_section', $base_name, $group_id, $item_key, $field_type, $field_label, $admin_label );