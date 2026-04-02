<?php
/**
 * Render the Booking Modal 2 block.
 *
 * @package WooCommerce\Bookings
 * @since 3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = wc_get_product();
if ( ! is_wc_booking_product( $product ) ) {
	return;
}
$buttons = do_blocks(
	'<!-- wp:buttons {"align":"full","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-buttons alignfull">
		<!-- wp:button {"width":100,"className":"is-style-outline continue-shopping"} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline continue-shopping"><a class="wp-block-button__link wp-element-button">' . __( 'Confirm and continue shopping', 'woocommerce-bookings' ) . '</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"width":100, "className":"complete-booking"} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100 complete-booking"><a class="wp-block-button__link wp-element-button">' . __( 'Complete booking', 'woocommerce-bookings' ) . '</a></div>
		<!-- /wp:button -->
	</div><!-- /wp:buttons -->'
);

?>
<div
	data-wp-interactive="woocommerce-bookings/booking-modal"
	data-wp-watch--focusButtonsWhenSlotSelected="callbacks.focusButtonsWhenSlotSelected"
	data-wp-watch--focusModalWhenOpen="callbacks.focusModalWhenOpen"
	>
	<div
		class="wc-bookings-modal-overlay"
		data-wp-bind--hidden="!state.isModalOpen"
		data-wp-on--click="actions.closeModal"
		>
	</div>
	<dialog
		class="wc-bookings-modal"
		data-wp-bind--open="state.isModalOpen"
		data-wp-bind--inert="!state.isModalOpen"
		data-wp-on--keydown="actions.onModalKeyDown"
		data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
		tabindex="-1"
		role="dialog" aria-modal="true"
		aria-label="<?php esc_attr_e( 'Choose a time', 'woocommerce-bookings' ); ?>"
		>
		<div class="wc-bookings-modal-header">
			<h3 class="wc-bookings-modal-title"><?php esc_attr_e( 'Select date and time', 'woocommerce-bookings' ); ?></h3>
			<button
				class="wc-bookings-modal-close"
				data-wp-on--click="actions.closeModal"
				aria-label="<?php esc_attr_e( 'Close modal', 'woocommerce-bookings' ); ?>"
				type="button"
				>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
					<path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path>
				</svg>
			</button>
		</div>
		<div class="wc-bookings-modal-container">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div
			class="wc-bookings-modal-buttons"
			data-wp-class--is-disabled="woocommerce-bookings/booking-form::state.isAddingBookingToCart"
			data-wp-bind--hidden="state.shouldHideAddToCartButton"
			>
			<?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</dialog>
</div>