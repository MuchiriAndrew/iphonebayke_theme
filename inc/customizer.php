<?php
/**
 * Customizer settings for editable homepage / global content.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a theme option with default.
 */
function iphonebay_opt( $key, $default = '' ) {
	return get_theme_mod( 'iphonebay_' . $key, $default );
}

/**
 * Register settings.
 */
function iphonebay_customize_register( $wp_customize ) {

	$wp_customize->add_panel( 'iphonebay_panel', array(
		'title'    => __( 'IphoneBayKE Options', 'iphonebay' ),
		'priority' => 30,
	) );

	/* ---- Helper closures ---- */
	$add_text = function ( $id, $label, $section, $default = '', $type = 'text' ) use ( $wp_customize ) {
		$wp_customize->add_setting( 'iphonebay_' . $id, array(
			'default'           => $default,
			'sanitize_callback' => ( 'url' === $type ) ? 'esc_url_raw' : 'wp_kses_post',
		) );
		$wp_customize->add_control( 'iphonebay_' . $id, array(
			'label'   => $label,
			'section' => $section,
			'type'    => ( 'textarea' === $type ) ? 'textarea' : ( ( 'url' === $type ) ? 'url' : 'text' ),
		) );
	};

	$add_image = function ( $id, $label, $section ) use ( $wp_customize ) {
		$wp_customize->add_setting( 'iphonebay_' . $id, array( 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'iphonebay_' . $id, array(
			'label'     => $label,
			'section'   => $section,
			'mime_type' => 'image',
		) ) );
	};

	/* ============ HEADER / CONTACT ============ */
	$wp_customize->add_section( 'iphonebay_header', array( 'title' => __( 'Header & Contact', 'iphonebay' ), 'panel' => 'iphonebay_panel' ) );
	$add_text( 'phone', __( 'Phone number', 'iphonebay' ), 'iphonebay_header', '+254 700 000 000' );
	$add_text( 'whatsapp', __( 'WhatsApp number (digits only, e.g. 254700000000)', 'iphonebay' ), 'iphonebay_header', '254700000000' );

	/* ============ TRADE-IN ============ */
	$wp_customize->add_section( 'iphonebay_tradein', array( 'title' => __( 'Trade-In Section', 'iphonebay' ), 'panel' => 'iphonebay_panel' ) );
	$add_text( 'tradein_eyebrow', __( 'Eyebrow', 'iphonebay' ), 'iphonebay_tradein', 'Trade-In' );
	$add_text( 'tradein_title', __( 'Title (wrap a word in *asterisks* for gold)', 'iphonebay' ), 'iphonebay_tradein', 'Your old phone is *worth more* than a drawer' );
	$add_text( 'tradein_desc', __( 'Description', 'iphonebay' ), 'iphonebay_tradein', 'Trade in the phone sitting in your drawer and put the value towards your next one. Example: an iPhone 12 64GB in good condition currently fetches up to KES 27,000 — paid to your M-Pesa the same day we verify it.', 'textarea' );
	$add_text( 'tradein_step1', __( 'Step 1', 'iphonebay' ), 'iphonebay_tradein', 'Tell us your phone model & condition' );
	$add_text( 'tradein_step2', __( 'Step 2', 'iphonebay' ), 'iphonebay_tradein', 'Get an instant quote — no obligation' );
	$add_text( 'tradein_step3', __( 'Step 3', 'iphonebay' ), 'iphonebay_tradein', 'Drop off or courier, get paid via M-Pesa' );
	$add_text( 'tradein_btn_text', __( 'Button text', 'iphonebay' ), 'iphonebay_tradein', 'Get a Quote' );
	$add_text( 'tradein_btn_url', __( 'Button URL', 'iphonebay' ), 'iphonebay_tradein', '#', 'url' );
	$add_image( 'tradein_image', __( 'Image', 'iphonebay' ), 'iphonebay_tradein' );

	/* ============ DELIVERY ============ */
	$wp_customize->add_section( 'iphonebay_delivery', array( 'title' => __( 'Delivery & Payment', 'iphonebay' ), 'panel' => 'iphonebay_panel' ) );
	$add_text( 'delivery_title', __( 'Title', 'iphonebay' ), 'iphonebay_delivery', 'Delivery & Payment' );
	$add_text( 'delivery_desc', __( 'Description', 'iphonebay' ), 'iphonebay_delivery', 'We deliver anywhere in Kenya. Same-day delivery within Nairobi CBD and estates. Next-day for Mombasa, Kisumu, Eldoret, and all major towns. Free delivery on orders above KES 50,000.', 'textarea' );
	$add_image( 'delivery_image', __( 'Image', 'iphonebay' ), 'iphonebay_delivery' );

	/* ============ SECTION TITLES ============ */
	$wp_customize->add_section( 'iphonebay_sections', array( 'title' => __( 'Homepage Product Rows', 'iphonebay' ), 'panel' => 'iphonebay_panel' ) );
	$add_text( 'best_title', __( 'Best Sellers — title', 'iphonebay' ), 'iphonebay_sections', 'Best *Sellers*' );

	/* ============ FOOTER / SOCIAL ============ */
	$wp_customize->add_section( 'iphonebay_footer', array( 'title' => __( 'Footer & Social', 'iphonebay' ), 'panel' => 'iphonebay_panel' ) );
	$add_text( 'footer_desc', __( 'Footer description', 'iphonebay' ), 'iphonebay_footer', "Kenya's trusted source for genuine iPhones, Samsung, and premium smartphones. Ex-UK, Brand New, and Refurbished — all verified." );
	$add_text( 'social_tiktok', __( 'TikTok URL', 'iphonebay' ), 'iphonebay_footer', '', 'url' );
	$add_text( 'social_instagram', __( 'Instagram URL', 'iphonebay' ), 'iphonebay_footer', '', 'url' );
	$add_text( 'social_facebook', __( 'Facebook URL', 'iphonebay' ), 'iphonebay_footer', '', 'url' );
}
add_action( 'customize_register', 'iphonebay_customize_register' );

/**
 * Render a heading that supports *gold* highlight syntax.
 */
function iphonebay_highlight( $text ) {
	$text = preg_replace( '/\*(.+?)\*/', '<em>$1</em>', esc_html( $text ) );
	return nl2br( $text );
}
