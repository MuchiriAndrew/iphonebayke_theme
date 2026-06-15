<?php
/**
 * Custom nav walkers for IphoneBayKE.
 *
 * - IphoneBay_Nav_Walker        : desktop nav with hover dropdowns (1 level).
 * - IphoneBay_Mobile_Nav_Walker : off-canvas nav with tap-to-expand submenus.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Desktop primary nav walker.
 */
class IphoneBay_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="dropdown-menu">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );

		if ( 0 === $depth ) {
			$li_class = 'nav-item' . ( $has_children ? ' has-dropdown' : '' );
			$a_class  = 'nav-link';
		} else {
			$li_class = 'dropdown-li';
			$a_class  = 'dropdown-link';
		}

		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-parent', $classes, true ) ) {
			$a_class .= ' active';
		}

		$output .= '<li class="' . esc_attr( $li_class ) . '">';

		$atts          = array();
		$atts['href']  = ! empty( $item->url ) ? $item->url : '#';
		$atts['class'] = $a_class;
		if ( ! empty( $item->target ) ) {
			$atts['target'] = $item->target;
		}
		if ( ! empty( $item->xfn ) ) {
			$atts['rel'] = $item->xfn;
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' !== $value ) {
				$attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$caret = '';
		if ( $has_children && 0 === $depth ) {
			$caret = ' <svg class="nav-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
		}

		$output .= '<a' . $attributes . '>' . esc_html( $title ) . $caret . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

/**
 * Mobile off-canvas nav walker (tap-to-expand submenus).
 */
class IphoneBay_Mobile_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="offcanvas-sub">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$url          = ! empty( $item->url ) ? $item->url : '#';
		$title        = apply_filters( 'the_title', $item->title, $item->ID );

		if ( 0 === $depth && $has_children ) {
			// Parent row with an expand toggle.
			$output .= '<div class="offcanvas-group">';
			$output .= '<div class="offcanvas-row">';
			$output .= '<a href="' . esc_url( $url ) . '" class="offcanvas-link">' . esc_html( $title ) . '</a>';
			$output .= '<button class="offcanvas-expand" type="button" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle submenu', 'iphonebay' ) . '"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></button>';
			$output .= '</div>';
		} elseif ( 0 === $depth ) {
			$output .= '<a href="' . esc_url( $url ) . '" class="offcanvas-link">' . esc_html( $title ) . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>';
		} else {
			$output .= '<a href="' . esc_url( $url ) . '" class="offcanvas-sublink">' . esc_html( $title ) . '</a>';
		}
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		if ( 0 === $depth && $has_children ) {
			$output .= '</div>'; // .offcanvas-group
		}
	}
}

/**
 * Fallback desktop menu (fresh install, no menu assigned yet).
 */
function iphonebay_default_menu() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	echo '<ul class="nav-links" id="primary-menu">';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'iphonebay' ) . '</a></li>';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( $shop ) . '">' . esc_html__( 'Shop', 'iphonebay' ) . '</a></li>';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( $shop ) . '">' . esc_html__( 'Categories', 'iphonebay' ) . '</a></li>';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( home_url( '/#tradein' ) ) . '">' . esc_html__( 'Trade-In', 'iphonebay' ) . '</a></li>';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html__( 'About', 'iphonebay' ) . '</a></li>';
	echo '<li class="nav-item"><a class="nav-link" href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'iphonebay' ) . '</a></li>';
	echo '</ul>';
}

/**
 * Fallback mobile menu.
 */
function iphonebay_default_mobile_menu() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$links = array(
		__( 'Home', 'iphonebay' )       => home_url( '/' ),
		__( 'Shop', 'iphonebay' )       => $shop,
		__( 'Categories', 'iphonebay' ) => $shop,
		__( 'Trade-In', 'iphonebay' )   => home_url( '/#tradein' ),
		__( 'About', 'iphonebay' )      => home_url( '/about/' ),
		__( 'Contact', 'iphonebay' )    => home_url( '/contact/' ),
	);
	foreach ( $links as $label => $url ) {
		echo '<a href="' . esc_url( $url ) . '" class="offcanvas-link">' . esc_html( $label ) . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>';
	}
}
