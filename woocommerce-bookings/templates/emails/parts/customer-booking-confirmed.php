<?php
/**
 * Part template for Customer booking confirmed email.
 *
 * This template is used to display the customer booking confirmed in the email.
 *
 * @since 3.2.0
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable Squiz.PHP.EmbeddedPhp.ContentBeforeOpen -- removed to prevent empty new lines.
// phpcs:disable Squiz.PHP.EmbeddedPhp.ContentAfterEnd -- removed to prevent empty new lines.

$booking       = $email->object;
$email_preview = 'email_preview' === $booking->get_status();
?>

<?php if ( $booking->get_order() ) : ?>
	<p>
	<?php
	if ( $email_preview ) {
		echo esc_html( __( 'Hello Doe', 'woocommerce-bookings' ) );
	} else {
		/* translators: 1: billing first name */
		echo esc_html( sprintf( __( 'Hello %s', 'woocommerce-bookings' ), ( is_callable( array( $booking->get_order(), 'get_billing_first_name' ) ) ? $booking->get_order()->get_billing_first_name() : $booking->get_order()->billing_first_name ) ) );
	}
	?>
	</p>
<?php endif; ?>

<p><?php esc_html_e( 'Your booking has been confirmed. The details of your booking are shown below.', 'woocommerce-bookings' ); ?></p>

<?php
wc_get_template(
	'emails/parts/booking-table.php',
	array(
		'email' => $email,
	),
	'woocommerce-bookings',
	WC_BOOKINGS_TEMPLATE_PATH
);

$wc_order = $email->object->get_order();
if ( ! $wc_order ) {
	return;
}

if ( 'pending' === $wc_order->get_status() ) :
	?>
	<p>
	<?php
	/* translators: 1: checkout payment url */
	echo wp_kses_post( sprintf( __( 'To pay for this booking please use the following link: %s', 'woocommerce-bookings' ), '<a href="' . esc_url( $wc_order->get_checkout_payment_url() ) . '">' . __( 'Pay for booking', 'woocommerce-bookings' ) . '</a>' ) );
	?>
	</p>
	<?php
endif;

/**
 * Action hook to add content before the order table.
 *
 * @since 3.2.0
 *
 * @param WC_Order $wc_order The order object.
 * @param bool     $sent_to_admin Whether it's sent to admin or customer.
 * @param bool     $plain_text Whether it's a plain text email.
 * @param WC_Email $email The email object.
 */
do_action( 'woocommerce_email_before_order_table', $wc_order, $sent_to_admin, $plain_text, $email );
?>

<h2>
<?php

$order_date = $wc_order->get_date_created() ? $wc_order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';

echo esc_html( __( 'Order', 'woocommerce-bookings' ) . ': ' . $wc_order->get_order_number() );
?>
(
<?php
echo wp_kses_post( sprintf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order_date ) ), date_i18n( wc_bookings_date_format(), strtotime( $order_date ) ) ) );
?>
)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-bookings' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-bookings' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-bookings' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		switch ( $wc_order->get_status() ) {

			case 'completed':
				echo wp_kses_post( wc_get_email_order_items( $wc_order, array( 'show_sku' => false ) ) );
				break;

			case 'processing':
			default:
				echo wp_kses_post( wc_get_email_order_items( $wc_order, array( 'show_sku' => true ) ) );
				break;
		}
		?>
	</tbody>
	<tfoot>
		<?php
		$wc_order_totals = $wc_order->get_order_item_totals();
		if ( $wc_order_totals ) {
			$i = 0;
			foreach ( $wc_order_totals as $total ) {
				++$i;
				?>
				<tr>
					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php
					if ( 1 === $i ) {
						echo 'border-top-width: 4px;';
					}
					?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
					<td style="text-align:left; border: 1px solid #eee; <?php
					if ( 1 === $i ) {
						echo 'border-top-width: 4px;';
					}
					?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tfoot>
</table>

<?php
/**
 * Action hook to add content after the order table.
 *
 * @since 3.2.0
 *
 * @param WC_Order $wc_order The order object.
 * @param bool     $sent_to_admin Whether it's sent to admin or customer.
 * @param bool     $plain_text Whether it's a plain text email.
 * @param WC_Email $email The email object.
 */
do_action( 'woocommerce_email_after_order_table', $wc_order, $sent_to_admin, $plain_text, $email );

/**
 * Action hook to add content after the order meta.
 *
 * @since 3.2.0
 *
 * @param WC_Order $wc_order The order object.
 * @param bool     $sent_to_admin Whether it's sent to admin or customer.
 * @param bool     $plain_text Whether it's a plain text email.
 * @param WC_Email $email The email object.
 */
do_action( 'woocommerce_email_order_meta', $wc_order, $sent_to_admin, $plain_text, $email );

// phpcs:enable Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
// phpcs:enable Squiz.PHP.EmbeddedPhp.ContentAfterEnd
