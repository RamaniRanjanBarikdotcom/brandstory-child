<?php
/**
 * BrandStory Child theme bootstrap.
 *
 * Each concern lives in its own file under /inc so functions.php stays a simple loader.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

define( 'BRANDSTORY_VERSION', '1.0.0' );
define( 'BRANDSTORY_DIR', get_stylesheet_directory() );
define( 'BRANDSTORY_URI', get_stylesheet_directory_uri() );

$brandstory_includes = array(
	'inc/template-tags.php',      // Small helpers: icons, asset URLs, contact data.
	'inc/setup.php',              // Theme supports, menus, Elementor defaults.
	'inc/enqueue.php',            // Styles and scripts.
	'inc/class-brandstory-nav-walker.php',
	'inc/menus-fallback.php',     // Default menus so links work before menus are configured.
	'inc/customizer.php',         // Contact details + social links.
	'inc/shortcodes.php',         // [bs_clients], [bs_team_carousel], [bs_case_studies].
	'inc/contact-form.php',       // AJAX quote form + "Enquiries" post type.
	'inc/demo-import.php',        // One-click page import (Appearance → BrandStory Setup).
);

foreach ( $brandstory_includes as $brandstory_file ) {
	require_once BRANDSTORY_DIR . '/' . $brandstory_file;
}
