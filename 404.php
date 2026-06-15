<?php
/**
 * 404 template.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<main id="main" class="site-main">
	<div class="entry-wrap" style="text-align:center;padding-top:5rem;padding-bottom:6rem;">
		<div style="font-family:var(--font-display);font-size:clamp(4rem,12vw,8rem);font-weight:900;color:var(--color-navy);letter-spacing:-0.04em;line-height:1;">404</div>
		<h1 class="page-title" style="margin:1rem 0;"><?php esc_html_e( 'Page not found', 'iphonebay' ); ?></h1>
		<p style="color:var(--color-ink-2);margin-bottom:2rem;"><?php esc_html_e( 'The page you are looking for has moved or no longer exists. Let’s get you back to shopping.', 'iphonebay' ); ?></p>
		<div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary" style="background:var(--color-navy);color:#fff;"><?php esc_html_e( 'Back to Home', 'iphonebay' ); ?></a>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="btn-view" style="width:auto;padding:0.75rem 1.75rem;border-radius:var(--radius-full);"><?php esc_html_e( 'Shop Phones', 'iphonebay' ); ?></a>
		</div>
	</div>
</main>
<?php
get_footer();
