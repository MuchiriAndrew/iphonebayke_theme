<?php
/**
 * Template Name: Trade-In Page
 * Description: Dedicated trade-in landing page with quote form.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$steps = array(
	iphonebay_opt( 'tradein_step1', 'Tell us your phone model & condition' ),
	iphonebay_opt( 'tradein_step2', 'Get an instant quote — no obligation' ),
	iphonebay_opt( 'tradein_step3', 'Drop off or courier, get paid via M-Pesa' ),
);
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php echo esc_html( iphonebay_opt( 'tradein_eyebrow', 'Trade-In Program' ) ); ?></div>
				<h1 class="page-title"><?php echo wp_kses_post( iphonebay_highlight( iphonebay_opt( 'tradein_title', 'Your Old Phone is *Worth More*' ) ) ); ?></h1>
			</div>
			<p class="page-hero-copy"><?php echo esc_html( iphonebay_opt( 'tradein_desc', "Don't let your old device collect dust. Trade it in and get instant credit towards your next purchase — or cash via M-Pesa." ) ); ?></p>
		</div>

		<section class="entry-wrap page-shell tradein-page">
			<div class="tradein-page-copy">
				<div class="detail-card">
					<div class="detail-card-label"><?php esc_html_e( 'How it works', 'iphonebay' ); ?></div>
					<ol class="tradein-page-steps">
						<?php foreach ( $steps as $step ) : ?>
							<li><?php echo esc_html( $step ); ?></li>
						<?php endforeach; ?>
					</ol>
				</div>
				<div class="detail-card detail-card--prose">
					<?php the_content(); ?>
				</div>
			</div>
			<div class="tradein-page-form">
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
