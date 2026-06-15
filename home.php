<?php
/**
 * Blog index / posts page.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main">
	<div class="page-hero page-hero--split">
		<div>
			<div class="section-eyebrow"><?php esc_html_e( 'Journal', 'iphonebay' ); ?></div>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		</div>
		<p class="page-hero-copy"><?php esc_html_e( 'Battery testing notes, buying guides, shipping explainers, and behind-the-scenes store updates.', 'iphonebay' ); ?></p>
	</div>

	<div class="entry-wrap blog-archive">
		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'post-card' ); ?>>
						<a class="post-card-media" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php else : ?>
								<div class="post-card-placeholder"><?php esc_html_e( 'Story', 'iphonebay' ); ?></div>
							<?php endif; ?>
						</a>
						<div class="post-card-body">
							<div class="post-card-meta"><?php echo esc_html( get_the_date() ); ?></div>
							<h2 class="post-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="post-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a href="<?php the_permalink(); ?>" class="section-link"><?php esc_html_e( 'Read story', 'iphonebay' ); ?> &rarr;</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<h2><?php esc_html_e( 'No stories yet', 'iphonebay' ); ?></h2>
				<p><?php esc_html_e( 'We will publish buying guides and testing notes here soon.', 'iphonebay' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
