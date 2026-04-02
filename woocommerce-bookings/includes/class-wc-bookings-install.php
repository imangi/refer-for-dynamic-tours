<?php
/**
 * File responsible for doing migrations.
 *
 * @package  Installation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Installation/Migration Class.
 *
 * Handles the activation/installation of the plugin.
 *
 * @version  1.13.0
 */
class WC_Bookings_Install {

	/**
	 * Current plugin version.
	 *
	 * @var string
	 */
	private static $current_version;

	/**
	 * Current plugin db version.
	 *
	 * @var string
	 */
	private static $current_db_version;

	/**
	 * Get capabilities for WooCommerce Bookings - these are assigned to admin/shop manager during installation or reset.
	 *
	 * @return array
	 */
	public static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_bookings_settings',
			'manage_bookings_timezones',
			'manage_bookings_connection',
		);

		$resource_capability_types = wc_booking_get_product_resource_post_types();
		$capability_types          = array_merge( $resource_capability_types, array( 'bookable_person', 'wc_booking' ) );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type.
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
			);
		}

		$capabilities['global_availability'] = array(
			'edit_global_availability',
			'read_global_availability',
			'delete_global_availability',
			'edit_global_availabilities',
			'delete_global_availabilities',
		);
		return $capabilities;
	}

	/**
	 * Initialize.
	 *
	 * @since 1.13.0
	 */
	public static function init() {
		self::$current_version    = get_option( 'wc_bookings_version' );
		self::$current_db_version = get_option( 'wc_bookings_db_version' );

		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_install' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_update' ) );
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since 3.2.0
	 */
	public static function maybe_install() {
		if ( self::can_install() && self::must_install() ) {
			set_transient( 'wc_bookings_installing', 'yes', 10 );
			self::install();
			delete_transient( 'wc_bookings_installing' );
		}
	}

	/**
	 * Run the updater if triggered.
	 *
	 * @since 3.2.0
	 */
	public static function maybe_update() {
		if ( self::can_update() && self::must_update() ) {
			set_transient( 'wc_bookings_installing', 'yes', 10 );
			self::update();
			delete_transient( 'wc_bookings_installing' );
		}
	}
	/**
	 * Installation possible?
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	private static function can_install() {
		return ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' ) && ! self::is_installing();
	}

	/**
	 * DB update possible?
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	private static function can_update() {
		return ( self::can_install() && current_user_can( 'manage_woocommerce' ) ) && version_compare( self::$current_db_version, WC_BOOKINGS_DB_VERSION, '<' );
	}

	/**
	 * Is currently installing?
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	private static function is_installing() {
		return 'yes' === get_transient( 'wc_bookings_installing' );
	}

	/**
	 * Must install?
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	private static function must_install() {
		if ( is_null( self::$current_version ) ) {
			return true;
		}

		return version_compare( self::$current_version, WC_BOOKINGS_VERSION, '<' );
	}

	/**
	 * Must update?
	 *
	 * @since 3.2.0
	 *
	 * @return boolean
	 */
	private static function must_update() {
		return version_compare( self::$current_db_version, WC_BOOKINGS_DB_VERSION, '<' );
	}

	/**
	 * Install the plugin.
	 *
	 * Handles initial plugin installation including database setup,
	 * product type registration, and capability assignment.
	 *
	 * @since 3.2.0
	 */
	private static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		self::create_tables();
		self::register_product_type();
		self::setup_capabilities();

		// Update version.
		self::update_plugin_version();
	}

	/**
	 * Update the plugin.
	 *
	 * Handles plugin updates including version-specific migrations,
	 * database updates, and cache clearing.
	 *
	 * @since 3.2.0
	 */
	private static function update() {
		// Flush transients on update.
		WC_Bookings_Cache::clear_cache();

		self::run_version_migrations();
		self::update_db_version();

		/**
		 * Action when the plugin is updated.
		 *
		 * @since 3.2.0
		 */
		do_action( 'wc_bookings_updated' );
	}

	/**
	 * Create or update database tables.
	 *
	 * Uses dbDelta to create tables on install or update them if schema changes.
	 *
	 * @since 3.2.0
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta(
			"
	CREATE TABLE {$wpdb->prefix}wc_booking_relationships (
	ID bigint(20) unsigned NOT NULL auto_increment,
	product_id bigint(20) unsigned NOT NULL,
	resource_id bigint(20) unsigned NOT NULL,
	sort_order bigint(20) unsigned NOT NULL default 0,
	PRIMARY KEY  (ID),
	KEY product_id (product_id),
	KEY resource_id (resource_id)
	) $collate;
	CREATE TABLE {$wpdb->prefix}wc_bookings_availability (
	ID bigint(20) unsigned NOT NULL auto_increment,
	gcal_event_id varchar(100) NOT NULL,
	title varchar(255) NULL,
	range_type varchar(60) NOT NULL,
	from_date varchar(60) NOT NULL,
	to_date varchar(60) NOT NULL,
	from_range varchar(60) NULL,
	to_range varchar(60) NULL,
	bookable varchar(5) NOT NULL default 'yes',
	priority int(2) NOT NULL default 10,
	ordering int(2) NOT NULL default 0,
	date_created datetime NULL default NULL,
	date_modified datetime NULL default NULL,
    rrule text NULL default NULL,
	rule_type varchar(20) NULL default NULL,
	PRIMARY KEY  (ID),
	KEY gcal_event_id (gcal_event_id)
	) $collate;
	CREATE TABLE {$wpdb->prefix}wc_bookings_availabilitymeta (
	  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
	  bookings_availability_id BIGINT UNSIGNED NOT NULL,
	  meta_key varchar(255) NULL,
	  meta_value longtext NULL,
	  PRIMARY KEY  (meta_id),
	  KEY bookings_availability_id (bookings_availability_id),
	  KEY meta_key (meta_key(32))
	) $collate;
			"
		);
	}

	/**
	 * Register the booking product type.
	 *
	 * Ensures the 'booking' product type term exists in the product_type taxonomy.
	 *
	 * @since 3.2.0
	 */
	private static function register_product_type() {
		if ( ! get_term_by( 'slug', sanitize_title( 'booking' ), 'product_type' ) ) {
			wp_insert_term( 'booking', 'product_type' );
		}
	}

	/**
	 * Setup user capabilities.
	 *
	 * Assigns booking-related capabilities to shop_manager and administrator roles,
	 * and removes deprecated capabilities.
	 *
	 * @since 3.2.0
	 */
	public static function setup_capabilities() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		if ( is_object( $wp_roles ) ) {
			foreach ( self::get_core_capabilities() as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'shop_manager', $cap );
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
			// Remove deprecated manage_bookings capability.
			$wp_roles->remove_cap( 'shop_manager', 'manage_bookings' );
			$wp_roles->remove_cap( 'administrator', 'manage_bookings' );
		}
	}

	/**
	 * Run version-specific migrations.
	 *
	 * Executes data migrations based on the previously installed version.
	 *
	 * @since 3.2.0
	 */
	private static function run_version_migrations() {
		global $wpdb;

		// Version-specific migrations.
		if ( version_compare( self::$current_version, '1.13.0', '<' ) ) {
			self::migration_1_13_0();
		}

		if ( version_compare( self::$current_version, '1.13.2', '<' ) ) {
			// Keep old option data but disable autoload since it won't be used anymore.
			$wpdb->query(
				"UPDATE $wpdb->options SET autoload = 'no' WHERE option_name IN ('wc_global_booking_availability','woocommerce_bookings_tz_calculation','woocommerce_bookings_timezone_conversion','woocommerce_bookings_client_firstday')"
			);
		}

		// Data updates.
		if ( version_compare( self::$current_version, '1.3', '<' ) ) {
			$bookings = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN ( '_booking_start', '_booking_end' );" );
			foreach ( $bookings as $booking ) {
				if ( ctype_digit( $booking->meta_value ) && $booking->meta_value <= 2147483647 ) {
					$new_date = date( 'YmdHis', $booking->meta_value ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					update_post_meta( $booking->post_id, $booking->meta_key, $new_date );
				}
			}
		}

		if ( version_compare( self::$current_version, '1.4', '<' ) ) {
			$resources = $wpdb->get_results( "SELECT ID, post_parent FROM $wpdb->posts WHERE post_type = 'bookable_resource' AND post_parent > 0;" );
			foreach ( $resources as $resource ) {
				$wpdb->insert(
					$wpdb->prefix . 'wc_booking_relationships',
					array(
						'product_id'  => $resource->post_parent,
						'resource_id' => $resource->ID,
						'sort_order'  => 1,
					)
				);
				if ( $wpdb->insert_id ) {
					$wpdb->update(
						$wpdb->posts,
						array(
							'post_parent' => 0,
						),
						array(
							'ID' => $resource->ID,
						)
					);
					$cost         = get_post_meta( $resource->ID, 'cost', true );
					$parent_costs = get_post_meta( $resource->post_parent, '_resource_base_costs', true );
					if ( ! $parent_costs ) {
						$parent_costs = array();
					}
					$parent_costs[ $resource->ID ] = $cost;
					update_post_meta( $resource->post_parent, '_resource_base_costs', $parent_costs );
				}
			}
		}

		if ( version_compare( self::$current_version, '1.5', '<' ) ) {
			$wpdb->query(
				"
				UPDATE {$wpdb->posts} as posts
				SET posts.post_status = 'pending-confirmation'
				WHERE posts.post_type = 'wc_booking'
				AND posts.post_status = 'pending';
				"
			);
		}

		if ( version_compare( self::$current_version, '1.10.3', '<' ) ) {

			$booking_products = WC_Product_Booking_Data_Store_CPT::get_bookable_product_ids();

			// Update all bookings to match the proper price.
			foreach ( $booking_products as $product_id ) {
				$price = get_post_meta( $product_id, '_price', true );

				if ( ! empty( $price ) ) {
					continue;
				}

				$new_price = WC_Bookings_Cost_Calculation::calculated_base_cost( get_wc_product_booking( $product_id ) );

				update_post_meta( $product_id, '_price', $new_price );
			}
		}

		if ( version_compare( self::$current_version, '1.10.9', '<' ) ) {
			$booking_products = WC_Product_Booking_Data_Store_CPT::get_bookable_product_ids();

			// Update all bookings to match the proper base cost.
			foreach ( $booking_products as $product_id ) {
				$base_cost = get_post_meta( $product_id, '_wc_booking_base_cost', true );

				if ( empty( $base_cost ) ) {
					continue;
				}

				update_post_meta( $product_id, '_wc_booking_block_cost', $base_cost );
				delete_post_meta( $product_id, '_wc_booking_base_cost' );
			}
		}
	}

	/**
	 * Updates the plugin version in db.
	 *
	 * @since 1.13.0
	 */
	private static function update_plugin_version() {
		delete_option( 'wc_bookings_version' );
		add_option( 'wc_bookings_version', WC_BOOKINGS_VERSION );
	}

	/**
	 * Updates the plugin db version in db.
	 *
	 * @since 1.13.0
	 */
	private static function update_db_version() {
		delete_option( 'wc_bookings_db_version' );
		add_option( 'wc_bookings_db_version', WC_BOOKINGS_DB_VERSION );
	}

	/**
	 * Migrate global availabiltity from options table
	 * to custom availability table.
	 *
	 * @since 1.13.0
	 */
	private static function migration_1_13_0() {
		global $wpdb;

		// Get global availability settings and migrate.
		$global_availability = get_option( 'wc_global_booking_availability', array() );

		if ( ! empty( $global_availability ) ) {
			$index = 0;

			foreach ( $global_availability as $rule ) {
				$type       = ! empty( $rule['type'] ) ? $rule['type'] : '';
				$from_range = ! empty( $rule['from'] ) ? $rule['from'] : '';
				$to_range   = ! empty( $rule['to'] ) ? $rule['to'] : '';
				$from_date  = ! empty( $rule['from_date'] ) ? $rule['from_date'] : '';
				$to_date    = ! empty( $rule['to_date'] ) ? $rule['to_date'] : '';
				$bookable   = ! empty( $rule['bookable'] ) ? $rule['bookable'] : '';
				$priority   = ! empty( $rule['priority'] ) ? $rule['priority'] : '';

				$wpdb->insert(
					$wpdb->prefix . 'wc_bookings_availability',
					array(
						'gcal_event_id' => '',
						'title'         => '',
						'range_type'    => $type,
						'from_range'    => $from_range,
						'to_range'      => $to_range,
						'from_date'     => $from_date,
						'to_date'       => $to_date,
						'bookable'      => $bookable,
						'priority'      => $priority,
						'ordering'      => $index,
					)
				);

				$index++;
			}
		}

		// Migrate timezone settings from separate options to WC_Bookings_Timezone_Settings.
		if ( ! WC_Bookings_Timezone_Settings::exists_in_db() ) {
			$use_server_timezone_for_actions = get_option( 'woocommerce_bookings_tz_calculation', 'no' );
			$use_client_timezone             = get_option( 'woocommerce_bookings_timezone_conversion', 'no' );
			$use_client_firstday             = get_option( 'woocommerce_bookings_client_firstday', 'no' );

			update_option(
				'wc_bookings_timezone_settings',
				array(
					'use_server_timezone_for_actions' => $use_server_timezone_for_actions,
					'use_client_timezone'             => $use_client_timezone,
					'use_client_firstday'             => $use_client_firstday,
				)
			);
		}

		// Migrate Google Access token to new format.
		$access_token = get_transient( 'wc_bookings_gcalendar_access_token' );
		if ( is_string( $access_token ) ) {
			set_transient(
				'wc_bookings_gcalendar_access_token',
				array(
					'access_token' => WC_Bookings_Encryption::instance()->encrypt( $access_token ),
					'expires_in'   => 3600,
				),
				HOUR_IN_SECONDS
			);
		}
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param  mixed $links Plugin Row Meta.
	 * @param  mixed $file  Plugin Base file.
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( WC_BOOKINGS_MAIN_FILE ) === $file ) {
			/**
			 * Filter the plugin documentation link.
			 *
			 * @since 1.13.0
			 */
			$doc_url = apply_filters( 'woocommerce_bookings_docs_url', 'https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-extensions/woocommerce-bookings/' );

			/**
			 * Filter the support link.
			 *
			 * @since 1.13.0
			 */
			$support_url = apply_filters( 'woocommerce_bookings_support_url', 'https://woocommerce.com/my-account/tickets/' );

			$row_meta = array(
				'docs'    => '<a href="' . esc_url( $doc_url ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-bookings' ) ) . '">' . __( 'Docs', 'woocommerce-bookings' ) . '</a>',
				'support' => '<a href="' . esc_url( $support_url ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support', 'woocommerce-bookings' ) ) . '">' . __( 'Premium Support', 'woocommerce-bookings' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}
