<?php
/**
 * Sync the IphoneBay reference homepage into the local WordPress site.
 *
 * Run with:
 * wp eval-file wp-content/themes/iphonebay/tools/sync-reference-homepage.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this script with wp eval-file.\n" );
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$iphonebay_reference_assets = '/Users/muchiriandrew/myprojects/iphonebay assets';

if ( ! is_dir( $iphonebay_reference_assets ) ) {
	exit( "Reference asset directory not found: {$iphonebay_reference_assets}\n" );
}

function iphonebay_sync_log( $message ) {
	echo $message . "\n";
}

function iphonebay_sync_attachment( $base_dir, $relative_path, $title, $alt = '' ) {
	global $wpdb;

	$source_path = wp_normalize_path( trailingslashit( $base_dir ) . ltrim( $relative_path, '/\\' ) );
	$source_path = realpath( $source_path );

	if ( ! $source_path || ! file_exists( $source_path ) ) {
		throw new RuntimeException( 'Missing reference asset: ' . $relative_path );
	}

	$existing_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_iphonebay_reference_asset' AND meta_value = %s LIMIT 1",
			wp_normalize_path( $source_path )
		)
	);

	if ( $existing_id ) {
		if ( $alt ) {
			update_post_meta( $existing_id, '_wp_attachment_image_alt', $alt );
		}
		return $existing_id;
	}

	$upload = wp_upload_bits( wp_basename( $source_path ), null, file_get_contents( $source_path ) );
	if ( ! empty( $upload['error'] ) ) {
		throw new RuntimeException( 'Upload failed for ' . $relative_path . ': ' . $upload['error'] );
	}

	$filetype = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, '_iphonebay_reference_asset', wp_normalize_path( $source_path ) );

	if ( $alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	}

	return $attachment_id;
}

function iphonebay_sync_page( $title, $slug, $content = '' ) {
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
		wp_update_post( $args );
		return (int) $page->ID;
	}

	return (int) wp_insert_post( $args );
}

function iphonebay_sync_reference_post( $post_type, $reference_key, $post_args, $meta = array() ) {
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => '_iphonebay_reference_key',
			'meta_value'     => $reference_key,
		)
	);

	$post_args['post_type']   = $post_type;
	$post_args['post_status'] = isset( $post_args['post_status'] ) ? $post_args['post_status'] : 'publish';

	if ( $existing ) {
		$post_args['ID'] = $existing[0]->ID;
		$post_id = wp_update_post( $post_args );
	} else {
		$post_id = wp_insert_post( $post_args );
	}

	update_post_meta( $post_id, '_iphonebay_reference_key', $reference_key );
	foreach ( $meta as $meta_key => $meta_value ) {
		update_post_meta( $post_id, $meta_key, $meta_value );
	}

	return (int) $post_id;
}

function iphonebay_sync_menu( $menu_name, $items ) {
	$menu = wp_get_nav_menu_object( $menu_name );
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
			'menu-item-type'   => isset( $item['type'] ) ? $item['type'] : 'custom',
		);

		if ( 'post_type' === $args['menu-item-type'] ) {
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = (int) $item['object_id'];
		} elseif ( 'taxonomy' === $args['menu-item-type'] ) {
			$args['menu-item-object']    = 'product_cat';
			$args['menu-item-object-id'] = (int) $item['object_id'];
		} else {
			$args['menu-item-url'] = $item['url'];
		}

		wp_update_nav_menu_item( $menu_id, 0, $args );
	}

	return $menu_id;
}

function iphonebay_assign_menu_location( $location, $menu_id ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = (int) $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

function iphonebay_term_by_slug( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) ) ? $term : null;
}

$shop_page_id    = iphonebay_sync_page( 'Shop', 'shop' );
$contact_page_id = iphonebay_sync_page( 'Contact Us', 'contact-us' );
$about_page_id   = iphonebay_sync_page( 'About', 'about' );
$track_page_id   = iphonebay_sync_page( 'Track Order', 'order-tracking' );
$trade_page_id   = iphonebay_sync_page( 'Trade-In', 'trade-in', 'Ready to upgrade? Share your current device details below and we will send a trade-in quote.' );
$privacy_page_id = iphonebay_sync_page( 'Privacy Policy', 'privacy-policy', 'Privacy policy content will be added here.' );
$terms_page_id   = iphonebay_sync_page( 'Terms of Service', 'terms-of-service', 'Terms of service content will be added here.' );
$sell_page_id    = iphonebay_sync_page( 'Sell to Us', 'sell-to-us', 'Share your device details and our team will get back to you with an offer.' );
$blog_page_id    = iphonebay_sync_page( 'Blog', 'blog', 'Store guides and behind-the-scenes notes live here.' );

$shop_url    = get_permalink( $shop_page_id );
$contact_url = get_permalink( $contact_page_id );
$about_url   = get_permalink( $about_page_id );
$tradein_url = get_permalink( $trade_page_id );
$how_we_test = get_page_by_path( 'how-we-test-battery-life', OBJECT, 'post' );
$how_we_test_url = $how_we_test ? get_permalink( $how_we_test ) : get_permalink( $blog_page_id );

$logo_id = iphonebay_sync_attachment( $iphonebay_reference_assets, 'iphonebay_logo_variant_1.png', 'IphoneBayKE Logo', 'IphoneBayKE' );
set_theme_mod( 'custom_logo', $logo_id );

$tradein_image_id  = iphonebay_sync_attachment( $iphonebay_reference_assets, 'AIgenerated/89gske89gske89gs (14).png', 'Trade-In Hero', 'Trade in your phone' );
$delivery_image_id = iphonebay_sync_attachment( $iphonebay_reference_assets, 'AIgenerated/89gske89gske89gs (11).png', 'Delivery and Payment', 'Fast delivery across Kenya' );

$accessories = iphonebay_term_by_slug( 'accessories' );
if ( ! $accessories ) {
	$created = wp_insert_term( 'Accessories', 'product_cat', array( 'slug' => 'accessories' ) );
	$accessories = ! is_wp_error( $created ) ? get_term( $created['term_id'], 'product_cat' ) : null;
}

$category_images = array(
	'iphones'        => array( 'AIgenerated/Image_89gske89gske89gs (1).png', 'iPhones' ),
	'samsung-phones' => array( 'AIgenerated/89gske89gske89gs (6).png', 'Samsung' ),
	'accessories'    => array( 'AIgenerated/89gske89gske89gs (9).png', 'Accessories' ),
	'google-pixel'   => array( 'AIgenerated/89gske89gske89gs (7).png', 'Google Pixel' ),
);

foreach ( $category_images as $slug => $config ) {
	$term = iphonebay_term_by_slug( $slug );
	if ( ! $term ) {
		continue;
	}

	$image_id = iphonebay_sync_attachment( $iphonebay_reference_assets, $config[0], $config[1], $config[1] );
	update_term_meta( $term->term_id, 'thumbnail_id', $image_id );
}

$theme_mods = array(
	'iphonebay_tradein_eyebrow' => 'Trade-In',
	'iphonebay_tradein_title'   => "Your old phone is *worth more*\nthan a drawer",
	'iphonebay_tradein_desc'    => 'Trade in the phone sitting in your drawer and put the value towards your next one. Example: an iPhone 12 64GB in good condition currently fetches up to KES 27,000 — paid to your M-Pesa the same day we verify it.',
	'iphonebay_tradein_step1'   => 'Tell us your phone model & condition',
	'iphonebay_tradein_step2'   => 'Get an instant quote — no obligation',
	'iphonebay_tradein_step3'   => 'Drop off or courier, get paid via M-Pesa',
	'iphonebay_tradein_btn_text'=> 'Get a Quote',
	'iphonebay_tradein_btn_url' => $tradein_url,
	'iphonebay_tradein_image'   => $tradein_image_id,
	'iphonebay_delivery_title'  => "Delivery &\nPayment",
	'iphonebay_delivery_desc'   => 'We deliver anywhere in Kenya. Same-day delivery within Nairobi CBD and estates. Next-day for Mombasa, Kisumu, Eldoret, and all major towns. Free delivery on orders above KES 50,000.',
	'iphonebay_delivery_image'  => $delivery_image_id,
	'iphonebay_best_title'      => "Best\n*Sellers*",
	'iphonebay_new_title'       => "New\n*Arrivals*",
	'iphonebay_deals_title'     => "Deals &\n*Offers*",
	'iphonebay_footer_desc'     => "Kenya's trusted source for genuine iPhones, Samsung, and premium smartphones. Ex-UK, Brand New, and Refurbished — all verified.",
);

foreach ( $theme_mods as $key => $value ) {
	set_theme_mod( $key, $value );
}

$hero_slides = array(
	array(
		'key'    => 'reference-premium-iphones',
		'title'  => "Premium\n*iPhones*\nDelivered",
		'order'  => 0,
		'image'  => 'AIgenerated/Image_89gske89gske89gs (1).png',
		'eyebrow'=> 'New Season Drop',
		'sub'    => 'Ex-UK & Brand New iPhones. Every model. Every colour. Verified genuine, battery health guaranteed.',
		'btn1'   => 'Shop iPhones',
		'url1'   => get_term_link( iphonebay_term_by_slug( 'iphones' ) ),
		'btn2'   => 'View All Deals',
		'url2'   => add_query_arg( 'collection', 'deals', $shop_url ),
	),
	array(
		'key'    => 'reference-battery-health',
		'title'  => "Battery\n*Health*\nVerified",
		'order'  => 1,
		'image'  => 'AIgenerated/Image_89gske89gske89gs (2).png',
		'eyebrow'=> 'Certified Quality',
		'sub'    => 'Every refurbished phone ships with a full battery health report. No surprises — only transparency.',
		'btn1'   => 'Shop Refurbished',
		'url1'   => add_query_arg( 'filter_condition', 'refurbished', $shop_url ),
		'btn2'   => 'How We Test',
		'url2'   => $how_we_test_url,
	),
	array(
		'key'    => 'reference-samsung-collection',
		'title'  => "Samsung\n*Galaxy*\nSeries",
		'order'  => 2,
		'image'  => 'AIgenerated/89gske89gske89gs (6).png',
		'eyebrow'=> 'Samsung Collection',
		'sub'    => 'The full Galaxy lineup — S-series, A-series, and foldables. Top-tier Android at Kenyan prices.',
		'btn1'   => 'Shop Samsung',
		'url1'   => get_term_link( iphonebay_term_by_slug( 'samsung-phones' ) ),
		'btn2'   => 'Compare Models',
		'url2'   => get_term_link( iphonebay_term_by_slug( 'samsung-phones' ) ),
	),
	array(
		'key'    => 'reference-trade-in',
		'title'  => "Trade In,\n*Upgrade*\nToday",
		'order'  => 3,
		'image'  => 'AIgenerated/89gske89gske89gs (14).png',
		'eyebrow'=> 'Trade-In Program',
		'sub'    => 'Get instant value for your old phone. Trade-in in 3 simple steps. M-Pesa payout or store credit.',
		'btn1'   => 'Start Trade-In',
		'url1'   => $tradein_url,
		'btn2'   => 'Get a Quote',
		'url2'   => add_query_arg( 'topic', 'trade-in', $contact_url ),
	),
);

$hero_ids = array();
foreach ( $hero_slides as $slide ) {
	$image_id = iphonebay_sync_attachment( $iphonebay_reference_assets, $slide['image'], $slide['eyebrow'], $slide['eyebrow'] );
	$post_id = iphonebay_sync_reference_post(
		'hero_slide',
		$slide['key'],
		array(
			'post_title' => $slide['title'],
			'menu_order' => $slide['order'],
		),
		array(
			'iphonebay_eyebrow'   => $slide['eyebrow'],
			'iphonebay_subtitle'  => $slide['sub'],
			'iphonebay_btn1_text' => $slide['btn1'],
			'iphonebay_btn1_url'  => $slide['url1'],
			'iphonebay_btn2_text' => $slide['btn2'],
			'iphonebay_btn2_url'  => $slide['url2'],
		)
	);
	set_post_thumbnail( $post_id, $image_id );
	$hero_ids[] = $post_id;
}

$testimonials = array(
	array(
		'key'      => 'reference-review-wanjiku',
		'title'    => 'Wanjiku K.',
		'content'  => 'Ordered an iPhone 14 Ex-UK on a Wednesday, it arrived Thursday morning. Battery at 91% as described and the phone felt brand new. Will definitely buy again.',
		'order'    => 0,
		'author'   => 'Wanjiku K.',
		'location' => 'Westlands, Nairobi',
	),
	array(
		'key'      => 'reference-review-brian',
		'title'    => 'Brian M.',
		'content'  => 'Best place to buy phones in Nairobi. Got my Samsung S23 at a price 20K cheaper than everywhere else. Legit, fast, and M-Pesa checkout is seamless.',
		'order'    => 1,
		'author'   => 'Brian M.',
		'location' => 'Thika Road, Nairobi',
	),
	array(
		'key'      => 'reference-review-amina',
		'title'    => 'Amina O.',
		'content'  => 'The trade-in process was incredibly smooth. Got KES 32,000 for my old iPhone 12 and used it towards an iPhone 15. Whole thing took less than 30 minutes.',
		'order'    => 2,
		'author'   => 'Amina O.',
		'location' => 'Kilimani, Nairobi',
	),
);

foreach ( $testimonials as $testimonial ) {
	iphonebay_sync_reference_post(
		'testimonial',
		$testimonial['key'],
		array(
			'post_title'   => $testimonial['title'],
			'post_content' => $testimonial['content'],
			'menu_order'   => $testimonial['order'],
		),
		array(
			'iphonebay_author'   => $testimonial['author'],
			'iphonebay_location' => $testimonial['location'],
			'iphonebay_rating'   => 5,
		)
	);
}

$iphones_term  = iphonebay_term_by_slug( 'iphones' );
$samsung_term  = iphonebay_term_by_slug( 'samsung-phones' );
$pixel_term    = iphonebay_term_by_slug( 'google-pixel' );

$main_menu_id = iphonebay_sync_menu(
	'Main Menu',
	array(
		array( 'title' => 'Home', 'url' => home_url( '/' ) ),
		array( 'title' => 'Shop', 'type' => 'post_type', 'object_id' => $shop_page_id ),
		array( 'title' => 'Categories', 'url' => home_url( '/#categories' ) ),
		array( 'title' => 'Trade-In', 'type' => 'post_type', 'object_id' => $trade_page_id ),
		array( 'title' => 'About', 'type' => 'post_type', 'object_id' => $about_page_id ),
		array( 'title' => 'Contact', 'type' => 'post_type', 'object_id' => $contact_page_id ),
	)
);

$footer_products_id = iphonebay_sync_menu(
	'Footer Products',
	array(
		array( 'title' => 'iPhones', 'type' => $iphones_term ? 'taxonomy' : 'custom', 'object_id' => $iphones_term ? $iphones_term->term_id : 0, 'url' => $iphones_term ? '' : $shop_url ),
		array( 'title' => 'Samsung', 'type' => $samsung_term ? 'taxonomy' : 'custom', 'object_id' => $samsung_term ? $samsung_term->term_id : 0, 'url' => $samsung_term ? '' : $shop_url ),
		array( 'title' => 'Google Pixel', 'type' => $pixel_term ? 'taxonomy' : 'custom', 'object_id' => $pixel_term ? $pixel_term->term_id : 0, 'url' => $pixel_term ? '' : $shop_url ),
		array( 'title' => 'Smart Watches', 'url' => $shop_url ),
		array( 'title' => 'AirPods', 'url' => $shop_url ),
	)
);

$footer_support_id = iphonebay_sync_menu(
	'Footer Support',
	array(
		array( 'title' => 'Track My Order', 'type' => 'post_type', 'object_id' => $track_page_id ),
		array( 'title' => 'Trade-In', 'type' => 'post_type', 'object_id' => $trade_page_id ),
		array( 'title' => 'Contact Us', 'type' => 'post_type', 'object_id' => $contact_page_id ),
		array( 'title' => 'FAQs', 'type' => 'post_type', 'object_id' => iphonebay_sync_page( 'FAQs', 'faqs' ) ),
	)
);

$footer_company_id = iphonebay_sync_menu(
	'Footer Company',
	array(
		array( 'title' => 'About IphoneBayKE', 'type' => 'post_type', 'object_id' => $about_page_id ),
		array( 'title' => 'How We Test', 'url' => $how_we_test_url ),
		array( 'title' => 'Privacy Policy', 'type' => 'post_type', 'object_id' => $privacy_page_id ),
		array( 'title' => 'Terms of Service', 'type' => 'post_type', 'object_id' => $terms_page_id ),
	)
);

iphonebay_assign_menu_location( 'primary', $main_menu_id );
iphonebay_assign_menu_location( 'footer_products', $footer_products_id );
iphonebay_assign_menu_location( 'footer_support', $footer_support_id );
iphonebay_assign_menu_location( 'footer_company', $footer_company_id );

update_option( 'wp_page_for_privacy_policy', $privacy_page_id );

iphonebay_sync_log( 'Reference homepage content synced.' );
