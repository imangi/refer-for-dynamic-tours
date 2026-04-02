<?php
/**
 * Customer booking notification email - Block template.
 *
 * This template can be overridden by editing it in the WooCommerce email editor.
 * This is a manually sent notification email with custom content set by the admin.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.2.0
 * @since   3.2.0
 */

use Automattic\WooCommerce\Internal\EmailEditor\BlockEmailRenderer;

defined( 'ABSPATH' ) || exit;
?>

<!-- wp:woocommerce/email-content {"lock":{"move":false,"remove":true}} -->
<div class="wp-block-woocommerce-email-content"><?php echo esc_html( BlockEmailRenderer::WOO_EMAIL_CONTENT_PLACEHOLDER ); ?></div>
<!-- /wp:woocommerce/email-content -->
