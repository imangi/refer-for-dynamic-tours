<?php
/**
 * Handles email sending.
 *
 * @package WooCommerce Bookings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Booking_Email_Manager class.
 *
 * Handles email sending for bookings.
 */
class WC_Booking_Email_Manager {

	/**
	 * Email group name for WooCommerce Bookings emails.
	 *
	 * @var string
	 */
	const EMAIL_GROUP = 'wc-bookings';

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_groups', array( $this, 'register_email_groups' ) );

		add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );

		add_filter( 'woocommerce_email_attachments', array( $this, 'attach_ics_file' ), 10, 3 );

		add_filter( 'woocommerce_template_directory', array( $this, 'template_directory' ), 10, 2 );

		add_action( 'init', array( $this, 'bookings_email_actions' ) );

		// Load the email preview class.
		$this->load_bookings_email_preview();

		// Load block email editor support.
		$this->load_block_email_editor_support();
	}

	/**
	 * Include our mail templates
	 *
	 * @param  array $emails
	 * @return array
	 */
	public function init_emails( $emails ) {
		if ( ! isset( $emails['WC_Email_New_Booking'] ) ) {
			$emails['WC_Email_New_Booking'] = new WC_Email_New_Booking();
		}

		if ( ! isset( $emails['WC_Email_Booking_Reminder'] ) ) {
			$emails['WC_Email_Booking_Reminder'] = new WC_Email_Booking_Reminder();
		}

		if ( ! isset( $emails['WC_Email_Booking_Confirmed'] ) ) {
			$emails['WC_Email_Booking_Confirmed'] = new WC_Email_Booking_Confirmed();
		}

		if ( ! isset( $emails['WC_Email_Booking_Pending_Confirmation'] ) ) {
			$emails['WC_Email_Booking_Pending_Confirmation'] = new WC_Email_Booking_Pending_Confirmation();
		}

		if ( ! isset( $emails['WC_Email_Booking_Notification'] ) ) {
			$emails['WC_Email_Booking_Notification'] = new WC_Email_Booking_Notification();
		}

		if ( ! isset( $emails['WC_Email_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Booking_Cancelled'] = new WC_Email_Booking_Cancelled();
		}

		if ( ! isset( $emails['WC_Email_Admin_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Admin_Booking_Cancelled'] = new WC_Email_Admin_Booking_Cancelled();
		}

		return $emails;
	}

	/**
	 * Register email groups for WooCommerce 10.3.0+
	 *
	 * @param array $email_groups Array of email groups.
	 * @return array
	 */
	public function register_email_groups( $email_groups ) {
		if ( ! is_array( $email_groups ) ) {
			return $email_groups;
		}
		$email_groups[ self::EMAIL_GROUP ] = __( 'Bookings', 'woocommerce-bookings' );

		return $email_groups;
	}

	/**
	 * Attach the .ics files in the emails.
	 *
	 * @param  array  $attachments
	 * @param  string $email_id
	 * @param  mixed  $booking
	 *
	 * @return array
	 */
	public function attach_ics_file( $attachments, $email_id, $booking ) {
		$available = apply_filters( 'woocommerce_bookings_emails_ics', array( 'booking_confirmed', 'booking_reminder' ) );

		if ( in_array( $email_id, $available ) ) {
			$generate = new WC_Bookings_ICS_Exporter;
			$attachments[] = $generate->get_booking_ics( $booking );
		}

		return $attachments;
	}

	/**
	 * Custom template directory.
	 *
	 * @param  string $directory
	 * @param  string $template
	 *
	 * @return string
	 */
	public function template_directory( $directory, $template ) {
		if ( false !== strpos( $template, '-booking' ) ) {
			return 'woocommerce-bookings';
		}

		return $directory;
	}

	/**
	 * Bookings email actions for transactional emails.
	 *
	 * @since   1.10.5
	 * @version 1.10.5
	 */
	public function bookings_email_actions() {
		// Email Actions
		$email_actions = apply_filters( 'woocommerce_bookings_email_actions', array(
			// New & Pending Confirmation
			'woocommerce_booking_in-cart_to_paid',
			'woocommerce_booking_in-cart_to_pending-confirmation',
			'woocommerce_booking_unpaid_to_paid',
			'woocommerce_booking_unpaid_to_pending-confirmation',
			'woocommerce_booking_confirmed_to_paid',
			'woocommerce_admin_new_booking',
			'woocommerce_admin_confirmed',

			// Confirmed
			'woocommerce_booking_confirmed',

			// Pending Confirmation
			'woocommerce_booking_pending-confirmation',

			// Cancelled
			'woocommerce_booking_pending-confirmation_to_cancelled',
			'woocommerce_booking_confirmed_to_cancelled',
			'woocommerce_booking_paid_to_cancelled',
			'woocommerce_booking_unpaid_to_cancelled',
		));

		foreach ( $email_actions as $action ) {
			add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}
	}

	/**
	 * Load the bookings email preview class.
	 *
	 * @since 2.2.7
	 */
	public function load_bookings_email_preview() {
		// Add email preview class.
		require_once WC_BOOKINGS_ABSPATH . 'includes/emails/class-wc-booking-email-previews.php';

		$email_preview = new WC_Booking_Email_Previews();
		$email_preview->init();
	}

	/**
	 * Load block email editor support for WooCommerce 9.0+.
	 *
	 * @since 3.2.0
	 */
	public function load_block_email_editor_support() {
		// Block email editor requires WooCommerce 9.0+.
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '9.0', '<' ) ) {
			return;
		}

		// Register booking emails for block editor.
		add_filter( 'woocommerce_transactional_emails_for_block_editor', array( $this, 'register_block_editor_emails' ) );

		// Add booking context data for email personalization.
		add_filter( 'woocommerce_email_editor_integration_personalizer_context_data', array( $this, 'add_booking_context_data' ), 10, 2 );

		// Render booking details in the email content block.
		add_action( 'woocommerce_email_general_block_content', array( $this, 'render_booking_block_content' ), 10, 3 );

		// Exclude booking emails from order details rendering in block emails.
		// Booking emails don't have traditional order objects, so we handle the content ourselves.
		add_filter( 'woocommerce_emails_general_block_content_emails_without_order_details', array( $this, 'exclude_booking_emails_from_order_details' ) );

		// Load personalization tags class.
		require_once WC_BOOKINGS_ABSPATH . 'includes/emails/class-wc-booking-email-personalization-tags.php';

		$personalization_tags = new WC_Booking_Email_Personalization_Tags();
		$personalization_tags->init();
	}

	/**
	 * Register booking emails for the block email editor.
	 *
	 * @since 3.2.0
	 *
	 * @param array $emails Array of email IDs supported by block editor.
	 * @return array
	 */
	public function register_block_editor_emails( $emails ) {
		return array_merge( $emails, $this->get_booking_email_ids() );
	}

	/**
	 * Get the list of booking email IDs.
	 *
	 * @since 3.2.0
	 * @return array
	 */
	private function get_booking_email_ids() {
		return array(
			'admin_booking_cancelled',
			'new_booking',
			'booking_cancelled',
			'booking_confirmed',
			'booking_notification',
			'booking_pending_confirmation',
			'booking_reminder',
		);
	}

	/**
	 * Add booking context data for email personalization.
	 *
	 * @since 3.2.0
	 *
	 * @param array    $context The context data.
	 * @param WC_Email $email   The email object.
	 * @return array
	 */
	public function add_booking_context_data( $context, $email ) {
		// Only add context for booking emails.
		if ( ! $email || false === strpos( get_class( $email ), 'Booking' ) ) {
			return $context;
		}

		// Get the booking object from the email.
		if ( isset( $email->object ) && $email->object instanceof WC_Booking ) {
			$context['booking']    = $email->object;
			$context['booking_id'] = $email->object->get_id();
		}

		return $context;
	}

	/**
	 * Exclude booking emails from order details rendering in block emails.
	 *
	 * Booking emails use WC_Booking objects, not WC_Order objects, so the default
	 * order details rendering would not work correctly.
	 *
	 * @since 3.2.0
	 *
	 * @param array $emails Array of email IDs that should not display order details.
	 * @return array
	 */
	public function exclude_booking_emails_from_order_details( $emails ) {
		return array_merge( $emails, $this->get_booking_email_ids() );
	}

	/**
	 * Render booking details content for block emails.
	 *
	 * This is called via the woocommerce_email_general_block_content action
	 * to render booking details in the email-content block.
	 *
	 * @since 3.2.0
	 *
	 * @param bool     $sent_to_admin Whether the email is sent to admin.
	 * @param bool     $plain_text    Whether the email is plain text.
	 * @param WC_Email $email         The email object.
	 */
	public function render_booking_block_content( $sent_to_admin, $plain_text, $email ) {
		// Only render for booking emails.
		if ( ! in_array( $email->id, $this->get_booking_email_ids(), true ) ) {
			return;
		}

		// Get the booking object.
		$booking = isset( $email->object ) && $email->object instanceof WC_Booking ? $email->object : null;
		if ( ! $booking ) {
			return;
		}

		switch ( $email->id ) {
			case 'admin_booking_cancelled':
				$template_part = 'admin-booking-cancelled';
				break;
			case 'booking_cancelled':
				$template_part = 'customer-booking-cancelled';
				break;
			case 'booking_notification':
				$template_part = 'customer-booking-notification';
				break;
			case 'booking_pending_confirmation':
				$template_part = 'customer-booking-pending-confirmation';
				break;
			case 'booking_reminder':
				$template_part = 'customer-booking-reminder';
				break;
			case 'new_booking':
				$template_part = 'admin-new-booking';
				break;
			case 'booking_confirmed':
				$template_part = 'customer-booking-confirmed';
				break;
			default:
				$template_part = '';
				break;
		}

		if ( '' === $template_part ) {
			return;
		}

		wc_get_template(
			'emails/parts/' . $template_part . '.php',
			array(
				'sent_to_admin' => $sent_to_admin,
				'plain_text'    => $plain_text,
				'email'         => $email,
			),
			'woocommerce-bookings',
			WC_BOOKINGS_TEMPLATE_PATH
		);
	}
}
