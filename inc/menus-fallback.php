<?php
/**
 * Default menu structure.
 *
 * One source of truth used twice:
 *  1. As the fallback when a menu location has no menu assigned (links work out of the box).
 *  2. By the demo importer to create real, editable menus (Appearance → Menus).
 *
 * "#quote" links open the quote modal (see assets/js/main.js).
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default menus keyed by location.
 *
 * @return array<string, array{name:string, items:array}>
 */
function brandstory_default_menus() {
	$home = home_url( '/' );

	$item = static function ( $title, $url, $children = array() ) {
		return array(
			'title'    => $title,
			'url'      => $url,
			'children' => $children,
		);
	};

	return array(
		'primary'         => array(
			'name'  => 'Primary Menu',
			'items' => array(
				$item( 'Digital marketing', '#', array(
					$item( 'Search Engine Optimization', $home . '#service-seo' ),
					$item( 'Social Media Marketing', $home . '#service-smm' ),
					$item( 'Pay Per Click', $home . '#service-ppc' ),
					$item( 'Email Marketing', $home . '#about' ),
				) ),
				$item( 'Design', '#', array(
					$item( 'UI / UX Design', $home . '#about' ),
					$item( 'Website Design', $home . '#about' ),
					$item( 'Branding', $home . '#about' ),
				) ),
				$item( 'Content', '#', array(
					$item( 'Content Writing', $home . '#about' ),
					$item( 'Content Strategy', $home . '#about' ),
				) ),
				$item( 'Production', '#', array(
					$item( 'Video Production', $home . '#case-studies' ),
					$item( 'Photography', $home . '#case-studies' ),
				) ),
				$item( 'Technology', '#', array(
					$item( 'Web Development', $home . '#about' ),
					$item( 'Mobile App Development', $home . '#about' ),
					$item( 'AI & Automation', $home . '#about' ),
				) ),
				$item( 'Resources', '#', array(
					$item( 'Case Studies', $home . '#case-studies' ),
					$item( 'FAQs', $home . '#faq' ),
					$item( 'Our Team', $home . '#team' ),
				) ),
			),
		),
		'footer_links'    => array(
			'name'  => 'Footer – Our Links',
			'items' => array(
				$item( 'Digital Marketing Agency Abu Dhabi', $home ),
				$item( 'Digital Marketing Agency Sharjah', $home . '#locations' ),
				$item( 'Digital Marketing Agency in Dubai', $home . '#locations' ),
				$item( 'SEO Agency in Dubai', $home . '#service-seo' ),
				$item( 'Digital Marketing Agency Ajman', $home . '#locations' ),
				$item( 'PR Agency in Dubai', $home . '#locations' ),
				$item( 'B2B Company in Dubai', $home . '#locations' ),
				$item( 'Content Writing Company in Dubai', $home . '#locations' ),
			),
		),
		'footer_services' => array(
			'name'  => 'Footer – Services',
			'items' => array(
				$item( 'Digital Marketing Company Dubai', $home . '#services' ),
				$item( 'UI UX Design Services Dubai', $home . '#services' ),
				$item( 'Web Design Company in Dubai', $home . '#services' ),
				$item( 'SEO Company in Dubai', $home . '#service-seo' ),
				$item( 'Social Media Marketing in Dubai', $home . '#service-smm' ),
				$item( 'Mobile App Marketing Services', $home . '#services' ),
				$item( 'Email Marketing Services in Dubai', $home . '#services' ),
				$item( 'PPC Agency in Dubai', $home . '#service-ppc' ),
			),
		),
		'locations'       => array(
			'name'  => 'Other Locations',
			'items' => array_map(
				static function ( $title ) use ( $item ) {
					return $item( $title, '#quote' );
				},
				array(
					'Digital Marketing Agency in Dubai',
					'Digital Marketing Agency in Al Ain',
					'Digital Marketing Agency in Qatar',
					'Digital Marketing Agency in Sharjah',
					'Digital Marketing Agency in Fujairah',
					'Digital Marketing Agency in Kuwait',
					'Digital Marketing Agency in Abu Dhabi',
					'Digital Marketing Agency in Ras Al Khaimah',
					'PR Agency in Dubai',
					'Digital Marketing Agency in Ajman',
					'Digital Marketing Agency in Bahrain',
					'Social Media Marketing Agency in Dubai',
					'PPC Agency in Dubai',
					'SEO Dubai',
					'Content Writers in Dubai',
				)
			),
		),
	);
}

/**
 * Render a default menu for a location with the same markup the real menu produces.
 *
 * @param string $location Menu location.
 */
function brandstory_render_default_menu( $location ) {
	$menus = brandstory_default_menus();
	if ( empty( $menus[ $location ] ) ) {
		return;
	}

	foreach ( $menus[ $location ]['items'] as $index => $entry ) {
		if ( 'primary' === $location ) {
			if ( $entry['children'] ) {
				$dd = 'dd-default-' . $index;
				echo '<li class="main-nav__item has-dropdown">';
				printf(
					'<button class="main-nav__link" type="button" aria-expanded="false" aria-controls="%1$s">%2$s <svg class="icon icon--sm" aria-hidden="true"><use href="#i-chevron-down"></use></svg></button>',
					esc_attr( $dd ),
					esc_html( $entry['title'] )
				);
				printf( '<ul class="dropdown%s" id="%s">', count( $menus[ $location ]['items'] ) - 1 === $index ? ' dropdown--right' : '', esc_attr( $dd ) );
				foreach ( $entry['children'] as $child ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( $child['url'] ), esc_html( $child['title'] ) );
				}
				echo '</ul></li>';
			} else {
				printf( '<li class="main-nav__item"><a class="main-nav__link" href="%s">%s</a></li>', esc_url( $entry['url'] ), esc_html( $entry['title'] ) );
			}
			continue;
		}

		if ( 'locations' === $location ) {
			echo '<li>';
			brandstory_location_link( $entry['title'], $entry['url'] );
			echo '</li>';
			continue;
		}

		printf( '<li><a href="%s">%s</a></li>', esc_url( $entry['url'] ), esc_html( $entry['title'] ) );
	}
}

/**
 * A single "Other locations" link with its pin icon.
 *
 * @param string $title Link text.
 * @param string $url   URL.
 */
function brandstory_location_link( $title, $url ) {
	printf( '<a class="location-link" href="%s">', esc_url( $url ) );
	brandstory_icon( 'pin' );
	echo esc_html( $title ) . '</a>';
}

/**
 * Print the items of a menu location: the real menu if assigned, otherwise the defaults.
 *
 * @param string $location Menu location.
 */
function brandstory_menu_items( $location ) {
	if ( ! has_nav_menu( $location ) ) {
		brandstory_render_default_menu( $location );
		return;
	}

	if ( 'primary' === $location ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'walker'         => new BrandStory_Nav_Walker(),
				'depth'          => 2,
			)
		);
		return;
	}

	$items = wp_get_nav_menu_items( get_nav_menu_locations()[ $location ] );
	foreach ( (array) $items as $menu_item ) {
		if ( 'locations' === $location ) {
			echo '<li>';
			brandstory_location_link( $menu_item->title, $menu_item->url );
			echo '</li>';
		} else {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $menu_item->url ), esc_html( $menu_item->title ) );
		}
	}
}
