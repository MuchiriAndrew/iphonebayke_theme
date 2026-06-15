<?php
/**
 * IphoneBayKE theme functions.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IPHONEBAY_VERSION', '1.4.0' );
define( 'IPHONEBAY_DIR', get_template_directory() );
define( 'IPHONEBAY_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function iphonebay_setup() {
	load_theme_textdomain( 'iphonebay', IPHONEBAY_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Image size used by product cards / hero.
	add_image_size( 'iphonebay_card', 600, 630, true );
	add_image_size( 'iphonebay_hero', 1600, 1000, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'iphonebay' ),
		'footer_products' => __( 'Footer — Products', 'iphonebay' ),
		'footer_support'  => __( 'Footer — Support', 'iphonebay' ),
		'footer_company'  => __( 'Footer — Company', 'iphonebay' ),
	) );
}
add_action( 'after_setup_theme', 'iphonebay_setup' );

/**
 * Set the content width.
 */
function iphonebay_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'iphonebay_content_width', 1400 );
}
add_action( 'after_setup_theme', 'iphonebay_content_width', 0 );

/**
 * Enqueue styles and scripts.
 */
function iphonebay_assets() {
	// Google Fonts — Outfit.
	wp_enqueue_style(
		'iphonebay-fonts',
		'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap',
		array(),
		null
	);

	// Main stylesheet (theme header).
	wp_enqueue_style( 'iphonebay-style', get_stylesheet_uri(), array(), IPHONEBAY_VERSION );

	// Theme design system + components.
	wp_enqueue_style( 'iphonebay-theme', IPHONEBAY_URI . '/assets/css/theme.css', array( 'iphonebay-style' ), IPHONEBAY_VERSION );

	// WooCommerce-specific overrides (only when Woo active).
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'iphonebay-woo', IPHONEBAY_URI . '/assets/css/woocommerce.css', array( 'iphonebay-theme' ), IPHONEBAY_VERSION );
	}

	// Main script (jQuery dependency so the variation-swatch enhancer can hook Woo's form events).
	wp_enqueue_script( 'iphonebay-main', IPHONEBAY_URI . '/assets/js/main.js', array( 'jquery' ), IPHONEBAY_VERSION, true );

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$script_data = array(
		'searchEndpoint' => esc_url_raw( rest_url( 'iphonebay/v1/product-search' ) ),
		'shopUrl'        => esc_url_raw( $shop_url ),
		'searchUrl'      => esc_url_raw( add_query_arg( 'post_type', 'product', $shop_url ) ),
		'strings'        => array(
			'searchEmpty'   => __( 'Start typing to narrow down the products in stock.', 'iphonebay' ),
			'searchLoading' => __( 'Searching products…', 'iphonebay' ),
			'searchNoMatch' => __( 'No matching products found.', 'iphonebay' ),
			'searchBrowse'  => __( 'Browse the full shop', 'iphonebay' ),
			'searchPopular' => __( 'Popular right now', 'iphonebay' ),
		),
	);

	// Provide the colour map for variation swatches.
	if ( class_exists( 'WooCommerce' ) && function_exists( 'iphonebay_colour_map' ) ) {
		$script_data['colours'] = iphonebay_colour_map();
	}

	wp_localize_script( 'iphonebay-main', 'IphoneBayData', $script_data );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'iphonebay_assets' );

/**
 * Enable Elementor compatibility: register our palette & fonts as globals
 * so client-built pages inherit the brand automatically.
 */
function iphonebay_elementor_kit() {
	// Tell Elementor our content is full-width capable.
	add_post_type_support( 'page', 'elementor' );
}
add_action( 'init', 'iphonebay_elementor_kit' );

/**
 * Body classes helper.
 */
function iphonebay_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}
	return $classes;
}
add_filter( 'body_class', 'iphonebay_body_classes' );

/**
 * Widget areas (footer is menu-driven, but keep a sidebar for blog).
 */
function iphonebay_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'iphonebay' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Sidebar shown on blog and archive pages.', 'iphonebay' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'iphonebay_widgets_init' );

// Includes.
require IPHONEBAY_DIR . '/inc/class-iphonebay-nav-walker.php';
require IPHONEBAY_DIR . '/inc/post-types.php';
require IPHONEBAY_DIR . '/inc/customizer.php';
require IPHONEBAY_DIR . '/inc/template-functions.php';
require IPHONEBAY_DIR . '/inc/forms.php';

if ( class_exists( 'WooCommerce' ) ) {
	require IPHONEBAY_DIR . '/inc/woocommerce.php';
}

/**
 * Admin notice if WooCommerce isn't active.
 */
function iphonebay_woo_notice() {
	if ( class_exists( 'WooCommerce' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>IphoneBayKE</strong> — ' . esc_html__( 'This theme is built for WooCommerce. Please install & activate WooCommerce to enable the shop, product pages and product carousels.', 'iphonebay' ) . '</p></div>';
}
add_action( 'admin_notices', 'iphonebay_woo_notice' );
