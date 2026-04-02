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


<div class="product-extra-row-wrapper pewc-field-calendar-list-wrapper" data-row-count="<?php echo esc_attr( isset( $row_count ) ? $row_count : '' ); ?>">

	<div>
		<input type="number" class="pewc-field-row-offset" name="" value="" min="0">
	</div>
	<div>
		<input type="text" class="pewc-field-row-price wc_input_price" name="" value="">
	</div>
	<div>
		<span class="sort-row pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-row pewc-action"><span class="dashicons dashicons-trash"></span></span>
	</div>

</div>
