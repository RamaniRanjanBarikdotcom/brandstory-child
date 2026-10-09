<?php
/**
 * Template helpers.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of a file inside /assets.
 *
 * @param string $path Relative path, e.g. 'images/team/team-1.webp'.
 */
function brandstory_asset( $path ) {
	return BRANDSTORY_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Print an icon from the inline SVG sprite (template-parts/global/icon-sprite.php).
 *
 * @param string $name  Icon id without the "i-" prefix.
 * @param string $class Extra classes.
 */
function brandstory_icon( $name, $class = '' ) {
	printf(
		'<svg class="icon %1$s" aria-hidden="true" focusable="false"><use href="#i-%2$s"></use></svg>',
		esc_attr( $class ),
		esc_attr( $name )
	);
}

/**
 * Contact + social settings with sensible defaults (editable in the Customizer).
 *
 * @param string $key Setting key.
 * @return string
 */
function brandstory_setting( $key ) {
	$defaults = array(
		'phone'        => '+971 52 283 1655',
		'email'        => 'info@brandstory.ae',
		'address'      => 'G5, Al Meheri Plaza, opp DBC Building, Al Khabaisi Area, Deira Dubai - 81577, United Arab Emirates',
		'head_office'  => 'Villa No 1, Westar Les Maisonettes, District 10, Street 5, Jumeirah Village Circle, Dubai, United Arab Emirates.',
		'footer_about' => "The BrandStory team has industry expertise and cutting-edge digital capabilities to help clients connect with their audiences in complex environments. We can't wait for you join us on this journey!",
		'linkedin'     => 'https://www.linkedin.com/',
		'youtube'      => 'https://www.youtube.com/',
		'facebook'     => 'https://www.facebook.com/',
		'instagram'    => 'https://www.instagram.com/',
		'twitter'      => 'https://twitter.com/',
	);

	return (string) get_theme_mod( 'brandstory_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}
