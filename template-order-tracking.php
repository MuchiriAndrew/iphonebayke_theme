<?php
/**
 * Template Name: Track Order Page
 * Description: Real order lookup (WooCommerce order tracking) plus delivery process info.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$steps = array(
	array(
		'title' => __( 'Order confirmed', 'iphonebay' ),
		'copy'  => __( 'You get an instant confirmation by email once payment clears — M-Pesa, card or bank transfer.', 'iphonebay' ),
		'icon'  => '<polyline points="20 6 9 17 4 12"/>',
	),
	array(
		'title' => __( 'Packed & verified', 'iphonebay' ),
		'copy'  => __( 'Your device is pulled from stock, its final inspection is confirmed, and it is boxed with the warranty card.', 'iphonebay' ),
		'icon'  => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
	),
	array(
		'title' => __( 'Out for delivery', 'iphonebay' ),
		'copy'  => __( 'Same day within Nairobi CBD and nearby estates for orders confirmed before 3pm. Next-day courier countrywide.', 'iphonebay' ),
		'icon'  => '<path d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h13l4 4v6a2 2 0 0 1-2 2h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
	),
	array(
		'title' => __( 'Delivered', 'iphonebay' ),
		'copy'  => __( 'Inspect it on handover if you like — our courier will wait. Any issue, WhatsApp us the same day.', 'iphonebay' ),
		'icon'  => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
	),
);

$whatsapp_url = 'https://wa.me/' . preg_replace( '/\D+/', '', iphonebay_opt( 'whatsapp', '254700000000' ) );
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Order status', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'Enter your order ID and the email you used at checkout to see the latest status. No account needed.', 'iphonebay' ); ?></p>
		</div>

		<section class="entry-wrap page-shell track-page">
			<div class="track-form-card">
				<?php echo do_shortcode( '[woocommerce_order_tracking]' ); ?>
			</div>
		</section>

		<section class="track-process">
			<div class="track-process-inner">
				<h2 class="tradein-flat-head"><?php esc_html_e( 'What happens after you order', 'iphonebay' ); ?></h2>
				<div class="track-process-grid">
					<?php foreach ( $steps as $i => $step ) : ?>
						<div class="track-process-step">
							<div class="track-process-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $step['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></div>
							<div class="track-process-n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></div>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['copy'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="faq-cta">
			<div class="faq-cta-inner">
				<h2><?php esc_html_e( "Can't find your order?", 'iphonebay' ); ?></h2>
				<p><?php esc_html_e( 'Double-check the order ID from your confirmation email, or message us directly and we will look it up.', 'iphonebay' ); ?></p>
				<div class="faq-cta-actions">
					<a href="<?php echo esc_url( $whatsapp_url ); ?>" class="btn-primary" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'iphonebay' ); ?> &rarr;</a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
