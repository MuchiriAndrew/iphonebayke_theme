<?php
/**
 * Template Name: Legal Page
 * Description: Clean legal-content template for privacy, terms, and policy pages.
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
				<div class="section-eyebrow"><?php esc_html_e( 'Legal', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'Straightforward terms, no fine-print surprises — the same honesty we apply to grading a phone.', 'iphonebay' ); ?></p>
		</div>

		<article <?php post_class( 'entry-wrap legal-layout' ); ?>>
			<div class="legal-meta"><?php printf( esc_html__( 'Last updated %s', 'iphonebay' ), esc_html( get_the_modified_date() ) ); ?></div>
			<div class="entry-content legal-prose">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'iphonebay' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
