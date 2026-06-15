<?php
/**
 * Shared template helpers — product card, carousels, product queries.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine a product's condition badge (from a "condition" attribute).
 *
 * @return array{label:string,class:string}
 */
function iphonebay_product_condition( $product ) {
	$label = '';
	$class = 'badge-exuk';

	if ( function_exists( 'wc_get_product_terms' ) ) {
		$terms = wc_get_product_terms( $product->get_id(), 'pa_condition', array( 'fields' => 'names' ) );
		if ( ! empty( $terms ) ) {
			$label = $terms[0];
		}
	}

	if ( '' === $label ) {
		if ( $product->is_on_sale() ) {
			return array( 'label' => __( 'Deal', 'iphonebay' ), 'class' => 'badge-new' );
		}
		return array( 'label' => '', 'class' => '' );
	}

	$key = strtolower( $label );
	if ( false !== strpos( $key, 'new' ) ) {
		$class = 'badge-new';
	} elseif ( false !== strpos( $key, 'refurb' ) ) {
		$class = 'badge-refurb';
	} else {
		$class = 'badge-exuk';
	}

	return array( 'label' => $label, 'class' => $class );
}

/**
 * Short meta line for a card, e.g. "128GB · Ex-UK".
 */
function iphonebay_product_meta_line( $product ) {
	$parts = array();

	if ( function_exists( 'wc_get_product_terms' ) ) {
		$storage = wc_get_product_terms( $product->get_id(), 'pa_storage', array( 'fields' => 'names' ) );
		if ( ! empty( $storage ) ) {
			$parts[] = $storage[0];
		}
		$condition = wc_get_product_terms( $product->get_id(), 'pa_condition', array( 'fields' => 'names' ) );
		if ( ! empty( $condition ) ) {
			$parts[] = $condition[0];
		}
	}

	if ( empty( $parts ) ) {
		$cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
			$parts[] = $cats[0];
		}
	}

	return implode( ' &middot; ', array_map( 'esc_html', $parts ) );
}

/**
 * Get the canonical shop URL.
 *
 * @param array<string, scalar|array> $args Optional query args.
 * @return string
 */
function iphonebay_shop_url( $args = array() ) {
	$url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	if ( empty( $args ) ) {
		return $url;
	}

	return add_query_arg( $args, $url );
}

/**
 * Get a page URL by slug with a fallback.
 *
 * @param string $slug Page slug.
 * @param string $fallback Fallback URL.
 * @return string
 */
function iphonebay_get_core_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );

	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return $fallback ? $fallback : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Collection labels used on the homepage and shop archive.
 *
 * @return array<string, array<string, string>>
 */
function iphonebay_shop_collection_map() {
	return array(
		'best'  => array(
			'label' => __( 'Best Sellers', 'iphonebay' ),
			'copy'  => __( 'Our most-purchased devices right now.', 'iphonebay' ),
		),
		'new'   => array(
			'label' => __( 'New Arrivals', 'iphonebay' ),
			'copy'  => __( 'The freshest additions to the catalog.', 'iphonebay' ),
		),
		'deals' => array(
			'label' => __( 'Deals & Offers', 'iphonebay' ),
			'copy'  => __( 'Devices currently on offer or marked down.', 'iphonebay' ),
		),
	);
}

/**
 * Get the current collection slug from the query.
 *
 * @return string
 */
