<?php
/**
 * Theme setup.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and menu locations.
 */
function brandstory_setup() {
	load_child_theme_textdomain( 'brandstory', BRANDSTORY_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	register_nav_menus(
		array(
			'primary'         => __( 'Primary (header)', 'brandstory' ),
			'footer_links'    => __( 'Footer – Our Links', 'brandstory' ),
			'footer_services' => __( 'Footer – Services', 'brandstory' ),
			'locations'       => __( 'Other Locations', 'brandstory' ),
		)
	);
}
add_action( 'after_setup_theme', 'brandstory_setup', 20 );

/**
 * Let the theme own colours and fonts: switch off Elementor's default
 * colour + typography schemes so widgets inherit the design tokens in base.css.
 */
function brandstory_elementor_defaults() {
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
}
add_action( 'after_switch_theme', 'brandstory_elementor_defaults' );

/**
 * Hello Elementor: we ship our own header/footer and reset, so skip the parent's
 * stylesheets and its header/footer feature to avoid duplicate CSS.
 */
add_filter( 'hello_elementor_enqueue_style', '__return_false' );
add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
add_filter( 'hello_elementor_header_footer', '__return_false' );
add_filter( 'hello_elementor_page_title', '__return_false' );
add_filter( 'hello_elementor_description_meta_tag', '__return_false' ); // We print our own below.

/**
 * Body class used to scope Elementor overrides.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function brandstory_body_class( $classes ) {
	$classes[] = 'brandstory';
	return $classes;
}
add_filter( 'body_class', 'brandstory_body_class' );

/**
 * Meta description for the front page (lightweight; an SEO plugin overrides this).
 */
function brandstory_meta_description() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || ! is_front_page() ) {
		return;
	}
	$description = __( 'Looking for a digital marketing agency in Abu Dhabi? With 12+ years of expertise, BrandStory delivers culturally intelligent, data-driven SEO, social media and PPC strategies.', 'brandstory' );
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	echo '<meta name="theme-color" content="#0A0B0F">' . "\n";
}
add_action( 'wp_head', 'brandstory_meta_description', 1 );
