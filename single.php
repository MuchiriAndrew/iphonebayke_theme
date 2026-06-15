<?php
/**
 * Single post template.
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
		<div class="page-hero page-hero--split">
			<div>
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'iphonebay' ); ?></a><span class="breadcrumb-sep">/</span><span><?php the_title(); ?></span></nav>
			<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<div class="page-hero-copy">
				<div class="post-card-meta"><?php echo esc_html( get_the_date() ); ?></div>
				<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			</div>
		</div>
		<article <?php post_class( 'entry-wrap legal-layout' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:2rem;"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'iphonebay' ), 'after' => '</div>' ) );
				?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
