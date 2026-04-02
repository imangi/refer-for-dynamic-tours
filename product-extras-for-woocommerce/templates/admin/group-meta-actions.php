<?php
/**
 * The markup for group actions
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="pewc-actions pewc-group-meta-actions">
	<span class="table-panel sort-handle" title="<?php _e( 'Sort', 'pewc' ); ?>"><span class="dashicons dashicons-menu"></span></span>
	<span class="table-panel collapse toggle" title="<?php _e( 'Collapse / Expand', 'pewc' ); ?>">
		<span class="dashicons dashicons-arrow-up"></span>
		<span class="dashicons dashicons-arrow-down"></span>
	</span>
	<span class="table-panel duplicate" title="<?php _e( 'Duplicate', 'pewc' ); ?>"><span class="dashicons dashicons-admin-page"></span></span>
	<span class="table-panel remove" title="<?php _e( 'Delete', 'pewc' ); ?>"><span class="dashicons dashicons-trash"></span></span>
</div>
