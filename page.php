<?php
/**
 * Default page template.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-received' ) ) : ?>
			<div class="page-hero">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</div>
		<?php endif; ?>
		<article <?php post_class( 'entry-wrap' ); ?>>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'iphonebay' ),
					'after'  => '</div>',
				) );
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
