<?php
/**
 * Customer booking cancelled email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/customer-booking-cancelled.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/introduction-to-woocommerce-bookings/pages-and-emails-customization/
 * @author  Automattic
 * @version 3.2.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email );

wc_get_template(
	'emails/parts/customer-booking-cancelled.php',
	array(
		'email'         => $email,
		'sent_to_admin' => $sent_to_admin,
		'plain_text'    => $plain_text,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);

/**
 * Action hook to add content after the email footer.
 *
 * @since 1.0.0
 *
 * @param WC_Email $email The email object.
 */
do_action( 'woocommerce_email_footer', $email );
