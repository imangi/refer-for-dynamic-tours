<?php
/**
 * Part template for Admin booking cancelled email.
 *
 * @since 3.2.0
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$booking = $email->object;
?>
<p><?php esc_html_e( 'The following booking has been cancelled. The details of the cancelled booking can be found below.', 'woocommerce-bookings' ); ?></p>

<?php
ob_start();
wc_get_template(
	'order/admin/booking-display.php',
	array(
		'booking_ids'   => array( $booking->get_id() ),
		'sent_to_admin' => true,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);
$title_table = ob_get_clean();

wc_get_template(
	'emails/parts/booking-table.php',
	array(
		'email'       => $email,
		'title_table' => $title_table,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);
?>

<p>
<?php
$edit_booking_url  = wc_bookings_get_edit_booking_url( $booking );
$edit_booking_link = sprintf(
	'<a href="%1$s">%2$s</a>',
	esc_url( $edit_booking_url ),
	__( 'Edit booking', 'woocommerce-bookings' )
);

/* translators: 1: a href to booking */
echo wp_kses_post( sprintf( __( 'You can view and edit this booking in the dashboard here: %s', 'woocommerce-bookings' ), $edit_booking_link ) );
?>
</p>
