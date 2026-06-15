<?php
/**
 * Shop archive — custom layout: light header, left filter sidebar, product grid.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$collection_map  = iphonebay_shop_collection_map();
$collection_key  = iphonebay_current_shop_collection();
$collection_data = isset( $collection_map[ $collection_key ] ) ? $collection_map[ $collection_key ] : null;
$shop_url        = iphonebay_shop_url();
?>

<div class="shop-header">
	<?php woocommerce_breadcrumb(); ?>
	<?php if ( $collection_data ) : ?>
		<div class="section-eyebrow"><?php esc_html_e( 'Curated Collection', 'iphonebay' ); ?></div>
	<?php endif; ?>
	<h1 class="shop-title">
		<?php
		if ( $collection_data ) {
			echo esc_html( $collection_data['label'] );
		} elseif ( apply_filters( 'woocommerce_show_page_title', true ) ) {
			woocommerce_page_title();
		} else {
			esc_html_e( 'Shop', 'iphonebay' );
		}
		?>
	</h1>
	<?php if ( $collection_data ) : ?>
		<div class="shop-header-row">
			<p class="shop-lead"><?php echo esc_html( $collection_data['copy'] ); ?></p>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="section-link"><?php esc_html_e( 'View all products', 'iphonebay' ); ?> &rarr;</a>
		</div>
	<?php endif; ?>
</div>

<!-- Mobile filter backdrop (kept outside the grid) -->
<div class="filter-backdrop" id="filterBackdrop" aria-hidden="true"></div>

<div class="shop-layout">

	<?php
	/**
	 * Left filter sidebar.
	 */
	if ( function_exists( 'iphonebay_shop_filters' ) ) {
		iphonebay_shop_filters();
	}
	?>

	<main class="shop-main" id="main">

		<div class="shop-toolbar">
			<?php woocommerce_result_count(); ?>
			<div class="toolbar-right">
				<button class="filter-toggle" id="filterToggle" type="button">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
					<?php esc_html_e( 'Filters', 'iphonebay' ); ?>
				</button>
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>

		<?php if ( woocommerce_product_loop() ) : ?>

			<ul class="products product-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'product' );
				}
				?>
			</ul>

			<?php woocommerce_pagination(); ?>

		<?php else : ?>

			<?php do_action( 'woocommerce_no_products_found' ); ?>

		<?php endif; ?>

	</main>
</div>

<?php
get_footer();
