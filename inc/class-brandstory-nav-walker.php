<?php
/**
 * Header navigation walker.
 *
 * Outputs the same accessible markup as the custom-coded build:
 * top-level items with children become <button aria-expanded> toggles
 * that control a .dropdown list; leaf items are plain links.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class BrandStory_Nav_Walker
 */
class BrandStory_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * ID of the dropdown currently being opened (used for aria-controls).
	 *
	 * @var string
	 */
	private $current_dropdown_id = '';

	/**
	 * Opening <ul> for a submenu.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= sprintf( '<ul class="dropdown" id="%s">', esc_attr( $this->current_dropdown_id ) );
	}

	/**
	 * Closing </ul> for a submenu.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Single menu item.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     Item id.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$title        = apply_filters( 'the_title', $item->title, $item->ID );
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		if ( 0 === $depth ) {
			$classes = array( 'main-nav__item' );
			if ( $has_children ) {
				$classes[] = 'has-dropdown';
			}
			if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
				$classes[] = 'is-current';
			}
			$output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';

			if ( $has_children ) {
				$this->current_dropdown_id = 'dd-' . $item->ID;
				$output .= sprintf(
					'<button class="main-nav__link" type="button" aria-expanded="false" aria-controls="%1$s">%2$s <svg class="icon icon--sm" aria-hidden="true"><use href="#i-chevron-down"></use></svg></button>',
					esc_attr( $this->current_dropdown_id ),
					esc_html( $title )
				);
				return;
			}

			$output .= sprintf( '<a class="main-nav__link" href="%1$s"%2$s>%3$s</a>', esc_url( $item->url ), $this->link_target( $item ), esc_html( $title ) );
			return;
		}

		$output .= sprintf( '<li><a href="%1$s"%2$s>%3$s</a>', esc_url( $item->url ), $this->link_target( $item ), esc_html( $title ) );
	}

	/**
	 * Target/rel attributes for a menu item.
	 *
	 * @param WP_Post $item Menu item.
	 * @return string
	 */
	private function link_target( $item ) {
		return '_blank' === $item->target ? ' target="_blank" rel="noopener"' : '';
	}
}
