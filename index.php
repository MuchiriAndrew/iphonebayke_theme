<?php
/**
 * Main template / blog fallback.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main">
	<div class="page-hero">
		<h1 class="page-title">
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_search() ) {
				printf( esc_html__( 'Search results for: %s', 'iphonebay' ), '<em>' . esc_html( get_search_query() ) . '</em>' );
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'Latest', 'iphonebay' );
			}
			?>
		</h1>
	</div>

	<div class="entry-wrap">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'blog-card' ); ?> style="margin-bottom:2.5rem;padding-bottom:2.5rem;border-bottom:1px solid var(--color-surface-2);">
					<h2 style="font-family:var(--font-display);"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p style="font-size:var(--text-xs);color:var(--color-ink-3);"><?php echo esc_html( get_the_date() ); ?></p>
					<div class="entry-content"><?php the_excerpt(); ?></div>
					<a class="btn-view" style="display:inline-flex;width:auto;padding:0.5rem 1.25rem;margin-top:0.75rem;" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'iphonebay' ); ?></a>
				</article>
			<?php endwhile; ?>
			<div class="pagination"><?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'iphonebay' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
