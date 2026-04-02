<?php
/**
 * Booking table template.
 *
 * This template is used to display the booking table in the email.
 *
 * @since   3.2.0
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Bookings
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$booking         = $email->object;
$email_preview   = 'email_preview' === $booking->get_status();
$booking_product = $booking->get_product();

if ( $email_preview ) {
	$product_title = __( 'Dummy Product', 'woocommerce-bookings' );
} elseif ( $booking_product ) {
	$product_title = $booking_product->get_title();
} else {
	$product_title = '';
}
?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Booked Product', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;">
				<?php
				if ( ! empty( $title_table ) ) {
					echo wp_kses_post( $title_table );
				} else {
					echo esc_html( $product_title );
				}
				?>
			</td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking ID', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_id() ); ?></td>
		</tr>
		<?php
		if ( ! $email_preview && $booking_product && $booking->has_resources() ) :
			$resource = $booking->get_resource();
			if ( $resource ) :
				$resource_label = $booking_product->get_resource_label();
				?>
				<tr>
					<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo esc_html( ( '' !== $resource_label ) ? $resource_label : __( 'Booking Type', 'woocommerce-bookings' ) ); ?></th>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $resource->post_title ); ?></td>
				</tr>
				<?php
			endif;
		endif;
		?>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking Start Date', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_start_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Booking End Date', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $booking->get_end_date( null, null, wc_should_convert_timezone( $booking ) ) ); ?></td>
		</tr>
		<?php if ( wc_should_convert_timezone( $booking ) ) : ?>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php esc_html_e( 'Time Zone', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( str_replace( '_', ' ', $booking->get_local_timezone() ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! $email_preview && $booking->has_persons() ) : ?>
			<?php
			foreach ( $booking->get_persons() as $person_type_id => $qty ) :
				if ( 0 === $qty ) {
					continue;
				}

				$person_type = ( 0 < $person_type_id ) ? get_the_title( $person_type_id ) : __( 'Person(s)', 'woocommerce-bookings' );
				?>
				<tr>
					<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo esc_html( $person_type ); ?></th>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $qty ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
