<?php
// This file is generated. Do not modify it manually.
return array(
	'booking-duration' => array(
		'name' => 'woocommerce-bookings/booking-duration',
		'title' => 'Booking Duration',
		'description' => 'Displays the duration of a booking.',
		'category' => 'woocommerce-product-elements',
		'attributes' => array(
			'prefix' => array(
				'type' => 'string',
				'default' => 'Duration: ',
				'role' => 'content'
			),
			'suffix' => array(
				'type' => 'string',
				'default' => '',
				'role' => 'content'
			),
			'textAlign' => array(
				'type' => 'string'
			)
		),
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'gradients' => true,
				'link' => true,
				'__experimentalDefaultControls' => array(
					'background' => true,
					'text' => true,
					'link' => true
				)
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'__experimentalFontFamily' => true,
				'__experimentalFontWeight' => true,
				'__experimentalFontStyle' => true,
				'__experimentalTextTransform' => true,
				'__experimentalTextDecoration' => true,
				'__experimentalLetterSpacing' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true,
					'fontWeight' => true
				)
			),
			'__experimentalBorder' => array(
				'radius' => true,
				'color' => true,
				'width' => true,
				'style' => true,
				'__experimentalDefaultControls' => array(
					'radius' => true,
					'color' => true,
					'width' => true,
					'style' => true
				)
			)
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'editorScript' => 'file:./index.js',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json'
	),
	'booking-form' => array(
		'name' => 'woocommerce-bookings/booking-form',
		'title' => 'Booking Form',
		'description' => 'A wrapper block for booking form elements.',
		'category' => 'woocommerce-product-elements',
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'inserter' => false,
			'html' => false
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'editorScript' => 'file:./index.js',
		'viewScriptModule' => 'file:./frontend.js'
	),
	'booking-form-calendar' => array(
		'name' => 'woocommerce-bookings/booking-form-calendar',
		'title' => 'Booking Form Calendar',
		'description' => 'Displays an interactive calendar for booking availability within the booking form.',
		'category' => 'woocommerce-product-elements',
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'inserter' => false,
			'html' => false,
			'interactivity' => true
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'viewScriptModule' => 'file:./frontend.js',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'booking-form-modal' => array(
		'name' => 'woocommerce-bookings/booking-form-modal',
		'title' => 'Booking Modal',
		'description' => 'Displays a modal where shoppers can select specific booking options before adding to cart.',
		'category' => 'woocommerce-product-elements',
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'inserter' => false,
			'html' => false,
			'interactivity' => true
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'viewScriptModule' => 'file:./frontend.js',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'booking-form-team-member-selector' => array(
		'name' => 'woocommerce-bookings/booking-form-team-member-selector',
		'title' => 'Booking Team Member Selector',
		'description' => 'Displays a selector to choose a team member for a booking.',
		'category' => 'woocommerce-product-elements',
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'inserter' => false,
			'html' => false,
			'interactivity' => true
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'viewScriptModule' => 'file:./frontend.js',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'booking-form-time-slots' => array(
		'name' => 'woocommerce-bookings/booking-form-time-slots',
		'title' => 'Booking Time Slots',
		'description' => 'Displays a list of time slots for a booking product.',
		'category' => 'woocommerce-product-elements',
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'html' => false,
			'interactivity' => true
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'viewScriptModule' => 'file:./frontend.js',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'booking-location' => array(
		'name' => 'woocommerce-bookings/booking-location',
		'title' => 'Booking Location',
		'description' => 'Displays the location of a booking.',
		'category' => 'woocommerce-product-elements',
		'attributes' => array(
			'prefix' => array(
				'type' => 'string',
				'default' => 'Location: ',
				'role' => 'content'
			),
			'suffix' => array(
				'type' => 'string',
				'default' => '',
				'role' => 'content'
			),
			'textAlign' => array(
				'type' => 'string'
			)
		),
		'usesContext' => array(
			'postId'
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'gradients' => true,
				'link' => true,
				'__experimentalDefaultControls' => array(
					'background' => true,
					'text' => true,
					'link' => true
				)
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'__experimentalFontFamily' => true,
				'__experimentalFontWeight' => true,
				'__experimentalFontStyle' => true,
				'__experimentalTextTransform' => true,
				'__experimentalTextDecoration' => true,
				'__experimentalLetterSpacing' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true,
					'fontWeight' => true
				)
			),
			'__experimentalBorder' => array(
				'radius' => true,
				'color' => true,
				'width' => true,
				'style' => true,
				'__experimentalDefaultControls' => array(
					'radius' => true,
					'color' => true,
					'width' => true,
					'style' => true
				)
			)
		),
		'keywords' => array(
			'WooCommerce',
			'Bookings'
		),
		'textdomain' => 'woocommerce-bookings',
		'editorScript' => 'file:./index.js',
		'render' => 'file:./render.php',
		'apiVersion' => 3,
		'$schema' => 'https://schemas.wp.org/trunk/block.json'
	)
);
