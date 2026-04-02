<?php
/**
 * WC_PB_TAWS_Variation_Swatches_Compatibility
 *
 * @package  WooCommerce Product Bundles
 * @since    5.9.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeAlien Variation Swatches for WooCommerce
 *
 * @version  8.5.4
 */
class WC_PB_TAWS_Variation_Swatches_Compatibility {

	/**
	 * Initialization.
	 */
	public static function init() {

		// Support for ThemeAlien Variation Swatches for WooCommerce.
		add_action( 'woocommerce_bundle_add_to_cart', array( __CLASS__, 'tawc_variation_swatches_form_support' ) );
	}

	/**
	 * Add footer script to support ThemeAlien's Variation Swatches.
	 *
	 * @return void
	 */
	public static function tawc_variation_swatches_form_support() {

		$handle = 'wc-pb-taws-variation-swatches';
		wp_register_script( $handle, '', array( 'jquery' ), false, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_enqueue_script( $handle );
		wp_add_inline_script(
			$handle,
			// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
			"
			( function( $ ) {
				$( function() {
					var init_tawcvs_variation_swatches_form = function() {
						if ( typeof $.fn.tawcvs_variation_swatches_form === 'function' ) {
							$( '.variations_form' ).tawcvs_variation_swatches_form();
							$( document.body ).trigger( 'tawcvs_initialized' );
						}
					};

					if ( $( '.bundle_form .bundle_data' ).length > 0 ) {
						init_tawcvs_variation_swatches_form();
					}
				} );
			} )( jQuery );
			"
		);
	}
}

WC_PB_TAWS_Variation_Swatches_Compatibility::init();
