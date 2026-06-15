<?php
/**
 * Front page (homepage).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url  = iphonebay_shop_url();
$best_url  = iphonebay_collection_url( 'best' );
$new_url   = iphonebay_collection_url( 'new' );
$deals_url = iphonebay_collection_url( 'deals' );
?>

<main id="main" class="site-main">

	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/marquee-gold' );
	get_template_part( 'template-parts/home/categories' );

	// Best Sellers (banded).
	iphonebay_render_carousel(
		iphonebay_opt( 'best_title', 'Best *Sellers*' ),
		iphonebay_get_products( 'best', 8 ),
		$best_url,
		'',
		true
	);

	get_template_part( 'template-parts/home/trust' );

	// New Arrivals.
	iphonebay_render_carousel(
		iphonebay_opt( 'new_title', 'New *Arrivals*' ),
		iphonebay_get_products( 'new', 8 ),
		$new_url,
		'',
		false
	);

	get_template_part( 'template-parts/home/marquee-dark' );

	// Deals & Offers (banded).
	iphonebay_render_carousel(
		iphonebay_opt( 'deals_title', 'Deals & *Offers*' ),
		iphonebay_get_products( 'deals', 8 ),
		$deals_url,
		__( 'Limited Time', 'iphonebay' ),
		true
	);

	get_template_part( 'template-parts/home/tradein' );
	get_template_part( 'template-parts/home/reviews' );
	get_template_part( 'template-parts/home/delivery' );
	?>

</main>

<?php
get_footer();
