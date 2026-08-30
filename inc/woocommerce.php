<?php
/**
 * WooCommerce integration.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* WordPress block-theme globals are not part of this custom storefront. */
add_action( 'after_setup_theme', function () {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
	remove_action( 'wp_footer', 'wp_global_styles_render_svg_filters' );
} );

/* Own the styling: drop WooCommerce's default skin/layout/smallscreen CSS. */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* Drop WooCommerce / block-theme CSS that keeps reintroducing plugin-level
 * resets over the custom theme's own design system. */
function iphonebay_strip_plugin_frontend_styles() {
	$handles = array(
		'wc-blocks-style',
		'wc-blocks-vendors-style',
		'wc-block-style',
		'wc-blocks-packages-style',
		'wp-block-library',
		'wp-block-library-theme',
		'classic-theme-styles',
		'global-styles',
	);

	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'iphonebay_strip_plugin_frontend_styles', 100 );
add_action( 'wp_print_styles', 'iphonebay_strip_plugin_frontend_styles', 100 );

/* Products per page & columns. */
add_filter( 'loop_shop_per_page', function () { return 12; }, 20 );
add_filter( 'loop_shop_columns', function () { return 3; }, 20 );

/* Related & up-sell counts. */
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );
add_filter( 'woocommerce_upsells_total', function () { return 4; } );

/* Content wrappers for single product / cart / checkout (not shop archive). */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', function () {
	echo '<main class="site-main woo-main"><div class="woo-container">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div></main>';
}, 10 );

/* Remove the default WooCommerce sidebar. */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/* The custom single-product header badge already covers sale messaging. */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/* Re-emit the order review heading INSIDE the #order_review panel so it sits
   above the table rather than floating as an orphaned grid element. */
add_action( 'woocommerce_checkout_before_order_review', function () {
	echo '<h2 class="order-review-title">' . esc_html__( 'Your order', 'iphonebay' ) . '</h2>';
}, 5 );

/* Remove the Reviews tab from all single product pages. */
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	unset( $tabs['reviews'] );
	return $tabs;
}, 98 );

/* Remove "Have a coupon?" toggle and add-to-cart notices — user explicitly wants no banners. */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_filter( 'wc_add_to_cart_message_html', '__return_empty_string' );
add_filter( 'woocommerce_coupons_enabled', function ( $enabled ) {
	return ( is_cart() || is_checkout() ) ? false : $enabled;
} );

/* Keep login + registration available from the account screen. */
add_filter( 'option_woocommerce_enable_myaccount_registration', function () {
	return 'yes';
} );

/* Shared thank-you panel used by the theme's WooCommerce template override. */
function iphonebay_render_thankyou_panel( $order_id ) {
	$order      = $order_id ? wc_get_order( $order_id ) : false;
	$track_page = get_page_by_path( 'order-tracking' );
	$track_url  = $track_page instanceof WP_Post ? get_permalink( $track_page ) : home_url( '/order-tracking/' );
	$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$whatsapp   = preg_replace( '/\D+/', '', (string) iphonebay_opt( 'whatsapp' ) );
	?>
	<section class="thankyou-next-steps" aria-label="<?php esc_attr_e( 'What happens next', 'iphonebay' ); ?>">
		<div class="thankyou-next-steps__card">
			<p class="thankyou-next-steps__eyebrow"><?php esc_html_e( 'What happens next', 'iphonebay' ); ?></p>
			<h2 class="thankyou-next-steps__title"><?php esc_html_e( 'We have your order and our team is already preparing the next step.', 'iphonebay' ); ?></h2>
			<p class="thankyou-next-steps__copy">
				<?php
				if ( $order instanceof WC_Order ) {
					printf(
						/* translators: %s: order number */
						esc_html__( 'Order #%s has been recorded successfully. We will confirm availability, prepare dispatch, and share delivery updates as soon as your device is ready.', 'iphonebay' ),
						esc_html( $order->get_order_number() )
					);
				} else {
					esc_html_e( 'Your order has been recorded successfully. We will confirm availability, prepare dispatch, and share delivery updates as soon as your device is ready.', 'iphonebay' );
				}
				?>
			</p>
			<div class="thankyou-next-steps__actions">
				<a class="thankyou-next-steps__button thankyou-next-steps__button--primary" href="<?php echo esc_url( $track_url ); ?>"><?php esc_html_e( 'Track your order', 'iphonebay' ); ?></a>
				<a class="thankyou-next-steps__button" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Continue shopping', 'iphonebay' ); ?></a>
				<?php if ( $whatsapp ) : ?>
					<a class="thankyou-next-steps__button" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp ); ?>"><?php esc_html_e( 'WhatsApp support', 'iphonebay' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/* Sale badge text. */
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	return '<span class="onsale">' . esc_html__( 'Sale', 'iphonebay' ) . '</span>';
}, 10, 3 );

