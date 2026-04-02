<?php
/**
 * Customer booking cancelled email - Block template.
 *
 * This template can be overridden by editing it in the WooCommerce email editor.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.2.0
 * @since   3.2.0
 */

use Automattic\WooCommerce\Internal\EmailEditor\BlockEmailRenderer;

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Booking Cancelled', 'woocommerce-bookings' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/email-content {"lock":{"move":false,"remove":true}} -->
<div class="wp-block-woocommerce-email-content"><?php echo esc_html( BlockEmailRenderer::WOO_EMAIL_CONTENT_PLACEHOLDER ); ?></div>
<!-- /wp:woocommerce/email-content -->
