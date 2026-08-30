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

$shop_url = iphonebay_shop_url();
$best_url = iphonebay_collection_url( 'best' );
?>

<main id="main" class="site-main">

	<?php
	get_template_part( 'template-parts/home/hero' );

	// Trust band — inspection, warranty, payment, delivery.
	get_template_part( 'template-parts/home/trust' );

	// Shop by category.
	get_template_part( 'template-parts/home/categories' );

	// Best Sellers carousel (banded).
	iphonebay_render_carousel(
		iphonebay_opt( 'best_title', 'Best *Sellers*' ),
		iphonebay_get_products( 'best', 8 ),
		$best_url,
		'',
		true
	);

	get_template_part( 'template-parts/home/tradein' );
	get_template_part( 'template-parts/home/reviews' );
	get_template_part( 'template-parts/home/delivery' );
	?>

</main>

<?php
get_footer();