/* Variable products: "From KSh X" instead of the full price range. */
add_filter( 'woocommerce_variable_price_html', function ( $price_html, $product ) {
	$prices = $product->get_variation_prices( true );
	if ( empty( $prices['price'] ) ) {
		return $price_html;
	}
	$min = min( $prices['price'] );
	$max = max( $prices['price'] );
	if ( $min === $max ) {
		return wc_price( $min );
	}
	return '<span class="price-from-label">' . esc_html__( 'From', 'iphonebay' ) . '</span> ' . wc_price( $min );
}, 10, 2 );

/* M-Pesa: mark the gateway label so checkout can render it prominently. */
add_filter( 'woocommerce_gateway_icon', function ( $icon, $gateway_id ) {
	$gateways = function_exists( 'WC' ) && WC()->payment_gateways() ? WC()->payment_gateways()->payment_gateways() : array();
	if ( isset( $gateways[ $gateway_id ] ) && false !== stripos( (string) $gateways[ $gateway_id ]->title, 'm-pesa' ) ) {
		$icon .= '<span class="mpesa-cta-badge">' . esc_html__( 'Most popular', 'iphonebay' ) . '</span>';
	}
	return $icon;
}, 10, 2 );

/* Friendly empty-cart copy. */
add_filter( 'wc_empty_cart_message', function () {
	return esc_html__( 'Your cart is empty — for now. Browse our Ex-UK iPhones and find your next phone.', 'iphonebay' );
} );

/* Trust reassurance directly above the Place Order button. */
add_action( 'woocommerce_review_order_before_submit', function () {
	?>
	<div class="checkout-reassure" role="note">
		<div class="checkout-reassure-item">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
			<span><?php esc_html_e( '30-point inspected before dispatch', 'iphonebay' ); ?></span>
		</div>
		<div class="checkout-reassure-item">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
			<span><?php esc_html_e( '6-month warranty on every device', 'iphonebay' ); ?></span>
		</div>
		<div class="checkout-reassure-item">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
			<span><?php esc_html_e( 'Pay securely — M-Pesa, bank or card', 'iphonebay' ); ?></span>
		</div>
	</div>
	<?php
} );

/* Breadcrumb delimiter to match design. */
add_filter( 'woocommerce_breadcrumb_defaults', function ( $defaults ) {
	$defaults['delimiter']   = '<span class="breadcrumb-sep">/</span>';
	$defaults['wrap_before'] = '<nav class="breadcrumb woo-breadcrumb" aria-label="Breadcrumb">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
} );

/* ============================================================
 * SINGLE PRODUCT enhancements (layout via hooks + CSS, swatches via JS)
 * ============================================================ */

/* Condition badge above the title. */
add_action( 'woocommerce_single_product_summary', 'iphonebay_single_badge', 4 );
function iphonebay_single_badge() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$cond = iphonebay_product_condition( $product );
	if ( $cond['label'] ) {
		echo '<span class="sp-badge product-badge ' . esc_attr( $cond['class'] ) . '">' . esc_html( $cond['label'] ) . '</span>';
	}
}

