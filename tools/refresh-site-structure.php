<?php
/**
 * Clean demo content and align core site pages/templates.
 *
 * Run with:
 * wp eval-file wp-content/themes/iphonebay/tools/refresh-site-structure.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this script with wp eval-file.\n" );
}

function iphonebay_refresh_upsert_page( $title, $slug, $content = '', $template = 'default' ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		$page = get_page_by_title( $title, OBJECT, 'page' );
	}

	$args = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => $content,
	);

	if ( $page ) {
		$args['ID'] = $page->ID;
		$page_id    = wp_update_post( $args );
	} else {
		$page_id = wp_insert_post( $args );
	}

	if ( 'default' !== $template ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}

	return (int) $page_id;
}

function iphonebay_refresh_term( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) ) ? $term : null;
}

function iphonebay_refresh_sync_menu( $menu_name, $items ) {
	$menu    = wp_get_nav_menu_object( $menu_name );
	$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );

	$existing_items = wp_get_nav_menu_items( $menu_id );
	if ( $existing_items ) {
		foreach ( $existing_items as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	foreach ( $items as $item ) {
		$args = array(
			'menu-item-title'  => $item['title'],
			'menu-item-status' => 'publish',
			'menu-item-type'   => $item['type'],
		);

		if ( 'post_type' === $item['type'] ) {
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = (int) $item['object_id'];
		} elseif ( 'taxonomy' === $item['type'] ) {
			$args['menu-item-object']    = 'product_cat';
			$args['menu-item-object-id'] = (int) $item['object_id'];
		} else {
			$args['menu-item-url'] = $item['url'];
		}

		wp_update_nav_menu_item( $menu_id, 0, $args );
	}

	return $menu_id;
}

function iphonebay_refresh_assign_menu_location( $location, $menu_id ) {
	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = (int) $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

$home_id    = get_option( 'page_on_front' ) ? (int) get_option( 'page_on_front' ) : iphonebay_refresh_upsert_page( 'Home Electronic', 'home-electronic' );
$shop_id    = iphonebay_refresh_upsert_page( 'Shop', 'shop' );
$about_id   = iphonebay_refresh_upsert_page( 'About', 'about', 'Learn more about the devices we source, how we test them, and how delivery works across Kenya.' );
$contact_id = iphonebay_refresh_upsert_page( 'Contact Us', 'contact-us', 'Use the form below and our team will get back to you with stock details, quotes, or order support.', 'template-contact.php' );
$track_id   = iphonebay_refresh_upsert_page( 'Track Order', 'order-tracking', 'If you have already placed an order, contact us with your order number and we will confirm the latest delivery update.' );
$faq_id     = iphonebay_refresh_upsert_page( 'FAQs', 'faqs', "Common questions coming soon.\n\nYou can also contact us directly for delivery, warranty, or stock questions." );
$blog_id    = iphonebay_refresh_upsert_page( 'Blog', 'blog', 'Store guides and behind-the-scenes notes live here.' );
$trade_id   = iphonebay_refresh_upsert_page( 'Trade-In', 'trade-in', 'Ready to upgrade? Share your current device details below and we will send a trade-in quote.', 'template-trade-in.php' );
$privacy_id = iphonebay_refresh_upsert_page( 'Privacy Policy', 'privacy-policy', "This Privacy Policy explains how iPhoneBayKE collects, uses, and protects customer information.\n\n1. Information we collect\nDescribe customer, order, and support data collected on the site.\n\n2. How we use it\nExplain fulfilment, support, and communication uses.\n\n3. Data sharing\nClarify payment, delivery, and legal disclosures.\n\n4. Contact\nProvide a way for customers to request help or deletion.", 'template-legal.php' );
$terms_id   = iphonebay_refresh_upsert_page( 'Terms of Service', 'terms-of-service', "These Terms of Service explain the rules for browsing, purchasing, and trading in devices through iPhoneBayKE.\n\n1. Orders and pricing\n2. Device condition and grading\n3. Delivery and collection\n4. Warranty and returns\n5. Trade-in valuations\n6. Contact and dispute handling", 'template-legal.php' );

update_option( 'page_for_posts', $blog_id );
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );

$how_we_test = get_page_by_path( 'how-we-test-battery-life', OBJECT, 'post' );
$how_we_test_args = array(
	'post_title'   => 'How We Test Battery Life Before Any Phone Goes Live',
	'post_name'    => 'how-we-test-battery-life',
	'post_type'    => 'post',
	'post_status'  => 'publish',
	'post_content' => "At iPhoneBayKE, battery health is one of the first checks we run before listing a device.\n\nWe review the battery health report, charging behaviour, heat, and overall stability. We only list devices whose condition matches the grade shown on the product page.\n\nFor refurbished and Ex-UK stock, we compare the reported battery health against real-world charging and drain tests. If a device fails that review, it does not go live.\n\nThat is why you will see battery health called out clearly on product pages, checkout, and customer support conversations.",
);

if ( $how_we_test ) {
	$how_we_test_args['ID'] = $how_we_test->ID;
	$how_we_test_id         = wp_update_post( $how_we_test_args );
} else {
	$how_we_test_id = wp_insert_post( $how_we_test_args );
}

set_theme_mod( 'iphonebay_tradein_btn_url', get_permalink( $trade_id ) );

$hero_buttons = array(
	'reference-premium-iphones'   => array( 'iphonebay_btn1_url' => get_term_link( iphonebay_refresh_term( 'iphones' ) ), 'iphonebay_btn2_url' => add_query_arg( 'collection', 'deals', get_permalink( $shop_id ) ) ),
	'reference-battery-health'    => array( 'iphonebay_btn1_url' => add_query_arg( 'filter_condition', 'refurbished', get_permalink( $shop_id ) ), 'iphonebay_btn2_url' => get_permalink( $how_we_test_id ) ),
	'reference-samsung-collection'=> array( 'iphonebay_btn1_url' => get_term_link( iphonebay_refresh_term( 'samsung-phones' ) ), 'iphonebay_btn2_url' => get_term_link( iphonebay_refresh_term( 'samsung-phones' ) ) ),
	'reference-trade-in'          => array( 'iphonebay_btn1_url' => get_permalink( $trade_id ), 'iphonebay_btn2_url' => add_query_arg( 'topic', 'trade-in', get_permalink( $contact_id ) ) ),
);

foreach ( $hero_buttons as $reference_key => $meta ) {
	$slides = get_posts(
		array(
			'post_type'      => 'hero_slide',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_iphonebay_reference_key',
			'meta_value'     => $reference_key,
		)
	);

	if ( empty( $slides ) ) {
		continue;
	}

	foreach ( $meta as $meta_key => $meta_value ) {
		update_post_meta( $slides[0]->ID, $meta_key, $meta_value );
	}
}

$trash_page_slugs = array(
	'sample-page',
	'compare',
	'wishlist',
	'about-2-2',
	'home-cosmetics',
	'home-automotive',
	'home-furniture',
	'home-supermarket-02',
	'home-supermarket-01',
	'coming-soon',
	'typography',
	'refund_returns',
	'blog-2',
);

foreach ( $trash_page_slugs as $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post && 'trash' !== $page->post_status ) {
		wp_trash_post( $page->ID );
	}
}

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
		'posts_per_page' => -1,
	)
);

foreach ( $posts as $post ) {
	if ( (int) $post->ID === (int) $how_we_test_id ) {
		continue;
	}
	if ( 'trash' !== $post->post_status ) {
		wp_trash_post( $post->ID );
	}
}

$iphones_term = iphonebay_refresh_term( 'iphones' );
$samsung_term = iphonebay_refresh_term( 'samsung-phones' );
$pixel_term   = iphonebay_refresh_term( 'google-pixel' );
$access_term  = iphonebay_refresh_term( 'accessories' );

$primary_menu_id = iphonebay_refresh_sync_menu(
	'Primary Menu',
	array(
		array( 'title' => 'Home', 'type' => 'custom', 'url' => home_url( '/' ) ),
		array( 'title' => 'Shop', 'type' => 'post_type', 'object_id' => $shop_id ),
		array( 'title' => 'Categories', 'type' => 'custom', 'url' => home_url( '/#categories' ) ),
		array( 'title' => 'Trade-In', 'type' => 'post_type', 'object_id' => $trade_id ),
		array( 'title' => 'About', 'type' => 'post_type', 'object_id' => $about_id ),
		array( 'title' => 'Contact', 'type' => 'post_type', 'object_id' => $contact_id ),
	)
);

$footer_products_id = iphonebay_refresh_sync_menu(
	'Footer Products',
	array(
		array( 'title' => 'iPhones', 'type' => 'taxonomy', 'object_id' => $iphones_term ? $iphones_term->term_id : 0 ),
		array( 'title' => 'Samsung', 'type' => 'taxonomy', 'object_id' => $samsung_term ? $samsung_term->term_id : 0 ),
		array( 'title' => 'Google Pixel', 'type' => 'taxonomy', 'object_id' => $pixel_term ? $pixel_term->term_id : 0 ),
		array( 'title' => 'Accessories', 'type' => 'taxonomy', 'object_id' => $access_term ? $access_term->term_id : 0 ),
	)
);

$footer_support_id = iphonebay_refresh_sync_menu(
	'Footer Support',
	array(
		array( 'title' => 'Track My Order', 'type' => 'post_type', 'object_id' => $track_id ),
		array( 'title' => 'Trade-In', 'type' => 'post_type', 'object_id' => $trade_id ),
		array( 'title' => 'Contact Us', 'type' => 'post_type', 'object_id' => $contact_id ),
		array( 'title' => 'FAQs', 'type' => 'post_type', 'object_id' => $faq_id ),
	)
);

$footer_company_id = iphonebay_refresh_sync_menu(
	'Footer Company',
	array(
		array( 'title' => 'About iPhoneBayKE', 'type' => 'post_type', 'object_id' => $about_id ),
		array( 'title' => 'How We Test', 'type' => 'custom', 'url' => get_permalink( $how_we_test_id ) ),
		array( 'title' => 'Privacy Policy', 'type' => 'post_type', 'object_id' => $privacy_id ),
		array( 'title' => 'Terms of Service', 'type' => 'post_type', 'object_id' => $terms_id ),
	)
);

iphonebay_refresh_assign_menu_location( 'primary', $primary_menu_id );
iphonebay_refresh_assign_menu_location( 'footer_products', $footer_products_id );
iphonebay_refresh_assign_menu_location( 'footer_support', $footer_support_id );
iphonebay_refresh_assign_menu_location( 'footer_company', $footer_company_id );

echo "Site structure refreshed.\n";
