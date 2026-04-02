<?php
/**
 * The "Team Members" Block Bindings.
 *
 * @package WooCommerce\Bookings
 * @since   3.1.0
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Bookings_Blocks_Team_Member_Block_Bindings class.
 */
class WC_Bookings_Blocks_Team_Member_Block_Bindings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! function_exists( 'register_block_bindings_source' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'register_block_bindings_sources' ) );
	}

	/**
	 * Register the block bindings source.
	 */
	public function register_block_bindings_sources() {

		register_block_bindings_source(
			'woocommerce-bookings/team-member',
			array(
				'label'              => _x( 'Team member', 'block bindings source', 'woocommerce-bookings' ),
				'get_value_callback' => array( $this, 'get_team_member_value' ),
				'uses_context'       => array( 'postId', 'postType' ),
			)
		);
	}

	/**
	 * Gets value for Team Member block bindings source.
	 *
	 * @param array    $source_args    Array containing source arguments used to look up the override value.
	 *                                 Example: array( 'field' => 'name' ).
	 * @param WP_Block $block_instance The block instance.
	 * @param string   $attribute_name The name of the target attribute (unused but required by interface).
	 * @return mixed The value computed for the source.
	 */
	public function get_team_member_value( $source_args, $block_instance, $attribute_name ) { //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		// Validate required arguments.
		if ( empty( $source_args['field'] ) ) {
			return null;
		}

		$team_member_id = $block_instance->context['postId'] ?? null;
		if ( ! $team_member_id ) {
			return null;
		}

		// Check if the post is a team member post type.
		$post_type = get_post_type( $team_member_id );
		if ( 'bookable_team_member' !== $post_type ) {
			return null;
		}

		// Load the team member object.
		$team_member = new WC_Product_Booking_Team_Member( $team_member_id );
		if ( ! $team_member->get_id() ) {
			return null;
		}

		// Get the requested field value.
		$field = $source_args['field'];
		switch ( $field ) {
			case 'name':
				return $team_member->get_name();

			case 'email':
				return $team_member->get_email();

			case 'phone_number':
				return $team_member->get_phone_number();

			case 'description':
				$description = $team_member->get_description();
				if ( ! $description ) {
					return null;
				}

				return nl2br( $description );

			default:
				return null;
		}
	}
}
