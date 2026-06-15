<?php
/**
 * Home — Shop by Category (editorial grid from WooCommerce product categories).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cards = array();
$preferred_cards = array(
	'iphones'        => array(
		'label' => __( 'Most Popular', 'iphonebay' ),
		'title' => __( 'iPhones', 'iphonebay' ),
		'sub'   => __( 'Ex-UK · Refurbished · Brand New', 'iphonebay' ),
	),
	'samsung-phones' => array(
		'label' => __( 'Android Flagship', 'iphonebay' ),
		'title' => __( 'Samsung', 'iphonebay' ),
		'sub'   => __( 'S-Series & A-Series', 'iphonebay' ),
	),
	'accessories'    => array(
		'label' => __( 'Complete Your Setup', 'iphonebay' ),
		'title' => __( 'Accessories', 'iphonebay' ),
		'sub'   => __( 'AirPods · Chargers · Cases', 'iphonebay' ),
	),
	'google-pixel'   => array(
		'label' => __( 'Pure Android', 'iphonebay' ),
		'title' => __( 'Google Pixel', 'iphonebay' ),
		'sub'   => __( 'Pixel 7 · 8 · 9 Series', 'iphonebay' ),
	),
);

if ( taxonomy_exists( 'product_cat' ) ) {
	foreach ( $preferred_cards as $slug => $config ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$cards[] = array(
				'term'   => $term,
				'config' => $config,
			);
		}
	}
}

if ( empty( $cards ) ) {
	return; // No categories yet — skip the section cleanly.
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<div class="section" id="categories">
	<div class="section-header">
		<h2 class="section-title"><?php echo wp_kses_post( iphonebay_highlight( __( "Shop by\n*Category*", 'iphonebay' ) ) ); ?></h2>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="section-link"><?php esc_html_e( 'All Categories', 'iphonebay' ); ?> &rarr;</a>
	</div>
	<div class="cat-grid">
		<?php foreach ( $cards as $card ) :
			$cat      = $card['term'];
			$config   = $card['config'];
			$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
			?>
			<a class="cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>">
				<?php else : ?>
					<img src="<?php echo esc_url( wc_placeholder_img_src( 'large' ) ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>">
				<?php endif; ?>
				<div class="cat-overlay"></div>
				<div class="cat-body">
					<div class="cat-label"><?php echo esc_html( $config['label'] ); ?></div>
					<div class="cat-name"><?php echo esc_html( $config['title'] ); ?></div>
					<div class="cat-sub"><?php echo esc_html( $config['sub'] ); ?></div>
				</div>
				<span class="cat-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
