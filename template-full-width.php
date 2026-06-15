<?php
/**
 * Template Name: Full Width (Page Builder)
 *
 * A blank-canvas, full-width template for pages built with Elementor,
 * the block editor, or shortcodes. Keeps the theme header & footer.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" class="site-main full-width-content">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
