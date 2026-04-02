<?php
/**
 * The markup for a new group
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div data-group-count="" id="group-" class="group-row new-group-row">
	<input type="hidden" class="pewc_group_id" name="">
	<div class="new-field-table field-table">
		<div class="wc-metabox">

			<div class="pewc-group-heading-wrap">
				<?php
				printf(
					'<h3 class="pewc-group-meta-heading">%s <span class="meta-item-id"></span>: <span class="pewc-display-title"></span></h3>',
					__( 'Group', 'pewc' )
				); ?>

				<?php include( PEWC_DIRNAME . '/templates/admin/group-meta-actions.php' ); ?>
			</div><!-- .pewc-group-heading-wrap -->
			
		</div><!-- .pewc-group-meta-table -->

		<?php do_action( 'pewc_after_new_group_title', false, false, false, false ); ?>

		<div class="pewc-all-fields-wrapper">

			<?php include( PEWC_DIRNAME . '/templates/admin/group.php' ); ?>

		</div><!-- pewc-all-fields-wrapper -->
		
	</div>

</div><!-- .new-group-row -->
