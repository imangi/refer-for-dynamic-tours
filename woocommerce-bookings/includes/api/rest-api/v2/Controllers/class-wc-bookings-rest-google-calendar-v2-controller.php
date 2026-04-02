<?php
/**
 * REST API for Google Calendar connection.
 *
 * Handles requests to the /google-calendar endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Google Calendar controller class.
 */
class WC_Bookings_REST_Google_Calendar_V2_Controller extends WC_REST_Data_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'google-calendar';

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = WC_Bookings_REST_API::V2_NAMESPACE;

	/**
	 * Register the routes for Google Calendar.
	 */
	public function register_routes() {
		// Connection status endpoint.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/connection',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_connection_status' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'disconnect' ),
					'permission_callback' => array( $this, 'disconnect_permissions_check' ),
				),
				'schema' => array( $this, 'get_connection_schema' ),
			)
		);

		// Calendars list endpoint.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/calendars',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_calendars' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_calendars_schema' ),
			)
		);

		// Settings endpoint.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/settings',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'update_settings_permissions_check' ),
					'args'                => array(
						'calendar_id' => array(
							'description' => __( 'The selected Google Calendar ID.', 'woocommerce-bookings' ),
							'type'        => 'string',
							'required'    => true,
						),
					),
				),
				'schema' => array( $this, 'get_settings_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read connection status.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a request has access to disconnect.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function disconnect_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a request has access to update settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function update_settings_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get Google Calendar connection status.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_connection_status( $request ) {
		$connection = WC_Bookings_Google_Calendar_Connection::instance();

		return rest_ensure_response(
			array(
				'connected'   => $connection->has_valid_connection(),
				'connect_url' => $connection->get_connection_auth_url(),
			)
		);
	}

	/**
	 * Disconnect from Google Calendar.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function disconnect( $request ) {
		$connection = WC_Bookings_Google_Calendar_Connection::instance();
		$success    = $connection->disconnect();

		if ( ! $success ) {
			return new WP_Error(
				'rest_google_calendar_disconnect_failed',
				__( 'Failed to disconnect from Google Calendar.', 'woocommerce-bookings' ),
				array( 'status' => 500 )
			);
		}

		return rest_ensure_response(
			array(
				'success'   => true,
				'connected' => false,
				'message'   => __( 'Successfully disconnected from Google Calendar.', 'woocommerce-bookings' ),
			)
		);
	}

	/**
	 * Get the list of available Google calendars.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_calendars( $request ) {
		$connection = WC_Bookings_Google_Calendar_Connection::instance();

		if ( ! $connection->has_valid_connection() ) {
			return new WP_Error(
				'rest_google_calendar_not_connected',
				__( 'Google Calendar is not connected.', 'woocommerce-bookings' ),
				array( 'status' => 400 )
			);
		}

		return rest_ensure_response(
			array(
				'calendars' => $connection->get_calendars(),
			)
		);
	}

	/**
	 * Get Google Calendar settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_settings( $request ) {
		$connection = WC_Bookings_Google_Calendar_Connection::instance();

		return rest_ensure_response(
			array(
				'calendar_id' => $connection->get_selected_calendar_id(),
			)
		);
	}

	/**
	 * Update Google Calendar settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_settings( $request ) {
		$connection  = WC_Bookings_Google_Calendar_Connection::instance();
		$calendar_id = $request->get_param( 'calendar_id' );
		$success     = $connection->set_selected_calendar_id( $calendar_id );

		if ( ! $success ) {
			return new WP_Error(
				'rest_google_calendar_settings_update_failed',
				__( 'Failed to update Google Calendar settings.', 'woocommerce-bookings' ),
				array( 'status' => 500 )
			);
		}

		return rest_ensure_response(
			array(
				'success'     => true,
				'calendar_id' => $calendar_id,
				'message'     => __( 'Settings saved successfully.', 'woocommerce-bookings' ),
			)
		);
	}

	/**
	 * Get the connection schema.
	 *
	 * @return array
	 */
	public function get_connection_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'google_calendar_connection',
			'type'       => 'object',
			'properties' => array(
				'connected'   => array(
					'description' => __( 'Whether Google Calendar is connected.', 'woocommerce-bookings' ),
					'type'        => 'boolean',
					'readonly'    => true,
				),
				'connect_url' => array(
					'description' => __( 'URL to initiate Google Calendar connection.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the calendars schema.
	 *
	 * @return array
	 */
	public function get_calendars_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'google_calendar_calendars',
			'type'       => 'object',
			'properties' => array(
				'calendars' => array(
					'description' => __( 'List of available Google calendars.', 'woocommerce-bookings' ),
					'type'        => 'array',
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'      => array(
								'type'        => 'string',
								'description' => __( 'Calendar ID.', 'woocommerce-bookings' ),
							),
							'name'    => array(
								'type'        => 'string',
								'description' => __( 'Calendar name.', 'woocommerce-bookings' ),
							),
							'primary' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this is the primary calendar for the account.', 'woocommerce-bookings' ),
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the settings schema.
	 *
	 * @return array
	 */
	public function get_settings_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'google_calendar_settings',
			'type'       => 'object',
			'properties' => array(
				'calendar_id' => array(
					'description' => __( 'The selected Google Calendar ID.', 'woocommerce-bookings' ),
					'type'        => 'string',
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
