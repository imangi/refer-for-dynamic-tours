<?php
/**
 * The markup for an information row
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>


<div class="product-extra-row-wrapper pewc-field-information-wrapper" data-row-count="<?php echo esc_attr( isset( $row_count ) ? $row_count : '' ); ?>">

	<div>
		<div class="pewc-field-image">
			<div class='image-preview-wrapper'>
				<a href="#" class="pewc-upload-button pewc-upload-option-image" data-item-id="">
					<?php	$placeholder = trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/placeholder-small.png'; ?>
					<img data-placeholder="<?php echo $placeholder; ?>" src="<?php echo esc_url( $placeholder ); ?>" style="height: 30px; width: 30px;">
				</a>
			</div>
			<input type="hidden" name="" class="pewc-image-attachment-id" value="">
		</div>

	</div>

	<div>
		<input type="text" class="pewc-field-row-label" name="" value="">
	</div>
	<div>
		<input type="text" class="pewc-field-row-data" name="" value="">
	</div>
	<div>
		<span class="sort-row pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-row pewc-action"><span class="dashicons dashicons-trash"></span></span>
	</div>

</div>
