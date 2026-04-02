<?php
/**
 * Part template for Customer booking notification email.
 *
 * This template is used to display the customer booking notification in the email.
 *
 * @since 3.2.0
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$notification_message = $email->notification_message;

if ( isset( $notification_message ) ) {
	echo esc_html( wptexturize( $notification_message ) );
}

wc_get_template(
	'emails/parts/booking-table.php',
	array(
		'email' => $email,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);
