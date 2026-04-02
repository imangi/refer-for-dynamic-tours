<?php
/**
 * REST API controller for resource team members objects.
 *
 * Handles requests to the /resources/team-members endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Team Members controller class.
 */
class WC_Bookings_REST_Team_Members_V2_Controller extends WC_Bookings_REST_CRUD_Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'resources/team-members';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'bookable_team_member';

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = WC_Bookings_REST_API::V2_NAMESPACE;

	/**
	 * Get object.
	 *
	 * @since 3.0.0
	 *
	 * @param int $id Object ID.
	 *
	 * @return WC_Product_Booking_Team_Member|false
	 */
	protected function get_object( $id ) {
		if ( get_post_type( $id ) !== $this->post_type ) {
			return false;
		}

		return new WC_Product_Booking_Team_Member( $id );
	}

	/**
	 * Create a single team member.
	 *
	 * @since 3.2.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_item( $request ) {
		$response = parent::create_item( $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $this->sync_and_refresh_response( $response, $request );
	}

	/**
	 * Update a single team member.
	 *
	 * @since 3.2.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		$response = parent::update_item( $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $this->sync_and_refresh_response( $response, $request );
	}

	/**
	 * Sync product relationships and refresh the response payload.
	 *
	 * @since 3.2.0
	 *
	 * @param WP_REST_Response $response Response object.
	 * @param WP_REST_Request  $request  Request object.
	 *
	 * @return WP_REST_Response
	 */
	private function sync_and_refresh_response( $response, $request ) {
		if ( ! isset( $request['product_booking_ids'] ) ) {
			return $response;
		}

		$data        = $response->get_data();
		$resource_id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;

		if ( ! $resource_id ) {
			return $response;
		}

		$this->sync_product_booking_ids(
			$resource_id,
			wp_parse_id_list( $request['product_booking_ids'] )
		);

		$resource_obj = $this->get_object( $resource_id );
		if ( $resource_obj ) {
			$refreshed = $this->prepare_object_for_response( $resource_obj, $request );
			$response->set_data( $refreshed->get_data() );
		}

		return $response;
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @since 3.0.0
	 *
	 * @param  WC_Product_Booking_Team_Member $resource_obj Object data.
	 * @param  WP_REST_Request                $request      Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $resource_obj, $request ) {

		parent::prepare_object_for_response( $resource_obj, $request );
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = array(
			'id'                  => $resource_obj->get_id(),
			'status'              => $resource_obj->get_status( $context ),
			'name'                => $resource_obj->get_name( $context ),
			'qty'                 => $resource_obj->get_qty( $context ),
			'role'                => $resource_obj->get_role( $context ),
			'email'               => $resource_obj->get_email( $context ),
			'phone_number'        => $resource_obj->get_phone_number( $context ),
			'image_id'            => $resource_obj->get_image_id( $context ),
			'image_url'           => '',
			'description'         => $resource_obj->get_description( $context ),
			'note'                => $resource_obj->get_note( $context ),
			'availability_source' => $resource_obj->get_availability_source( $context ),
			'availability'        => $this->format_availability_for_response( $resource_obj->get_availability( $context ) ),
			'product_booking_ids' => $this->get_product_booking_ids_for_resource( $resource_obj->get_id() ),
		);

		// Get attachment URL from image_id.
		if ( $data['image_id'] ) {
			$image_url         = wp_get_attachment_image_url( $data['image_id'], 'large' );
			$data['image_url'] = $image_url ? $image_url : '';
		}

		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $resource_obj, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $resource_obj Object data.
		 * @param WP_REST_Request  $request Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $resource_obj, $request );
	}

	/**
	 * Prepare objects query.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$query_args       = parent::prepare_objects_query( $request );
		$allowed_statuses = WC_Product_Booking_Resource::get_allowed_statuses();

		// Filter team members by include_status.
		if ( ! empty( $request['include_status'] ) ) {
			$statuses = $request['include_status'];

			if ( ! is_array( $statuses ) ) {
				$statuses = array( $statuses );
			}

			$valid_statuses = array_intersect( $statuses, $allowed_statuses );

			if ( ! empty( $valid_statuses ) ) {
				$query_args['post_status'] = $valid_statuses;
			}
		}

		// Filter team members by excluding statuses.
		if ( ! empty( $request['exclude_status'] ) ) {
			$exclude_statuses = $request['exclude_status'];

			if ( ! is_array( $exclude_statuses ) ) {
				$exclude_statuses = array( $exclude_statuses );
			}

			$valid_exclude_statuses = array_intersect( $exclude_statuses, $allowed_statuses );

			if ( ! empty( $valid_exclude_statuses ) ) {
				// Remove excluded statuses from the query.
				$current_post_statuses     = isset( $query_args['post_status'] ) ? (array) $query_args['post_status'] : $allowed_statuses;
				$query_args['post_status'] = array_values( array_diff( $current_post_statuses, $valid_exclude_statuses ) );
			}
		}

		return $query_args;
	}

	/**
	 * Validate the request for updating a resource.
	 *
	 * @since 3.0.0
	 *
	 * @param array $request The request data.
	 *
	 * @return WP_Error|true Returns a WP_Error object if the request is invalid, otherwise returns true.
	 */
	private function validate_update_request( $request ) {

		if ( isset( $request['image_id'] ) && ! empty( $request['image_id'] ) ) {
			$attachment = get_post( $request['image_id'] );
			if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
				return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_image_id', __( 'Invalid image ID. Must be a valid attachment ID.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
			}
		}

		if ( isset( $request['qty'] ) && (int) $request['qty'] < 0 ) {
			return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_qty', __( 'Quantity must be a positive number.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
		}

		if ( isset( $request['availability'] ) ) {
			$availability_validation = $this->validate_availability_request( $request['availability'] );
			if ( is_wp_error( $availability_validation ) ) {
				return $availability_validation;
			}
		}

		if ( isset( $request['product_booking_ids'] ) ) {
			if ( ! is_array( $request['product_booking_ids'] ) ) {
				return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_product_ids', __( 'Product booking IDs must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
			}

			foreach ( $request['product_booking_ids'] as $product_id ) {
				$product = wc_get_product( absint( $product_id ) );
				if ( ! is_wc_booking_product( $product ) ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_product_ids', __( 'Invalid product booking ID. Must be a valid booking product.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}
			}
		}

		return true;
	}

	/**
	 * Prepare a single resource for create or update.
	 *
	 * @since 3.0.0
	 *
	 * @param array $request Request object.
	 * @param bool  $creating If creating a new object.
	 *
	 * @return WP_Error|true Returns a WP_Error object if the request is invalid, otherwise returns true.
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {

		/**
		 * Handle the WC_Product_Booking_Team_Member object.
		 */
		$id = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		if ( ! $creating && ! $id ) {
			return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
		}

		if ( $id ) {
			$resource = get_wc_product_booking_resource( $id );
			if ( ! $resource ) {
				return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
			}
		} else {
			$resource = new WC_Product_Booking_Team_Member();
		}

		// When creating a new resource, name is required and should not be empty.
		if ( $creating && ( ! isset( $request['name'] ) || empty( $request['name'] ) ) ) {
			return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_name', __( 'Name is required.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
		}

		// When updating a resource, name is optional and should not be empty.
		if ( ! $creating && isset( $request['name'] ) && empty( $request['name'] ) ) {
			return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_name', __( 'Name cannot be empty.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
		}

		/**
		 * Handle validation and sanity checks.
		 */
		$validation = $this->validate_update_request( $request );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		/**
		 * Set props.
		 */
		if ( isset( $request['name'] ) ) {
			$resource->set_name( $request['name'] );
		}

		if ( isset( $request['qty'] ) ) {
			$resource->set_qty( $request['qty'] );
		}

		if ( isset( $request['role'] ) ) {
			$resource->set_role( $request['role'] );
		}

		if ( isset( $request['email'] ) ) {
			$resource->set_email( $request['email'] );
		}

		if ( isset( $request['phone_number'] ) ) {
			$resource->set_phone_number( $request['phone_number'] );
		}

		if ( isset( $request['image_id'] ) ) {
			$resource->set_image_id( $request['image_id'] );
		}

		if ( isset( $request['description'] ) ) {
			$resource->set_description( sanitize_textarea_field( $request['description'] ) );
		}

		if ( isset( $request['note'] ) ) {
			$resource->set_note( sanitize_textarea_field( $request['note'] ) );
		}

		if ( isset( $request['availability_source'] ) ) {
			$resource->set_availability_source( $request['availability_source'] );
		}

		if ( isset( $request['status'] ) ) {
			$resource->set_status( $request['status'] );
		}

		if ( $resource->get_availability_source( 'edit' ) === 'store' ) {
			if ( isset( $request['availability'] ) ) {
				// Process new availability from request, but only keep date overrides (strip weekly rules).
				$new_rules = $this->map_availability_for_database( $request['availability'] );
				$resource->set_availability( $this->get_date_override_rules( $new_rules ) );
			} else {
				// No new availability sent — just preserve existing date overrides.
				$date_overrides_only = $this->get_date_override_rules( $resource->get_availability( 'edit' ) );
				$resource->set_availability( $date_overrides_only );
			}
		} elseif ( isset( $request['availability'] ) ) {
			$availability_rules = $this->map_availability_for_database( $request['availability'] );
			$resource->set_availability( $availability_rules );
		} elseif (
			isset( $request['availability_source'] ) &&
			'team_member' === $request['availability_source'] &&
			empty( $this->get_weekly_rules( $resource->get_availability( 'edit' ) ) )
		) {
			// If the team member has no availability, set "unavailable all day" rules for each day of the week.
			$default_weekly = $this->map_availability_for_database(
				array(
					'weekly_schedule' => array_map(
						function ( $day ) {
							return array(
								'id'         => $day,
								'time_slots' => array(),
							);
						},
						range( 1, 7 )
					),
				)
			);
			// Preserve any existing date overrides.
			$existing_overrides = $this->get_date_override_rules( $resource->get_availability( 'edit' ) );
			$resource->set_availability( array_merge( $default_weekly, $existing_overrides ) );
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * @since 3.0.0
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param  WC_Data          $resource  Object object.
		 * @param  WP_REST_Request  $request   Request object.
		 * @param  bool             $creating  Whether creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $resource, $request, $creating );
	}

	/**
	 * Get the resource schema, conforming to JSON Schema.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'resource',
			'type'       => 'object',
			'properties' => array(
				'id'                  => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce-bookings' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status'              => array(
					'description' => __( 'Status of the resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'enum'        => WC_Product_Booking_Resource::get_allowed_statuses(),
					'context'     => array( 'view', 'edit' ),
				),
				'name'                => array(
					'description' => __( 'Name of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'qty'                 => array(
					'description' => __( 'Quantity of resource.', 'woocommerce-bookings' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'role'                => array(
					'description' => __( 'Role of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'email'               => array(
					'description' => __( 'Email of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'format'      => 'email',
					'context'     => array( 'view', 'edit' ),
				),
				'phone_number'        => array(
					'description' => __( 'Phone number of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'image_id'            => array(
					'description' => __( 'Attachment ID of resource.', 'woocommerce-bookings' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'image_url'           => array(
					'description' => __( 'URL of the resource image.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description'         => array(
					'description' => __( 'Description of resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'note'                => array(
					'description' => __( 'Note for resource.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'availability_source' => array(
					'description' => __( 'Source of availability for the team member: use team member schedule or store schedule.', 'woocommerce-bookings' ),
					'type'        => 'string',
					'enum'        => array( 'team_member', 'store' ),
					'context'     => array( 'view', 'edit' ),
				),
				'availability'        => array(
					'description' => __( 'Availability rules for the team member.', 'woocommerce-bookings' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'weekly_schedule' => array(
							'description' => __( 'Weekly schedule with time slots per day.', 'woocommerce-bookings' ),
							'type'        => 'array',
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'id'         => array(
										'type'        => 'integer',
										'description' => __( 'Day of week index (1-7).', 'woocommerce-bookings' ),
									),
									'time_slots' => array(
										'type'  => 'array',
										'items' => array(
											'type'       => 'object',
											'properties' => array(
												'start' => array(
													'type' => 'string',
													'description' => __( 'Start time in HH:MM format.', 'woocommerce-bookings' ),
													'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
												),
												'end'   => array(
													'type' => 'string',
													'description' => __( 'End time in HH:MM format.', 'woocommerce-bookings' ),
													'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
												),
											),
											'required'   => array( 'start', 'end' ),
										),
									),
								),
								'required'   => array( 'id', 'time_slots' ),
							),
						),
						'date_overrides'  => array(
							'description' => __( 'Date overrides with time slots for each date.', 'woocommerce-bookings' ),
							'type'        => 'array',
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'id'         => array(
										'type'        => 'string',
										'description' => __( 'Date in YYYY-MM-DD format.', 'woocommerce-bookings' ),
										'pattern'     => '^[0-9]{4}-[0-9]{2}-[0-9]{2}$',
									),
									'time_slots' => array(
										'type'  => 'array',
										'items' => array(
											'type'       => 'object',
											'properties' => array(
												'start' => array(
													'type' => 'string',
													'description' => __( 'Start time in HH:MM format.', 'woocommerce-bookings' ),
													'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
												),
												'end'   => array(
													'type' => 'string',
													'description' => __( 'End time in HH:MM format.', 'woocommerce-bookings' ),
													'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
												),
											),
											'required'   => array( 'start', 'end' ),
										),
									),
								),
								'required'   => array( 'id', 'time_slots' ),
							),
						),
					),
				),
				'product_booking_ids' => array(
					'description' => __( 'IDs of booking products this team member is assigned to.', 'woocommerce-bookings' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Validate availability payload.
	 *
	 * @since 3.2.0
	 *
	 * @param array $availability Availability payload.
	 * @return WP_Error|true
	 */
	private function validate_availability_request( $availability ) {
		if ( ! is_array( $availability ) ) {
			return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_availability', __( 'Availability must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
		}

		if ( isset( $availability['weekly_schedule'] ) ) {
			if ( ! is_array( $availability['weekly_schedule'] ) ) {
				return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_weekly_schedule', __( 'Weekly schedule must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
			}

			foreach ( $availability['weekly_schedule'] as $day_key => $day_value ) {
				$day_index  = absint( $day_value['id'] );
				$time_slots = isset( $day_value['time_slots'] ) ? $day_value['time_slots'] : array();

				if ( $day_index < 1 || $day_index > 7 ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_weekly_schedule_day', __( 'Weekly schedule day must be between 1 and 7.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}

				if ( ! is_array( $time_slots ) ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_time_slots', __( 'Time slots must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}

				$time_validation = WC_Bookings_Availability_Utils::validate_time_slots( $time_slots );
				if ( is_wp_error( $time_validation ) ) {
					return $time_validation;
				}
			}
		}

		if ( isset( $availability['date_overrides'] ) ) {
			if ( ! is_array( $availability['date_overrides'] ) ) {
				return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_date_overrides', __( 'Date overrides must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
			}

			foreach ( $availability['date_overrides'] as $override ) {
				if ( ! is_array( $override ) || ! isset( $override['id'] ) || ! isset( $override['time_slots'] ) ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_date_override', __( 'Each date override must include an id and time slots.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}

				if ( ! $this->is_valid_date( $override['id'] ) ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_date', __( 'Date overrides must use YYYY-MM-DD format.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}

				if ( ! is_array( $override['time_slots'] ) ) {
					return new WP_Error( 'woocommerce_bookings_resources_rest_invalid_time_slots', __( 'Time slots must be an array.', 'woocommerce-bookings' ), array( 'status' => 400 ) );
				}

				$time_validation = WC_Bookings_Availability_Utils::validate_time_slots( $override['time_slots'] );
				if ( is_wp_error( $time_validation ) ) {
					return $time_validation;
				}
			}
		}

		return true;
	}

	/**
	 * Convert availability payload to database-ready rules.
	 *
	 * @since 3.2.0
	 *
	 * @param array $availability Availability payload.
	 * @return array
	 */
	private function map_availability_for_database( $availability ) {
		$availability_rules = array();
		$weekly_schedule    = array();
		if ( isset( $availability['weekly_schedule'] ) && is_array( $availability['weekly_schedule'] ) ) {
			foreach ( $availability['weekly_schedule'] as $day_key => $day_value ) {
				$day_index = absint( $day_value['id'] );
				if ( $day_index < 1 || $day_index > 7 ) {
					continue;
				}
				$weekly_schedule[ $day_index ] = isset( $day_value['time_slots'] ) && is_array( $day_value['time_slots'] ) ? $day_value['time_slots'] : array();
			}
		}
		$date_overrides = isset( $availability['date_overrides'] ) && is_array( $availability['date_overrides'] ) ? $availability['date_overrides'] : array();

		if ( ! empty( $weekly_schedule ) ) {
			// Team member weekly: no=40, yes=50 (global weekly: no=30, yes=70).
			$availability_rules = array_merge( $availability_rules, WC_Bookings_Availability_Utils::map_weekly_availability_rules( $weekly_schedule, 40, 50 ) );
		}

		if ( ! empty( $date_overrides ) ) {
			// Team member date overrides are "blocked off time": the provided
			// time_slots are when the member is NOT available.  Store each slot
			// directly as bookable=no without gap-filling.  An empty time_slots
			// array means "unavailable all day".
			foreach ( $date_overrides as $override ) {
				if ( ! is_array( $override ) || ! isset( $override['id'] ) ) {
					continue;
				}

				$date       = $override['id'];
				$time_slots = isset( $override['time_slots'] ) && is_array( $override['time_slots'] ) ? $override['time_slots'] : array();

				if ( empty( $time_slots ) ) {
					// All-day blocked.
					$availability_rules[] = array(
						'type'      => 'custom:daterange',
						'rule_type' => 'date_override',
						'priority'  => 20,
						'bookable'  => 'no',
						'from'      => '00:00',
						'to'        => '23:59',
						'from_date' => $date,
						'to_date'   => $date,
					);
				} else {
					foreach ( $time_slots as $slot ) {
						if ( ! is_array( $slot ) || ! isset( $slot['start'], $slot['end'] ) ) {
							continue;
						}
						$availability_rules[] = array(
							'type'      => 'custom:daterange',
							'rule_type' => 'date_override',
							'priority'  => 20,
							'bookable'  => 'no',
							'from'      => $slot['start'],
							'to'        => $slot['end'],
							'from_date' => $date,
							'to_date'   => $date,
						);
					}
				}
			}
		}

		return $availability_rules;
	}

	/**
	 * Format availability rules for API response.
	 *
	 * @since 3.2.0
	 *
	 * @param array $availability_rules Availability rules from storage.
	 * @return array
	 */
	private function format_availability_for_response( $availability_rules ) {
		$weekly_schedule = array(
			array(
				'id'         => 1,
				'time_slots' => array(),
			),
			array(
				'id'         => 2,
				'time_slots' => array(),
			),
			array(
				'id'         => 3,
				'time_slots' => array(),
			),
			array(
				'id'         => 4,
				'time_slots' => array(),
			),
			array(
				'id'         => 5,
				'time_slots' => array(),
			),
			array(
				'id'         => 6,
				'time_slots' => array(),
			),
			array(
				'id'         => 7,
				'time_slots' => array(),
			),
		);
		$date_overrides  = array();

		if ( ! is_array( $availability_rules ) ) {
			return array(
				'weekly_schedule' => $weekly_schedule,
				'date_overrides'  => $date_overrides,
			);
		}

		foreach ( $availability_rules as $availability_rule ) {
			if ( ! is_array( $availability_rule ) ) {
				continue;
			}

			$bookable = isset( $availability_rule['bookable'] ) ? $availability_rule['bookable'] : '';
			$type     = isset( $availability_rule['type'] ) ? $availability_rule['type'] : '';
			$from     = isset( $availability_rule['from'] ) ? $availability_rule['from'] : '';
			$to       = isset( $availability_rule['to'] ) ? $availability_rule['to'] : '';

			// Weekly schedule: only available (bookable=yes) slots.
			if ( str_starts_with( $type, 'time:' ) ) {
				if ( 'yes' === $bookable && ! empty( $from ) && ! empty( $to ) ) {
					$day_index = absint( str_replace( 'time:', '', $type ) );
					if ( $day_index >= 1 && $day_index <= 7 ) {
						$weekly_schedule[ $day_index - 1 ]['time_slots'][] = array(
							'start' => $from,
							'end'   => $to,
						);
					}
				}
				continue;
			}

			// Date overrides (blocked off time): stored as bookable=no.
			if ( 'custom:daterange' === $type && 'no' === $bookable ) {
				$date = isset( $availability_rule['from_date'] ) ? $availability_rule['from_date'] : '';
				if ( empty( $date ) ) {
					$date = isset( $availability_rule['to_date'] ) ? $availability_rule['to_date'] : '';
				}
				if ( empty( $date ) ) {
					continue;
				}

				if ( ! isset( $date_overrides[ $date ] ) ) {
					$date_overrides[ $date ] = array(
						'id'         => $date,
						'time_slots' => array(),
					);
				}

				// All-day blocked (00:00–23:59) is represented as empty time_slots.
				if ( '00:00' === $from && '23:59' === $to ) {
					$date_overrides[ $date ]['time_slots'] = array();
				} elseif ( ! empty( $from ) && ! empty( $to ) ) {
					$date_overrides[ $date ]['time_slots'][] = array(
						'start' => $from,
						'end'   => $to,
					);
				}
			}
		}

		foreach ( $weekly_schedule as $index => $day_data ) {
			usort(
				$day_data['time_slots'],
				function ( $left, $right ) {
					return strcmp( $left['start'], $right['start'] );
				}
			);
			$weekly_schedule[ $index ] = $day_data;
		}

		if ( ! empty( $date_overrides ) ) {
			foreach ( $date_overrides as $date_key => $override ) {
				usort(
					$override['time_slots'],
					function ( $left, $right ) {
						return strcmp( $left['start'], $right['start'] );
					}
				);
				$date_overrides[ $date_key ] = $override;
			}

			ksort( $date_overrides );
		}

		return array(
			'weekly_schedule' => $weekly_schedule,
			'date_overrides'  => array_values( $date_overrides ),
		);
	}

	/**
	 * Validate date string in YYYY-MM-DD format.
	 *
	 * @since 3.2.0
	 *
	 * @param string $date Date string.
	 * @return bool
	 */
	private function is_valid_date( $date ) {
		if ( ! is_string( $date ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return false;
		}

		list( $year, $month, $day ) = array_map( 'intval', explode( '-', $date ) );
		return wp_checkdate( $month, $day, $year, $date );
	}

	/**
	 * Get the collection params.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['include_status'] = array(
			'description'       => __( 'Limit result set to team members with any of the specified statuses.', 'woocommerce-bookings' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
				'enum' => WC_Product_Booking_Resource::get_allowed_statuses(),
			),
			'default'           => array( 'publish' ),
			'sanitize_callback' => 'wp_parse_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['exclude_status'] = array(
			'description'       => __( 'Exclude team members with any of the specified statuses.', 'woocommerce-bookings' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
				'enum' => WC_Product_Booking_Resource::get_allowed_statuses(),
			),
			'sanitize_callback' => 'wp_parse_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get product IDs for a resource.
	 *
	 * @param int $resource_id Resource ID.
	 *
	 * @return int[]
	 */
	private function get_product_booking_ids_for_resource( $resource_id ) {
		global $wpdb;

		return wp_parse_id_list(
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT product_id
					FROM {$wpdb->prefix}wc_booking_relationships
					WHERE resource_id = %d
					ORDER BY sort_order ASC",
					$resource_id
				)
			)
		);
	}

	/**
	 * Sync product relationships for a resource.
	 *
	 * @param int   $resource_id Resource ID.
	 * @param int[] $new_product_booking_ids Product IDs to keep.
	 */
	private function sync_product_booking_ids( $resource_id, $new_product_booking_ids ) {
		$current_product_booking_ids = $this->get_product_booking_ids_for_resource( $resource_id );
		$to_add                      = array_diff( $new_product_booking_ids, $current_product_booking_ids );
		$to_remove                   = array_diff( $current_product_booking_ids, $new_product_booking_ids );

		foreach ( $to_add as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product instanceof WC_Product_Booking ) {
				$product->add_resource_id( $resource_id );
				$product->set_has_resources( true );
				$product->save();
			}
		}

		foreach ( $to_remove as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product instanceof WC_Product_Booking ) {
				$resource_ids = array_diff( $product->get_resource_ids(), array( $resource_id ) );
				$product->set_resource_ids( $resource_ids );
				$product->set_has_resources( ! empty( $resource_ids ) );
				$product->save();
			}
		}
	}

	/**
	 * Get only the weekly rules from an availability array.
	 *
	 * @param array $availability Availability rules.
	 * @return array Weekly rules, re-indexed.
	 */
	private function get_weekly_rules( array $availability ): array {
		return array_values(
			array_filter(
				$availability,
				function ( $rule ) {
					return 'weekly' === $rule['rule_type'];
				}
			)
		);
	}

	/**
	 * Get only the date override rules from an availability array.
	 *
	 * @param array $availability Availability rules.
	 * @return array Date override rules, re-indexed.
	 */
	private function get_date_override_rules( array $availability ): array {
		return array_values(
			array_filter(
				$availability,
				function ( $rule ) {
					return 'weekly' !== $rule['rule_type'];
				}
			)
		);
	}
}
