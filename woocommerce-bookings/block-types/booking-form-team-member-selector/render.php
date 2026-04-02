<?php
/**
 * Render the Booking Member Selector block.
 *
 * @package WooCommerce\Bookings
 * @since 3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_wc_booking_product( $product ) ) {
	return;
}

$team_members      = $product->get_resources();
$team_members_data = array();
foreach ( $team_members as $team_member ) {
	if ( ! $team_member instanceof WC_Product_Booking_Team_Member ) {
		continue;
	}

	$team_members_data[] = array(
		'id'   => $team_member->get_id(),
		'name' => $team_member->get_name(),
	);
}

if ( empty( $team_members_data ) ) {
	return;
}

$options_html = '';
foreach ( $team_members_data as $team_member ) {
	$options_html .= sprintf(
		'<option value="%d">%s</option>',
		absint( $team_member['id'] ),
		esc_html( $team_member['name'] )
	);
}
?>
<div
	class="wc-bookings-member-selector"
	data-wp-interactive="woocommerce-bookings/booking-team-member-selector"
	>
	<div class="wc-bookings-member-selector__label">
		<label for="wc-bookings-member-select">
			<?php esc_html_e( 'Team member', 'woocommerce-bookings' ); ?>
			<span class="wc-bookings-member-selector__required-indicator"><?php esc_html_e( '(required)', 'woocommerce-bookings' ); ?></span>
		</label>
	</div>
	<div class="wc-bookings-member-selector__select-container">
		<select
			id="wc-bookings-member-select"
			class="wc-bookings-member-selector__select"
			data-wp-on--change="actions.handleSelectTeamMember"
			data-wp-init="callbacks.teamMemberInit"
			>
			<?php echo $options_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</select>
	</div>
</div>