/* Trust mini-grid after the add-to-cart area. */
add_action( 'woocommerce_single_product_summary', 'iphonebay_single_trust', 35 );
function iphonebay_single_trust() {
	$items = array(
		array( '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', __( 'Genuine Device', 'iphonebay' ), __( 'IMEI verified, not a clone', 'iphonebay' ) ),
		array( '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>', __( '6-Month Warranty', 'iphonebay' ), __( 'Hardware faults covered', 'iphonebay' ) ),
		array( '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>', __( 'M-Pesa Accepted', 'iphonebay' ), __( 'Instant confirmation', 'iphonebay' ) ),
		array( '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="m16 8 5 2v6h-5V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', __( 'Same-Day Delivery', 'iphonebay' ), __( 'Nairobi CBD & estates', 'iphonebay' ) ),
	);
	echo '<div class="sp-trust">';
	foreach ( $items as $it ) {
		echo '<div class="sp-trust-item"><span class="sp-trust-icon">' . $it[0] . '</span><span class="sp-trust-text"><strong>' . esc_html( $it[1] ) . '</strong><span>' . esc_html( $it[2] ) . '</span></span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

/**
 * Colour name → hex map for variation colour swatches.
 * Passed to JS via wp_localize so swatches can render real colour dots.
 */
function iphonebay_colour_map() {
	return apply_filters( 'iphonebay_colour_map', array(
		'black'        => '#1a1a1a',
		'space black'  => '#1d1d1f',
		'midnight'     => '#1f2430',
		'graphite'     => '#54524f',
		'space gray'   => '#5c5b57',
		'space grey'   => '#5c5b57',
		'white'        => '#f5f5f0',
		'starlight'    => '#faf6ef',
		'silver'       => '#d8dadb',
		'gold'         => '#e3c8a1',
		'rose gold'    => '#ecc7c0',
		'pink'         => '#e8b9c2',
		'red'          => '#bd2231',
		'product red'  => '#bd2231',
		'blue'         => '#a4c6dd',
		'sierra blue'  => '#9fb6cd',
		'pacific blue' => '#2e5b76',
		'blue titanium'=> '#5d6b7e',
		'green'        => '#a6c0a6',
		'alpine green' => '#576b5d',
		'teal'         => '#9fc6c5',
		'yellow'       => '#f3d56b',
		'purple'       => '#cdc4e0',
		'deep purple'  => '#5a5570',
		'natural titanium' => '#bcb6ab',
		'white titanium'   => '#ecebe7',
		'black titanium'   => '#33312f',
		'desert titanium'  => '#bfa48f',
	) );
}

/**
 * Render the shop filter sidebar (native layered-nav GET form).
 * Used by archive-product.php.
 */
function iphonebay_shop_filters() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	?>
	<aside class="shop-sidebar" id="shopSidebar" aria-label="<?php esc_attr_e( 'Filters', 'iphonebay' ); ?>">
		<form class="sidebar-inner" method="get" action="<?php echo esc_url( $shop_url ); ?>">
			<div class="sidebar-head">
				<span class="sidebar-head-title"><?php esc_html_e( 'Filters', 'iphonebay' ); ?></span>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="sidebar-clear"><?php esc_html_e( 'Clear all', 'iphonebay' ); ?></a>
				<button class="sidebar-close" type="button" onclick="iphonebayCloseFilters()" aria-label="<?php esc_attr_e( 'Close filters', 'iphonebay' ); ?>">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
				</button>
			</div>

			<?php
			// Attribute filter groups (condition, storage) via layered nav.
			$attributes = array(
				'condition' => __( 'Condition', 'iphonebay' ),
				'storage'   => __( 'Storage', 'iphonebay' ),
			);
			$first = true;
			foreach ( $attributes as $slug => $label ) {
				$taxonomy = 'pa_' . $slug;
				if ( ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}
				$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					continue;
				}
				// WooCommerce layered nav sends a comma-separated string; our checkboxes
				// can also submit an array. Normalise both to an array of slugs.
				$chosen = array();
				if ( isset( $_GET[ 'filter_' . $slug ] ) ) {
					$raw = wp_unslash( $_GET[ 'filter_' . $slug ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$raw = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
					$chosen = array_filter( array_map( 'sanitize_title', (array) $raw ) );
				}
				printf(
					'<div class="filter-group"%s><div class="filter-group-title">%s</div><div class="filter-options">',
					$first ? ' style="border-top:none;padding-top:0;"' : '',
					esc_html( $label )
				);
				foreach ( $terms as $term ) {
					$checked = in_array( $term->slug, $chosen, true );
					echo '<label class="filter-opt"><input type="checkbox" name="filter_' . esc_attr( $slug ) . '[]" value="' . esc_attr( $term->slug ) . '"' . checked( $checked, true, false ) . '><span class="filter-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>' . esc_html( $term->name ) . '<span class="filter-opt-count">' . esc_html( $term->count ) . '</span></label>';
				}
				echo '</div></div>';
				$first = false;
			}

			// Category links (brand).
			$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0 ) );
			if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
				echo '<div class="filter-group"><div class="filter-group-title">' . esc_html__( 'Category', 'iphonebay' ) . '</div><div class="filter-options">';
				foreach ( $cats as $cat ) {
					echo '<a class="filter-opt filter-opt-link" href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '<span class="filter-opt-count">' . esc_html( $cat->count ) . '</span></a>';
				}
				echo '</div></div>';
			}
			?>

			<div class="filter-group">
				<div class="filter-group-title"><?php esc_html_e( 'Price (KES)', 'iphonebay' ); ?></div>
				<div class="price-inputs">
					<div class="price-field"><label for="min_price"><?php esc_html_e( 'Min', 'iphonebay' ); ?></label><input id="min_price" name="min_price" type="number" min="0" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( absint( $_GET['min_price'] ) ) : ''; ?>" placeholder="0"></div>
					<span class="price-dash">&ndash;</span>
					<div class="price-field"><label for="max_price"><?php esc_html_e( 'Max', 'iphonebay' ); ?></label><input id="max_price" name="max_price" type="number" min="0" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( absint( $_GET['max_price'] ) ) : ''; ?>" placeholder="200,000"></div>
				</div>
			</div>

			<button type="submit" class="filter-apply"><?php esc_html_e( 'Apply Filters', 'iphonebay' ); ?></button>
		</form>
	</aside>
	<?php
}
