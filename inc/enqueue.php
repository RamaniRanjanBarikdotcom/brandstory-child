<?php
/**
 * Styles and scripts.
 *
 * CSS is split by responsibility and shared 1:1 with the custom-coded build:
 *   base.css        tokens, reset, typography, utilities
 *   components.css  buttons, cards, accordions, slider buttons, modal
 *   layout.css      header, navigation, footer
 *   sections.css    shortcode sections (clients, team, case studies) + locations
 *   elementor.css   maps Elementor widget markup onto the same design system
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Preload the two most used font files.
 */
function brandstory_preload_fonts() {
	foreach ( array( 400, 600 ) as $weight ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( brandstory_asset( "fonts/hanken-grotesk-latin-{$weight}-normal.woff2" ) )
		);
	}
}
add_action( 'wp_head', 'brandstory_preload_fonts', 2 );

/**
 * Enqueue front-end assets.
 */
function brandstory_enqueue_assets() {
	$ver = BRANDSTORY_VERSION;

	// Swiper: reuse the copy Elementor registers, fall back to the bundled one.
	if ( ! wp_script_is( 'swiper', 'registered' ) ) {
		wp_register_script( 'swiper', brandstory_asset( 'vendor/swiper/swiper-bundle.min.js' ), array(), '11.2.10', true );
	}
	if ( ! wp_style_is( 'swiper', 'registered' ) ) {
		wp_register_style( 'swiper', brandstory_asset( 'vendor/swiper/swiper-bundle.min.css' ), array(), '11.2.10' );
	}

	wp_enqueue_style( 'swiper' );
	wp_enqueue_style( 'brandstory-base', brandstory_asset( 'css/base.css' ), array(), $ver );
	wp_enqueue_style( 'brandstory-components', brandstory_asset( 'css/components.css' ), array( 'brandstory-base' ), $ver );
	wp_enqueue_style( 'brandstory-layout', brandstory_asset( 'css/layout.css' ), array( 'brandstory-components' ), $ver );
	wp_enqueue_style( 'brandstory-sections', brandstory_asset( 'css/sections.css' ), array( 'brandstory-layout' ), $ver );
	wp_enqueue_style( 'brandstory-elementor', brandstory_asset( 'css/elementor.css' ), array( 'brandstory-sections' ), $ver );

	wp_enqueue_script( 'swiper' );
	wp_enqueue_script( 'brandstory-main', brandstory_asset( 'js/main.js' ), array( 'swiper' ), $ver, true );
	wp_localize_script(
		'brandstory-main',
		'brandstoryData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'brandstory_contact' ),
			'i18n'    => array(
				'invalid' => __( 'Please fill in the highlighted fields.', 'brandstory' ),
				'success' => __( 'Thank you! Our team will contact you shortly.', 'brandstory' ),
				'error'   => __( 'Something went wrong. Please try again or email us directly.', 'brandstory' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'brandstory_enqueue_assets', 20 );

/**
 * Load our CSS after Elementor's per-page CSS so overrides win without !important.
 */
function brandstory_elementor_css_priority() {
	if ( wp_style_is( 'brandstory-elementor', 'enqueued' ) ) {
		wp_dequeue_style( 'brandstory-elementor' );
		wp_enqueue_style( 'brandstory-elementor' );
	}
}
add_action( 'wp_enqueue_scripts', 'brandstory_elementor_css_priority', 999 );
