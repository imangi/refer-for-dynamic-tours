<?php
// phpcs:ignoreFile
/**
 * REST API for product objects.
 *
 * Handles requests to the WooCommerce Product REST API.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom REST API product fields.
 *
 * @class WC_Bookings_Product_Rest_API
 */
class WC_Bookings_Product_Rest_API {

	/**
	 * Resource/team member IDs to filter products by for the current request.
	 *
	 * Populated by filter_products_query() from the booking_resource_ids param.
	 * Used instead of a WP_Query arg because custom args are stripped by
	 * prepare_items_query() before reaching WP_Query.
	 *
	 * @var int[]
	 */
	private $filter_resource_ids = array();

	/**
	 * Resource/team member IDs to exclude products by for the current request.
	 *
	 * Populated by filter_products_query() from the exclude_booking_resource_ids param.
	 * Used instead of a WP_Query arg because custom args are stripped by
	 * prepare_items_query() before reaching WP_Query.
	 *
	 * @var int[]
	 */
	private $filter_exclude_resource_ids = array();

	/**
	 * Custom REST API product field names.
	 *
	 * @var array
	 */
	private $product_fields = array(
		'booking_location'       => array( 'get', 'update' ),
		'booking_location_type'  => array( 'get', 'update' ),
		'booking_duration'       => array( 'get', 'update' ),
		'booking_duration_unit'  => array( 'get', 'update' ),
		'booking_buffer'         => array( 'get', 'update' ),
		'booking_cost'           => array( 'get', 'update' ),
		'booking_resources'      => array( 'get', 'update' ),
	);

	/**
	 * Setup.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_product_fields' ), 0 );
		add_filter( 'rest_product_collection_params', array( $this, 'add_collection_params' ) );
		add_filter( 'woocommerce_rest_product_object_query', array( $this, 'filter_products_query' ), 20, 2 );
	}

	/**
	 * Register custom REST API fields for product requests.
	 */
	public function register_product_fields() {

		foreach ( $this->product_fields as $field_name => $field_supports ) {

			$args = array(
				'schema' => $this->get_product_field_schema( $field_name ),
			);

			if ( in_array( 'get', $field_supports, true ) ) {
				$args['get_callback'] = array( $this, 'get_product_field_value' );
			}
			if ( in_array( 'update', $field_supports, true ) ) {
				$args['update_callback'] = array( $this, 'update_product_field_value' );
			}

			register_rest_field( 'product', $field_name, $args );
		}
	}

