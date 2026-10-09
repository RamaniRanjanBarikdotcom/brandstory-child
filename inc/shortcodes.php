<?php
/**
 * Shortcodes for sections Elementor (free) has no widget for.
 *
 *   [bs_clients]         Two-row logo marquee.
 *   [bs_team_carousel]   Centre-mode team photo carousel with arrow buttons.
 *   [bs_case_studies]    Case-study card carousel with arrow buttons.
 *
 * Each accepts an optional ids="12,34,56" attribute (Media Library attachment IDs).
 * Without it, the images bundled in the theme are used, so the page works right after import.
 * Markup lives in template-parts/shortcodes/*.php.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalise images for a shortcode: Media Library IDs, or bundled defaults.
 *
 * @param string $ids      Comma-separated attachment IDs.
 * @param array  $defaults List of [ relative asset path, alt text, width, height ].
 * @return array<int, array{src:string, alt:string, width:int, height:int, srcset:string}>
 */
function brandstory_shortcode_images( $ids, array $defaults ) {
	$images = array();

	foreach ( array_filter( array_map( 'absint', explode( ',', (string) $ids ) ) ) as $id ) {
		$src = wp_get_attachment_image_src( $id, 'large' );
		if ( ! $src ) {
			continue;
		}
		$images[] = array(
			'src'    => $src[0],
			'width'  => (int) $src[1],
			'height' => (int) $src[2],
			'alt'    => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
			'srcset' => (string) wp_get_attachment_image_srcset( $id, 'large' ),
		);
	}

	if ( $images ) {
		return $images;
	}

	foreach ( $defaults as $image ) {
		$images[] = array(
			'src'    => brandstory_asset( $image[0] ),
			'alt'    => $image[1],
			'width'  => $image[2],
			'height' => $image[3],
			'srcset' => '',
		);
	}
	return $images;
}

/**
 * Render a shortcode template part into a string.
 *
 * @param string $slug Template part slug in template-parts/shortcodes.
 * @param array  $args Arguments passed to the template.
 * @return string
 */
function brandstory_render_part( $slug, array $args ) {
	ob_start();
	get_template_part( 'template-parts/shortcodes/' . $slug, null, $args );
	return (string) ob_get_clean();
}

/**
 * [bs_clients]
 *
 * @param array $atts Attributes.
 * @return string
 */
function brandstory_shortcode_clients( $atts ) {
	$atts = shortcode_atts( array( 'ids' => '' ), $atts, 'bs_clients' );

	$logos = brandstory_shortcode_images(
		$atts['ids'],
		array(
			array( 'images/clients/client-atom.webp', 'Atom Interiors', 280, 120 ),
			array( 'images/clients/client-hosachiguru.webp', 'Hosachiguru', 554, 120 ),
			array( 'images/clients/client-microgenesis.webp', 'Microgenesis', 172, 120 ),
			array( 'images/clients/client-biogen.webp', 'Biogen', 327, 120 ),
			array( 'images/clients/client-redseer.webp', 'Redseer', 492, 120 ),
			array( 'images/clients/client-hola.webp', 'Hola', 447, 120 ),
			array( 'images/clients/client-curtain-label.webp', 'Curtain Label', 480, 120 ),
			array( 'images/clients/client-smartlinks.webp', 'Smartlinks', 640, 120 ),
			array( 'images/clients/client-takeleap.webp', 'TakeLeap', 584, 100 ),
		)
	);

	$half = (int) ceil( count( $logos ) / 2 );

	return brandstory_render_part(
		'clients',
		array( 'rows' => array( array_slice( $logos, 0, $half ), array_slice( $logos, $half ) ) )
	);
}
add_shortcode( 'bs_clients', 'brandstory_shortcode_clients' );

/**
 * [bs_team_carousel]
 *
 * @param array $atts Attributes.
 * @return string
 */
function brandstory_shortcode_team( $atts ) {
	$atts = shortcode_atts( array( 'ids' => '' ), $atts, 'bs_team_carousel' );

	$photos = brandstory_shortcode_images(
		$atts['ids'],
		array(
			array( 'images/team/team-1.webp', 'BrandStory team collaborating at a shared desk', 760, 504 ),
			array( 'images/team/team-2.webp', 'Designer working on a desktop in the BrandStory studio', 760, 493 ),
			array( 'images/team/team-3.webp', 'Marketing specialists working on laptops', 760, 504 ),
		)
	);

	// Swiper's centred loop needs enough slides on wide screens: repeat short sets.
	$slides = $photos;
	while ( count( $slides ) < 9 ) {
		$slides = array_merge( $slides, $photos );
	}

	return brandstory_render_part( 'team-carousel', array( 'slides' => array_slice( $slides, 0, max( 9, count( $photos ) ) ) ) );
}
add_shortcode( 'bs_team_carousel', 'brandstory_shortcode_team' );

/**
 * [bs_case_studies]
 *
 * @param array $atts Attributes.
 * @return string
 */
function brandstory_shortcode_case_studies( $atts ) {
	$atts = shortcode_atts( array( 'ids' => '' ), $atts, 'bs_case_studies' );

	$cases = brandstory_shortcode_images(
		$atts['ids'],
		array(
			array( 'images/case-studies/case-study-1.webp', 'Glass facade of a modern commercial building', 840, 1261 ),
			array( 'images/case-studies/case-study-2.webp', 'Engineer reviewing a 3D engine model on a tablet', 840, 1261 ),
			array( 'images/case-studies/case-study-3.webp', 'Consultants reviewing documents in a meeting', 840, 1261 ),
			array( 'images/case-studies/case-study-1.webp', 'Glass facade of a modern commercial building', 840, 1261 ),
		)
	);

	$tags = array( 'Real Estate', 'Industrial', 'Professional Services', 'Hospitality' );

	return brandstory_render_part( 'case-studies', array( 'cases' => $cases, 'tags' => $tags ) );
}
add_shortcode( 'bs_case_studies', 'brandstory_shortcode_case_studies' );
