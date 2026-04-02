<?php
/**
 * Part template for Customer booking pending confirmation email.
 *
 * This template is used to display the customer booking pending confirmation in the email.
 *
 * @since 3.2.0
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$booking       = $email->object;
$email_preview = 'email_preview' === $booking->get_status();

if ( $booking->get_order() ) :
	?>
	<p>
	<?php
	if ( $email_preview ) {
		echo esc_html( __( 'Hello John Doe', 'woocommerce-bookings' ) );
	} else {
		/* translators: 1: billing first name */
		echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) );
	}
	?>
	</p>
	<?php
endif;
?>

<p><?php esc_html_e( 'Your booking has been received and it\'s pending confirmation. The details of your booking are shown below.', 'woocommerce-bookings' ); ?></p>

<?php
ob_start();
wc_get_template(
	'order/booking-display.php',
	array(
		'booking_ids' => array( $booking->get_id() ),
		'only_title'  => true,
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