	/**
	 * Gets extended schema properties for products.
	 *
	 * @return array
	 */
	public function get_extended_product_schema() {

		return array(
			'booking_location'      => array(
				'description' => __( 'Booking location. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
			),
			'booking_location_type' => array(
				'description' => __( 'Booking location type. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array( 'in-person', 'online' ),
			),
			'booking_duration'      => array(
				'description' => __( 'Duration. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'booking_duration_unit' => array(
				'description' => __( 'Duration unit. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array( 'minute', 'hour', 'day', 'night', 'month' ),
			),
			'booking_buffer'        => array(
				'description' => __( 'Buffer. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
			),
			'booking_cost'          => array(
				'description' => __( 'Cost. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'number',
				'context'     => array( 'view', 'edit' ),
			),
			'booking_resources'     => array(
				'description' => __( 'All resource IDs (resources and team members) assigned to this product. Applicable for booking-type products only.', 'woocommerce-bookings' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type' => 'integer',
				),
			),
		);
	}

	/**
	 * Gets schema properties for product fields.
	 *
	 * @param  string $field_name Field name.
	 * @return array
	 */
	public function get_product_field_schema( $field_name ) {

		$extended_schema = $this->get_extended_product_schema();
		$field_schema    = isset( $extended_schema[ $field_name ] ) ? $extended_schema[ $field_name ] : null;

		return $field_schema;
	}

	/**
	 * Gets values for product fields.
	 *
	 * @param  array           $response The response object.
	 * @param  string          $field_name Field name.
	 * @param  WP_REST_Request $request The request object.
	 * @return mixed
	 */
	public function get_product_field_value( $response, $field_name, $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$data = null;

		if ( isset( $response['id'] ) ) {
			$product = wc_get_product( $response['id'] );
			$data    = $this->get_product_field( $field_name, $product );
		}

		return $data;
	}

	/**
	 * Updates values for product fields.
	 *
	 * @param  mixed  $field_value  Field value.
	 * @param  mixed  $response     The response object.
	 * @param  string $field_name   Field name.
	 * @return boolean|WP_Error True on success, WP_Error on validation failure.
	 */
	public function update_product_field_value( $field_value, $response, $field_name ) {

		$product_id = false;

		if ( $response instanceof WP_Post ) {
			$product_id = absint( $response->ID );
			$product    = wc_get_product( $product_id );
		} elseif ( $response instanceof WC_Product ) {
			$product_id = $response->get_id();
			$product    = $response;
		}

		if ( ! $product_id ) {
			return true;
		}

		if ( ! is_wc_booking_product( $product ) ) {
			// Silent fail.
			return true;
		}

		if ( 'booking_location' === $field_name ) {
			$product->set_booking_location( $field_value );
			$product->save();
		} elseif ( 'booking_location_type' === $field_name ) {
			$product->set_booking_location_type( $field_value );
			$product->save();
		} elseif ( 'booking_duration' === $field_name ) {
			$product->set_duration( $field_value );
			$product->save();
		} elseif ( 'booking_duration_unit' === $field_name ) {
			$product->set_duration_unit( $field_value );
			$product->save();
		} elseif ( 'booking_buffer' === $field_name ) {
			$product->set_buffer_period( $field_value );
			$product->save();
		} elseif ( 'booking_cost' === $field_name ) {
			$product->set_cost( $field_value );
			$product->save();
		} elseif ( 'booking_resources' === $field_name ) {
			// Validate that resources exist.
			if ( ! empty( $field_value ) && is_array( $field_value ) ) {
				$invalid_ids = array();

				foreach ( $field_value as $resource_id ) {
					if ( ! get_wc_product_booking_resource( absint( $resource_id ) ) ) {
						$invalid_ids[] = $resource_id;
					}
				}

				if ( ! empty( $invalid_ids ) ) {
					throw new WC_REST_Exception(
						'woocommerce_rest_invalid_booking_resources',
						esc_html( sprintf(
							/* translators: %s: comma-separated list of invalid resource IDs */
							__( 'Invalid booking resource IDs: %s', 'woocommerce-bookings' ),
							implode( ', ', $invalid_ids )
						) ),
						400
					);
				}
			}

			$product->set_resource_ids( $field_value );

			if ( empty( $field_value ) ) {
				$product->set_has_resources( false );
			} else {
				$product->set_has_resources( true );
			}

			$product->save();
		}

		return true;
	}

	/**
	 * Gets product data.
	 *
	 * @param  string     $key Field key.
	 * @param  WC_Product $product Product object.
	 * @return mixed
	 */
	public function get_product_field( $key, $product ) {

		if ( ! is_wc_booking_product( $product ) ) {
			return null;
		}

		$value = null;
		switch ( $key ) {

			case 'booking_location':
					$value = $product->get_booking_location();
				break;
			case 'booking_location_type':
					$value = $product->get_booking_location_type();
				break;
			case 'booking_duration':
					$value = $product->get_duration();
				break;
			case 'booking_duration_unit':
					$value = $product->get_duration_unit();
				break;
			case 'booking_buffer':
					$value = $product->get_buffer_period();
				break;
			case 'booking_cost':
					$value = $product->get_cost();
				break;
			case 'booking_resources':
					$value = $product->get_resource_ids();
				break;
		}
		return $value;
	}

	/**
	 * Adds collection params to the /wc/v4/products endpoint.
	 *
	 * Registers:
	 * - booking_resource_ids:           filter to products assigned to any of the given resource IDs.
	 * - exclude_booking_resource_ids:   exclude products assigned to any of the given resource IDs.
	 *
	 * When WC_BOOKINGS_EXPERIMENTAL_ENABLED is true, also registers:
	 * - booking_location_type:          filter by in-person or online location type.
	 * - exclude_booking_location_type:  exclude products with a specific location type.
	 *
	 * @param array $params Existing collection params.
	 * @return array
	 */
	public function add_collection_params( $params ) {
		if ( defined( 'WC_BOOKINGS_EXPERIMENTAL_ENABLED' ) && WC_BOOKINGS_EXPERIMENTAL_ENABLED ) {
			$params['booking_location_type'] = array(
				'description'       => __( 'Limit result set to products with a specific booking location type.', 'woocommerce-bookings' ),
				'type'              => 'string',
				'enum'              => array( 'in-person', 'online' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			);
			$params['exclude_booking_location_type'] = array(
				'description'       => __( 'Exclude products with a specific booking location type.', 'woocommerce-bookings' ),
				'type'              => 'string',
				'enum'              => array( 'in-person', 'online' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			);
		}
		$params['booking_resource_ids'] = array(
			'description'       => __( 'Limit result set to products assigned to specific resources or team members (by ID).', 'woocommerce-bookings' ),
			'type'              => 'array',
			'items'             => array( 'type' => 'integer' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['exclude_booking_resource_ids'] = array(
			'description'       => __( 'Exclude products assigned to specific resources or team members (by ID).', 'woocommerce-bookings' ),
			'type'              => 'array',
			'items'             => array( 'type' => 'integer' ),
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

	/**
	 * Applies booking filter params to the WP_Query args.
	 *
	 * Handles: booking_location_type, exclude_booking_location_type,
	 *          booking_resource_ids, exclude_booking_resource_ids.
	 *
	 * Hooked into woocommerce_rest_product_object_query at priority 20 so it runs
	 * after other Bookings hooks (e.g. woocommerce_bookings_next_apply_service_filters)
	 * that may have already added incorrect meta_query entries for these params.
	 * We strip those entries and replace them with correct logic.
	 *
	 * booking_location_type / exclude_booking_location_type:
	 *   'in-person' is never stored explicitly in the DB (stored as '' or no row),
	 *   so direct meta_value matches from earlier hooks break it. We use a NOT EXISTS
	 *   subquery via posts_where instead (@see add_location_type_where).
	 *
	 * booking_resource_ids / exclude_booking_resource_ids:
	 *   Relationships are stored in wc_booking_relationships, not postmeta.
	 *   Earlier hooks query a non-existent _booking_resources meta key (both as flat
	 *   NOT LIKE clauses and as nested OR arrays for multi-ID includes), which always
	 *   returns empty. We use EXISTS / NOT EXISTS subqueries via posts_where instead
	 *   (@see add_resource_ids_where).
	 *
	 * @param array           $args    WP_Query args.
	 * @param WP_REST_Request $request REST request.
	 * @return array
	 */
	public function filter_products_query( $args, $request ) {
		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		// --- booking_location_type / exclude_booking_location_type (experimental) ---

		if ( defined( 'WC_BOOKINGS_EXPERIMENTAL_ENABLED' ) && WC_BOOKINGS_EXPERIMENTAL_ENABLED ) {
			// Remove all _wc_booking_booking_location_type meta_query entries added by
			// earlier hooks. Those entries use direct '=' / '!=' comparisons which break
			// for 'in-person' because that value is never stored in the DB.
			$args['meta_query'] = array_filter(
				$args['meta_query'],
				function( $clause ) {
					return ! ( is_array( $clause )
						&& isset( $clause['key'] )
						&& '_wc_booking_booking_location_type' === $clause['key'] );
				}
			);

			if ( ! empty( $request['booking_location_type'] ) ) {
				$location_type = sanitize_text_field( $request['booking_location_type'] );

				if ( 'in-person' === $location_type ) {
					// 'in-person' is the implicit default (stored as '' or missing entirely).
					// Register posts_where now so it only runs for this query; the callback
					// removes itself after firing, so it cannot affect other WP_Query instances.
					add_filter( 'posts_where', array( $this, 'add_location_type_where' ), 10, 2 );
				} else {
					$args['meta_query'][] = array(
						'key'   => '_wc_booking_booking_location_type',
						'value' => $location_type,
					);
				}
			}

			if ( ! empty( $request['exclude_booking_location_type'] ) ) {
				$exclude_type = sanitize_text_field( $request['exclude_booking_location_type'] );

				if ( 'in-person' === $exclude_type ) {
					// Excluding in-person = only show explicitly online products.
					$args['meta_query'][] = array(
						'key'   => '_wc_booking_booking_location_type',
						'value' => 'online',
					);
				} elseif ( 'online' === $exclude_type ) {
					// Excluding online = only show in-person products. Same NOT EXISTS approach
					// as booking_location_type=in-person; reuse the same callback.
					add_filter( 'posts_where', array( $this, 'add_location_type_where' ), 10, 2 );
				}
			}
		}

		// --- booking_resource_ids / exclude_booking_resource_ids ---

		// Remove _booking_resources entries added by earlier hooks. That plugin queries
		// a meta key that does not exist; resource relationships are stored in the
		// wc_booking_relationships table, not in postmeta. Two forms are produced:
		// - Flat NOT LIKE clauses (one per ID) for exclude_booking_resource_ids.
		// - A nested OR array (one clause per ID) for booking_resource_ids.
		$args['meta_query'] = array_filter(
			$args['meta_query'],
			function( $clause ) {
				if ( ! is_array( $clause ) ) {
					return true;
				}
				// Flat clause: key = '_booking_resources'.
				if ( isset( $clause['key'] ) && '_booking_resources' === $clause['key'] ) {
					return false;
				}
				// Nested OR/AND clause: strip if any sub-clause targets _booking_resources.
				if ( isset( $clause['relation'] ) ) {
					foreach ( $clause as $sub ) {
						if ( is_array( $sub ) && isset( $sub['key'] ) && '_booking_resources' === $sub['key'] ) {
							return false;
						}
					}
				}
				return true;
			}
		);

		if ( ! empty( $request['booking_resource_ids'] ) ) {
			$this->filter_resource_ids = array_map( 'absint', (array) $request['booking_resource_ids'] );
		}

		if ( ! empty( $request['exclude_booking_resource_ids'] ) ) {
			$this->filter_exclude_resource_ids = array_map( 'absint', (array) $request['exclude_booking_resource_ids'] );
		}

		if ( ! empty( $this->filter_resource_ids ) || ! empty( $this->filter_exclude_resource_ids ) ) {
			// Register posts_where now so it only runs for this query; the callback
			// removes itself after firing, so it cannot affect other WP_Query instances.
			add_filter( 'posts_where', array( $this, 'add_resource_ids_where' ), 10, 2 );
		}

		return $args;
	}

	/**
	 * Adds a WHERE condition to match 'in-person' products.
	 *
	 * Triggered by both booking_location_type=in-person and exclude_booking_location_type=online,
	 * since both reduce to the same query: products that are NOT explicitly set to 'online'.
	 *
	 * 'in-person' is the implicit default and is never stored explicitly in the DB
	 * (stored as '' or no meta row at all), so we cannot match it directly.
	 * Instead, we exclude products that have meta_value = 'online'. A NOT EXISTS
	 * subquery avoids WP_Meta_Query's INNER JOIN issues and correctly includes
	 * products with an empty value or no meta row.
	 *
	 * Registered dynamically by filter_products_query() only when needed.
	 * Removes itself immediately so it cannot fire for any subsequent WP_Query.
	 *
	 * @param string   $where    Current WHERE clause.
	 * @param WP_Query $wp_query Current query object.
	 * @return string
	 */
	public function add_location_type_where( $where, $wp_query ) {
		global $wpdb;

		remove_filter( 'posts_where', array( $this, 'add_location_type_where' ), 10 );

		$where .= $wpdb->prepare(
			" AND NOT EXISTS (
				SELECT 1 FROM {$wpdb->postmeta} AS bk_lt
				WHERE bk_lt.post_id = {$wpdb->posts}.ID
				AND bk_lt.meta_key = '_wc_booking_booking_location_type'
				AND bk_lt.meta_value = %s
			)",
			'online'
		);

		return $where;
	}

	/**
	 * Adds a WHERE condition to filter products by assigned resource/team member IDs.
	 *
	 * Resource relationships are stored in wc_booking_relationships (not postmeta),
	 * so we query that table directly. An EXISTS subquery avoids duplicate rows that
	 * a JOIN approach would require GROUP BY to resolve — a product with multiple
	 * matching resources would otherwise appear multiple times.
	 *
	 * booking_resource_ids:         EXISTS subquery — returns products assigned to ANY of the IDs.
	 * exclude_booking_resource_ids: NOT EXISTS subquery — excludes products assigned to ANY of the IDs.
	 *
	 * Registered dynamically by filter_products_query() only when needed.
	 * Removes itself immediately so it cannot fire for any subsequent WP_Query.
	 *
	 * @param string   $where    Current WHERE clause.
	 * @param WP_Query $wp_query Current query object.
	 * @return string
	 */
	public function add_resource_ids_where( $where, $wp_query ) {
		global $wpdb;

		remove_filter( 'posts_where', array( $this, 'add_resource_ids_where' ), 10 );

		if ( ! empty( $this->filter_resource_ids ) ) {
			$ids = $this->filter_resource_ids;
			$this->filter_resource_ids = array();

			$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			$where .= $wpdb->prepare(
				" AND EXISTS (
					SELECT 1 FROM {$wpdb->prefix}wc_booking_relationships AS bk_r
					WHERE bk_r.product_id = {$wpdb->posts}.ID
					AND bk_r.resource_id IN ({$placeholders})
				)",
				$ids
			);
		}

		if ( ! empty( $this->filter_exclude_resource_ids ) ) {
			$ids = $this->filter_exclude_resource_ids;
			$this->filter_exclude_resource_ids = array();

			$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
			$where .= $wpdb->prepare(
				" AND NOT EXISTS (
					SELECT 1 FROM {$wpdb->prefix}wc_booking_relationships AS bk_r_ex
					WHERE bk_r_ex.product_id = {$wpdb->posts}.ID
					AND bk_r_ex.resource_id IN ({$placeholders})
				)",
				$ids
			);
		}

		return $where;
	}

}
