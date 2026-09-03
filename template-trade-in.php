<?php
/**
 * Template Name: Trade-In Page
 * Description: Dedicated trade-in landing page — information first, quote form at the bottom.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$steps = array(
	array(
		'text' => iphonebay_opt( 'tradein_step1', 'Tell us your phone model & condition' ),
		'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
	),
	array(
		'text' => iphonebay_opt( 'tradein_step2', 'Get an instant quote — no obligation' ),
		'icon' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
	),
	array(
		'text' => iphonebay_opt( 'tradein_step3', 'Drop off or courier, get paid via M-Pesa' ),
		'icon' => '<path d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h13l4 4v6a2 2 0 0 1-2 2h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
	),
);
$perks = array(
	__( 'No-obligation quote, confirmed in minutes', 'iphonebay' ),
	__( 'Free courier pickup anywhere in Nairobi, or drop it at our CBD office', 'iphonebay' ),
	__( 'Paid the same day via M-Pesa once your device is verified', 'iphonebay' ),
	__( 'Written trade-in receipt for every device we take in', 'iphonebay' ),
);
$values = array(
	array( 'model' => 'iPhone 11', 'sub' => '64GB, good condition', 'amount' => 'Up to KES 16,000' ),
	array( 'model' => 'iPhone 12', 'sub' => '64GB, good condition', 'amount' => 'Up to KES 27,000' ),
	array( 'model' => 'iPhone 13', 'sub' => '128GB, good condition', 'amount' => 'Up to KES 38,000' ),
	array( 'model' => 'Samsung S22 / S23', 'sub' => '128GB, good condition', 'amount' => 'Up to KES 24,000' ),
);
$tradein_image_id = iphonebay_opt( 'tradein_image' );
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php echo esc_html( iphonebay_opt( 'tradein_eyebrow', 'Trade-In Program' ) ); ?></div>
				<h1 class="page-title"><?php echo wp_kses_post( iphonebay_highlight( iphonebay_opt( 'tradein_title', 'Your Old Phone is *Worth More*' ) ) ); ?></h1>
			</div>
			<div>
				<p class="page-hero-copy"><?php echo esc_html( iphonebay_opt( 'tradein_desc', "Don't let your old device collect dust. Trade it in and get instant credit towards your next purchase — or cash via M-Pesa." ) ); ?></p>
				<a href="#quote-form" class="btn-primary" style="margin-top:1.25rem;width:fit-content;"><?php esc_html_e( 'Get a Quote', 'iphonebay' ); ?> &darr;</a>
			</div>
		</div>

		<section class="tradein-section">
			<div class="tradein-section-inner">
				<h2 class="tradein-flat-head"><?php esc_html_e( 'How it works', 'iphonebay' ); ?></h2>
				<div class="tradein-steps-grid">
					<?php foreach ( $steps as $i => $step ) : ?>
						<div class="tradein-process-card">
							<div class="tradein-process-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $step['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></div>
							<div class="tradein-process-n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></div>
							<div class="tradein-process-t"><?php echo esc_html( $step['text'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="tradein-section tradein-section--surface">
			<div class="tradein-media-row">
				<?php if ( $tradein_image_id ) : ?>
					<div class="tradein-media-image">
						<?php echo wp_get_attachment_image( $tradein_image_id, 'large', false, array( 'alt' => esc_attr__( 'Person holding a smartphone', 'iphonebay' ) ) ); ?>
					</div>
				<?php endif; ?>
				<div class="tradein-media-copy">
					<h2 class="tradein-flat-head"><?php esc_html_e( 'Why trade in with us', 'iphonebay' ); ?></h2>
					<div class="tradein-prose">
						<?php the_content(); ?>
					</div>
					<ul class="tradein-perks">
						<?php foreach ( $perks as $perk ) : ?>
							<li>
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
								<span><?php echo esc_html( $perk ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>

		<section class="tradein-section">
			<div class="tradein-section-inner">
				<h2 class="tradein-flat-head"><?php esc_html_e( 'Typical trade-in values', 'iphonebay' ); ?></h2>
				<ul class="tradein-values">
					<?php foreach ( $values as $row ) : ?>
						<li>
							<span class="model"><?php echo esc_html( $row['model'] ); ?><small><?php echo esc_html( $row['sub'] ); ?></small></span>
							<span class="value"><?php echo esc_html( $row['amount'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="tradein-values-note"><?php esc_html_e( 'Final offer depends on verified condition and battery health. Paid to your M-Pesa the same day we verify the device.', 'iphonebay' ); ?></p>
			</div>
		</section>

		<section class="tradein-section tradein-section--form" id="quote-form">
			<div class="tradein-form-inner">
				<?php
				echo do_shortcode(
					'[iphonebay_contact_form topic="trade-in" title="' . esc_attr__( 'Request a trade-in quote', 'iphonebay' ) . '" intro="' . esc_attr__( 'Tell us the model, storage, cosmetic condition, and battery health of your current phone.', 'iphonebay' ) . '" button="' . esc_attr__( 'Request quote', 'iphonebay' ) . '"]'
				);
				?>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
