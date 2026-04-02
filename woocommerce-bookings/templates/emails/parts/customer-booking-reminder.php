<?php
/**
 * Part template for Customer booking reminder email.
 *
 * This template is used to display the customer booking reminder in the email.
 *
 * @since 3.2.0
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$booking       = $email->object;
$email_preview = 'email_preview' === $booking->get_status();

if ( $booking->get_order() ) :
	?>
	<p>
	<?php
	/* translators: 1: billing first name */
	echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) );
	?>
	</p>
	<?php
elseif ( $email_preview ) :
	?>
	<p>
	<?php
		echo esc_html( __( 'Hello John', 'woocommerce-bookings' ) );
	?>
	</p>
	<?php
else :
	$customer = $booking->customer_id ? get_user_by( 'id', $booking->customer_id ) : false;
	if ( $customer && isset( $customer->user_firstname ) ) :
		?>
		<p>
		<?php
		/* translators: 1: customer first name */
		echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), $customer->user_firstname ) );
		?>
		</p>
		<?php
	endif;
endif;
?>

<p>
<?php
/* translators: 1: booking start date */
echo esc_html( sprintf( __( 'This is a reminder that your booking will take place on %1$s.', 'woocommerce-bookings' ), $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ) );
?>
</p>

<?php
wc_get_template(
	'emails/parts/booking-table.php',
	array(
		'email' => $email,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);
