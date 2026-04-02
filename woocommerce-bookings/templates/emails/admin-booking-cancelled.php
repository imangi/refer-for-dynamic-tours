<?php
/**
 * Admin booking cancelled email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/admin-booking-cancelled.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @since   1.7.8
 * @see     https://woocommerce.com/document/introduction-to-woocommerce-bookings/pages-and-emails-customization/
 * @package WooCommerce_Bookings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Allows users to filter text in email header
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

wc_get_template(
	'emails/parts/admin-booking-cancelled.php',
	array(
		'email'         => $email,
		'sent_to_admin' => $sent_to_admin,
		'plain_text'    => $plain_text,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);

/**
 * Allows users to filter text in email footer
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_footer', $email );
