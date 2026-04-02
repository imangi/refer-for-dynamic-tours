<?php
/**
 * Part template for Customer booking cancelled email.
 *
 * This template is used to display the customer booking cancelled in the email.
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
?>

<?php if ( $email_preview ) : ?>
	<p>
		<?php echo esc_html( __( 'Hello Doe', 'woocommerce-bookings' ) ); ?>
	</p>
<?php elseif ( $booking->get_order() ) : ?>
	<p>
		<?php
		$booking_order = $booking->get_order();
		$first_name    = is_callable( array( $booking_order, 'get_billing_first_name' ) )
			? $booking_order->get_billing_first_name()
			: $booking_order->billing_first_name;

		/* translators: %s: billing first name */
		echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), $first_name ) );
		?>
	</p>
<?php endif; ?>

<p><?php esc_html_e( 'We are sorry to say that your booking could not be confirmed and has been cancelled. The details of the cancelled booking can be found below.', 'woocommerce-bookings' ); ?></p>

<?php
ob_start();
wc_get_template(
	'order/admin/booking-display.php',
	array(
		'booking_ids'   => array( $booking->get_id() ),
		'sent_to_admin' => false,
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

<p><?php esc_html_e( 'Please contact us if you have any questions or concerns.', 'woocommerce-bookings' ); ?></p>