function iphonebay_current_shop_collection() {
	$collection = get_query_var( 'collection' );

	if ( ! $collection && isset( $_GET['collection'] ) ) {
		$collection = wp_unslash( $_GET['collection'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return sanitize_key( (string) $collection );
}

/**
 * Build a collection archive URL for the shop.
 *
 * @param string $collection Collection slug.
 * @param array<string, scalar|array> $args Extra args.
 * @return string
 */
function iphonebay_collection_url( $collection, $args = array() ) {
	return iphonebay_shop_url( array_merge( array( 'collection' => sanitize_key( $collection ) ), $args ) );
}

/**
 * Build a category URL that stays inside the custom shop archive.
 *
 * @param string $slug Product category slug.
 * @return string
 */
function iphonebay_product_category_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( $term && ! is_wp_error( $term ) ) {
		return get_term_link( $term );
	}

	return iphonebay_shop_url();
}

/**
 * Get the article URL for the battery-testing explainer.
 *
 * @return string
 */
function iphonebay_how_we_test_url() {
	$post = get_page_by_path( 'how-we-test-battery-life', OBJECT, 'post' );

	if ( $post instanceof WP_Post ) {
		return get_permalink( $post );
	}

	return iphonebay_get_core_page_url( 'blog', home_url( '/blog/' ) );
}

/**
 * Build the contact page URL with a pre-selected topic.
 *
 * @param string $topic Contact topic.
 * @return string
 */
function iphonebay_contact_url( $topic = 'general' ) {
	return add_query_arg(
		array( 'topic' => sanitize_key( $topic ) ),
		iphonebay_get_core_page_url( 'contact-us', home_url( '/contact-us/' ) )
	);
}

/**
 * Resolve CTA labels to meaningful destinations.
 *
 * @param string $label CTA text.
 * @param string $fallback Explicit fallback URL if set in the backend.
 * @return string
 */
function iphonebay_resolve_cta_url( $label, $fallback = '' ) {
	$label_key = strtolower( trim( wp_strip_all_tags( $label ) ) );

	if ( $fallback && '#' !== $fallback ) {
		return $fallback;
	}

	if ( false !== strpos( $label_key, 'deal' ) ) {
		return iphonebay_collection_url( 'deals' );
	}

	if ( false !== strpos( $label_key, 'best' ) ) {
		return iphonebay_collection_url( 'best' );
	}

	if ( false !== strpos( $label_key, 'new arrival' ) ) {
		return iphonebay_collection_url( 'new' );
	}

	if ( false !== strpos( $label_key, 'quote' ) || false !== strpos( $label_key, 'trade-in' ) || false !== strpos( $label_key, 'trade in' ) ) {
		return iphonebay_contact_url( 'trade-in' );
	}

	if ( false !== strpos( $label_key, 'test' ) ) {
		return iphonebay_how_we_test_url();
	}

	if ( false !== strpos( $label_key, 'shop iphone' ) ) {
		return iphonebay_product_category_url( 'iphones' );
	}

	if ( false !== strpos( $label_key, 'shop samsung' ) || false !== strpos( $label_key, 'compare' ) ) {
		return iphonebay_product_category_url( 'samsung-phones' );
	}

	if ( false !== strpos( $label_key, 'refurbished' ) ) {
		return iphonebay_shop_url( array( 'filter_condition' => 'refurbished' ) );
	}

	return $fallback ? $fallback : iphonebay_shop_url();
}

/**
 * Render the shared product card.
 *
 * @param WC_Product $product
 */
function iphonebay_product_card( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$permalink = get_permalink( $product->get_id() );
	$cond      = iphonebay_product_condition( $product );
	$meta      = iphonebay_product_meta_line( $product );
	$cart_url  = $product->add_to_cart_url();
	$img       = $product->get_image( 'iphonebay_card' );
	?>
	<article class="product-card">
		<div class="product-img-wrap">
			<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_kses_post( $img ); ?></a>
			<?php if ( $cond['label'] ) : ?>
				<span class="product-badge <?php echo esc_attr( $cond['class'] ); ?>"><?php echo esc_html( $cond['label'] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="product-body">
			<h3 class="product-name"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<?php if ( $meta ) : ?><p class="product-meta"><?php echo wp_kses_post( $meta ); ?></p><?php endif; ?>
			<div class="price-row"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<div class="card-actions">
				<a href="<?php echo esc_url( $permalink ); ?>" class="btn-view"><?php esc_html_e( 'View Details', 'iphonebay' ); ?></a>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="btn-cart-quick" aria-label="<?php esc_attr_e( 'Add to cart', 'iphonebay' ); ?>" <?php echo $product->is_type( 'simple' ) ? 'data-quantity="1" rel="nofollow"' : ''; ?>>
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="23" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
				</a>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Query products for a homepage row.
 *
 * @param string $type  best|new|deals
 * @param int    $limit
 * @return WC_Product[]
 */
function iphonebay_get_products( $type = 'new', $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$args = array(
		'status'  => 'publish',
		'limit'   => $limit,
		'visibility' => 'catalog',
	);

	switch ( $type ) {
		case 'best':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
			$args['order']    = 'DESC';
			break;
		case 'deals':
			$ids = wc_get_product_ids_on_sale();
			if ( empty( $ids ) ) {
				return array();
			}
			$args['include'] = $ids;
			break;
		case 'new':
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	return wc_get_products( $args );
}

/**
 * Render a full homepage product carousel section.
 *
 * @param string $title_raw  Title with *gold* syntax.
 * @param array  $products
 * @param string $view_all
 * @param string $eyebrow
 */
function iphonebay_render_carousel( $title_raw, $products, $view_all = '', $eyebrow = '', $band = false ) {
	if ( empty( $products ) ) {
		return;
	}
	static $i = 0;
	$i++;
	$vp_id = 'carouselVp' . $i;

	if ( $band ) {
		echo '<div class="section-band"><div class="section section-flush">';
	} else {
		echo '<div class="section">';
	}
	?>
		<div class="section-header">
			<div>
				<?php if ( $eyebrow ) : ?><div class="section-eyebrow"><span class="hero-dot-live"></span> <?php echo esc_html( $eyebrow ); ?></div><?php endif; ?>
				<h2 class="section-title"><?php echo wp_kses_post( iphonebay_highlight( $title_raw ) ); ?></h2>
			</div>
			<?php if ( $view_all ) : ?><a href="<?php echo esc_url( $view_all ); ?>" class="section-link"><?php esc_html_e( 'View All', 'iphonebay' ); ?> &rarr;</a><?php endif; ?>
		</div>
		<div class="row-carousel">
			<div class="carousel-viewport" id="<?php echo esc_attr( $vp_id ); ?>">
				<div class="carousel-row">
					<?php foreach ( $products as $product ) {
						iphonebay_product_card( $product );
					} ?>
				</div>
			</div>
			<div class="carousel-controls">
				<button class="carousel-arrow" data-dir="-1" aria-label="<?php esc_attr_e( 'Scroll left', 'iphonebay' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg></button>
				<button class="carousel-arrow" data-dir="1" aria-label="<?php esc_attr_e( 'Scroll right', 'iphonebay' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></button>
			</div>
		</div>
	<?php
	if ( $band ) {
		echo '</div></div>';
	} else {
		echo '</div>';
	}
}

/**
 * Social profiles with SVG icons.
 *
 * @return array<string, array<string, string>>
 */
function iphonebay_social_profiles() {
	return array(
		'tiktok'    => array(
			'url'   => iphonebay_opt( 'social_tiktok' ),
			'label' => __( 'TikTok', 'iphonebay' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.7 3c.4 1.9 1.6 3.4 3.3 4.2v2.8a7.5 7.5 0 0 1-3.4-1V15a5.4 5.4 0 1 1-5.4-5.4c.3 0 .6 0 .8.1v2.9a2.6 2.6 0 1 0 1.9 2.5V3h2.8Z"/></svg>',
		),
		'instagram' => array(
			'url'   => iphonebay_opt( 'social_instagram' ),
			'label' => __( 'Instagram', 'iphonebay' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7.5 3h9A4.5 4.5 0 0 1 21 7.5v9a4.5 4.5 0 0 1-4.5 4.5h-9A4.5 4.5 0 0 1 3 16.5v-9A4.5 4.5 0 0 1 7.5 3Zm0 1.8A2.7 2.7 0 0 0 4.8 7.5v9a2.7 2.7 0 0 0 2.7 2.7h9a2.7 2.7 0 0 0 2.7-2.7v-9a2.7 2.7 0 0 0-2.7-2.7h-9Zm9.8 1.4a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Z"/></svg>',
		),
		'whatsapp'  => array(
			'url'   => iphonebay_opt( 'whatsapp' ) ? 'https://wa.me/' . preg_replace( '/\D/', '', iphonebay_opt( 'whatsapp' ) ) : '',
			'label' => __( 'WhatsApp', 'iphonebay' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3.2A8.8 8.8 0 0 0 4.5 16l-1.1 4.8 4.9-1.1A8.8 8.8 0 1 0 12 3.2Zm0 1.8a7 7 0 0 1 5.9 10.8l-.3.4.7 3-3-.7-.4.2A7 7 0 1 1 12 5Zm-3.2 3.6c-.2 0-.5.1-.7.4-.3.3-.8.8-.8 2 0 1.1.8 2.3.9 2.5.1.2 1.6 2.6 4 3.6 1.9.8 2.3.7 2.7.7.4-.1 1.4-.6 1.6-1.2.2-.6.2-1 .1-1.2-.1-.1-.3-.2-.7-.4l-1.2-.6c-.2-.1-.5 0-.6.2l-.6.8c-.1.2-.3.2-.6.1-.3-.1-1.1-.4-2.1-1.3-.7-.6-1.2-1.4-1.3-1.6-.1-.3 0-.4.1-.6l.5-.6c.1-.2.2-.3.3-.5.1-.2.1-.4 0-.6l-.5-1.3c-.1-.3-.4-.5-.7-.5Z"/></svg>',
		),
		'facebook'  => array(
			'url'   => iphonebay_opt( 'social_facebook' ),
			'label' => __( 'Facebook', 'iphonebay' ),
			'icon'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.4 21v-7.8H16l.4-3h-3V8.3c0-.9.2-1.5 1.5-1.5h1.6V4.1c-.3 0-1.2-.1-2.2-.1-2.2 0-3.8 1.3-3.8 3.9v2.2H8v3h2.5V21h2.9Z"/></svg>',
		),
	);
}

/**
 * Social links list for footer / off-canvas.
 */
function iphonebay_social_links() {
	$out = '';
	foreach ( iphonebay_social_profiles() as $key => $profile ) {
		$url = $profile['url'] ? $profile['url'] : '#';
		$out .= '<a href="' . esc_url( $url ) . '" class="footer-soc" aria-label="' . esc_attr( $profile['label'] ) . '"' . ( $profile['url'] ? ' target="_blank" rel="noopener"' : '' ) . '>' . $profile['icon'] . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return $out;
}

/**
 * Build a public shop search URL for a product query.
 *
 * @param string $query Optional search term.
 * @return string
 */
function iphonebay_product_search_url( $query = '' ) {
	$args     = array( 'post_type' => 'product' );

	if ( '' !== trim( $query ) ) {
		$args['s'] = trim( $query );
	}

	return iphonebay_shop_url( $args );
}

/**
 * Format a product for the header search overlay.
 *
 * @param WC_Product $product Product object.
 * @return array<string, mixed>
 */
function iphonebay_search_product_payload( $product ) {
	$meta = html_entity_decode( wp_strip_all_tags( iphonebay_product_meta_line( $product ) ), ENT_QUOTES, 'UTF-8' );
	$cond = iphonebay_product_condition( $product );
	$image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );

	if ( ! $image ) {
		$image = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'woocommerce_thumbnail' ) : IPHONEBAY_URI . '/assets/images/logo.png';
	}

	return array(
		'id'         => $product->get_id(),
		'name'       => $product->get_name(),
		'url'        => get_permalink( $product->get_id() ),
		'image'      => $image,
		'meta'       => $meta,
		'price_html' => $product->get_price_html(),
		'badge'      => $cond['label'],
		'badgeClass' => $cond['class'],
	);
}

/**
 * Query products for the predictive search overlay.
 *
 * @param string $query Search term.
 * @param int    $limit Result limit.
 * @return array<int, array<string, mixed>>
 */
function iphonebay_find_search_products( $query = '', $limit = 8 ) {
	$limit = max( 1, min( 12, (int) $limit ) );
	$query = trim( wp_strip_all_tags( $query ) );
	$items = array();

	if ( '' === $query ) {
		foreach ( iphonebay_get_products( 'best', $limit ) as $product ) {
			if ( $product instanceof WC_Product ) {
				$items[] = iphonebay_search_product_payload( $product );
			}
		}
		return $items;
	}

	$post_ids = get_posts( array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => $limit,
		's'                   => $query,
		'fields'              => 'ids',
		'orderby'             => 'relevance',
		'ignore_sticky_posts' => true,
	) );

	if ( empty( $post_ids ) ) {
		$post_ids = get_posts( array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'fields'              => 'ids',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'meta_query'          => array(
				array(
					'key'     => '_sku',
					'value'   => $query,
					'compare' => 'LIKE',
				),
			),
			'ignore_sticky_posts' => true,
		) );
	}

	foreach ( $post_ids as $post_id ) {
		$product = wc_get_product( $post_id );
		if ( $product instanceof WC_Product ) {
			$items[] = iphonebay_search_product_payload( $product );
		}
	}

	return $items;
}

/**
 * REST endpoint for the header's predictive product search.
 */
function iphonebay_register_search_route() {
	register_rest_route(
		'iphonebay/v1',
		'/product-search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $request ) {
				$query   = (string) $request->get_param( 'q' );
				$results = iphonebay_find_search_products( $query, 8 );

				return rest_ensure_response( array(
					'query'      => $query,
					'results'    => $results,
					'resultsUrl' => iphonebay_product_search_url( $query ),
					'isDefault'  => '' === trim( $query ),
				) );
			},
			'args'                => array(
				'q' => array(
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'iphonebay_register_search_route' );

/**
 * Determine whether the current query is for the storefront.
 *
 * @param WP_Query $query Query object.
 * @return bool
 */
function iphonebay_is_shop_query( $query ) {
	if ( ! $query instanceof WP_Query ) {
		return false;
	}

	if ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) {
		return true;
	}

	return 'product' === $query->get( 'post_type' );
}

/**
 * Apply collection and sidebar filters to the shop query.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function iphonebay_filter_shop_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! iphonebay_is_shop_query( $query ) ) {
		return;
	}

	$tax_query  = (array) $query->get( 'tax_query', array() );
	$meta_query = (array) $query->get( 'meta_query', array() );

	$filter_map = array(
		'filter_condition' => 'pa_condition',
		'filter_storage'   => 'pa_storage',
		'filter_colour'    => 'pa_colour',
	);

	foreach ( $filter_map as $param => $taxonomy ) {
		if ( empty( $_GET[ $param ] ) ) {
			continue;
		}

		$raw_values = wp_unslash( $_GET[ $param ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$raw_values = is_array( $raw_values ) ? $raw_values : explode( ',', (string) $raw_values );
		$terms      = array_filter( array_map( 'sanitize_title', $raw_values ) );

		if ( $terms ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $terms,
			);
		}
	}

	$min_price = isset( $_GET['min_price'] ) ? absint( wp_unslash( $_GET['min_price'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$max_price = isset( $_GET['max_price'] ) ? absint( wp_unslash( $_GET['max_price'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $min_price || $max_price ) {
		$price_clause = array(
			'key'     => '_price',
			'type'    => 'NUMERIC',
			'compare' => $min_price && $max_price ? 'BETWEEN' : ( $min_price ? '>=' : '<=' ),
			'value'   => $min_price && $max_price ? array( $min_price, $max_price ) : ( $min_price ? $min_price : $max_price ),
		);
		$meta_query[] = $price_clause;
	}

	$collection = iphonebay_current_shop_collection();
	if ( $collection ) {
		switch ( $collection ) {
			case 'best':
				$query->set( 'meta_key', 'total_sales' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );
				break;
			case 'new':
				$query->set( 'orderby', 'date' );
				$query->set( 'order', 'DESC' );
				break;
			case 'deals':
				$sale_ids = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
				$query->set( 'post__in', $sale_ids ? $sale_ids : array( 0 ) );
				break;
		}
	}

	if ( $tax_query ) {
		$query->set( 'tax_query', $tax_query );
	}

	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query );
	}
}
add_action( 'pre_get_posts', 'iphonebay_filter_shop_query' );
