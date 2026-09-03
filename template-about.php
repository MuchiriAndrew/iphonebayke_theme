<?php
/**
 * Template Name: About Page
 * Description: Brand story page — origin, values, and what we stand for.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$about_image_id = iphonebay_opt( 'about_image' );

$timeline = array(
	array(
		'year' => '2021',
		'text' => __( 'Started as a WhatsApp list — a few phones a month, sold to friends and colleagues from a desk in the Nairobi CBD.', 'iphonebay' ),
		'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
	),
	array(
		'year' => __( 'Today', 'iphonebay' ),
		'text' => __( 'A CBD office selling Ex-UK and sealed-new iPhones, Samsung and Google Pixel — every unit still gets the same 30-point inspection.', 'iphonebay' ),
		'icon' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
	),
);

$values = array(
	array(
		'title' => 'Real battery numbers',
		'copy'  => "The percentage we quote is the percentage you get, verified on our own tools before listing — not an estimate.",
		'icon'  => '<rect x="1" y="6" width="18" height="12" rx="2" ry="2"/><line x1="23" y1="13" x2="23" y2="11"/>',
	),
	array(
		'title' => 'No upselling',
		'copy'  => "We'd rather sell you the phone that fits your budget than talk you into one that doesn't.",
		'icon'  => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
	),
	array(
		'title' => 'Written warranty, honored',
		'copy'  => 'Six months on Ex-UK, twelve on sealed new. Every claim is handled the same way, every time.',
		'icon'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
	),
	array(
		'title' => 'We answer our own WhatsApp',
		'copy'  => 'No call centre, no ticket queue. The person who replies is the person who can actually fix the problem.',
		'icon'  => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
	),
);

$shop_url     = function_exists( 'iphonebay_shop_url' ) ? iphonebay_shop_url() : home_url( '/shop/' );
$tradein_page = get_page_by_path( 'trade-in' );
$tradein_url  = $tradein_page ? get_permalink( $tradein_page ) : home_url( '/trade-in/' );
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'About iPhoneBayKE', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php echo wp_kses_post( iphonebay_highlight( 'Started as a WhatsApp list. *We never stopped being honest about batteries.*' ) ); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'A small Nairobi team that has sold phones the same way since 2021 — checked in the open, priced honestly, warrantied for real.', 'iphonebay' ); ?></p>
		</div>

		<section class="entry-wrap page-shell about-story">
			<div class="about-story-copy">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</div>
			<?php if ( $about_image_id ) : ?>
				<div class="about-story-media">
					<?php echo wp_get_attachment_image( $about_image_id, 'large', false, array( 'alt' => esc_attr__( 'Team member checking a WhatsApp order while preparing a package', 'iphonebay' ) ) ); ?>
				</div>
			<?php endif; ?>
		</section>

		<section class="page-shell about-timeline">
			<div class="about-timeline-inner">
				<?php foreach ( $timeline as $i => $node ) : ?>
					<div class="about-timeline-node">
						<div class="about-timeline-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $node['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></div>
						<div class="about-timeline-year"><?php echo esc_html( $node['year'] ); ?></div>
						<p class="about-timeline-text"><?php echo esc_html( $node['text'] ); ?></p>
					</div>
					<?php if ( 0 === $i && count( $timeline ) > 1 ) : ?>
						<div class="about-timeline-connector" aria-hidden="true"></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="page-shell about-values">
			<div class="about-values-inner">
				<h2 class="about-values-head"><?php esc_html_e( 'What we stand for', 'iphonebay' ); ?></h2>
				<div class="about-values-grid">
					<?php foreach ( $values as $value ) : ?>
						<div class="about-value">
							<div class="about-value-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $value['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></div>
							<h3><?php echo esc_html( $value['title'] ); ?></h3>
							<p><?php echo esc_html( $value['copy'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="about-cta">
			<div class="about-cta-inner">
				<h2><?php esc_html_e( 'Ready to shop with confidence?', 'iphonebay' ); ?></h2>
				<p><?php esc_html_e( 'Every device we sell has already been through the process described above. Browse what is in stock, or trade in the phone you have now.', 'iphonebay' ); ?></p>
				<div class="about-cta-actions">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="btn-primary"><?php esc_html_e( 'Browse the Shop', 'iphonebay' ); ?> &rarr;</a>
					<a href="<?php echo esc_url( $tradein_url ); ?>" class="btn-ghost"><?php esc_html_e( 'Start a Trade-In', 'iphonebay' ); ?></a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
