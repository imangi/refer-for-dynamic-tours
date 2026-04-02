<?php
/**
 * Admin new booking email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/emails/admin-new-booking.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/introduction-to-woocommerce-bookings/pages-and-emails-customization/
 * @package WooCommerce_Bookings
 * @version 3.3.0
 * @since   1.0.0
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
	'emails/parts/admin-new-booking.php',
	array(
		'email' => $email,
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
