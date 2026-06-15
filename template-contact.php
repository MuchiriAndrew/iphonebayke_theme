<?php
/**
 * Template Name: Contact Page
 * Description: Brand contact page with a theme-native contact form.
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
				<div class="section-eyebrow"><?php esc_html_e( 'Contact', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'Need help choosing a phone, checking stock, tracking an order, or starting a trade-in? Reach out and we will point you in the right direction.', 'iphonebay' ); ?></p>
		</div>

		<section class="entry-wrap page-shell contact-layout">
			<div class="contact-sidebar">
				<div class="detail-card">
					<div class="detail-card-label"><?php esc_html_e( 'Call us', 'iphonebay' ); ?></div>
					<a class="detail-card-value" href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', iphonebay_opt( 'phone', '+254700000000' ) ) ); ?>"><?php echo esc_html( iphonebay_opt( 'phone', '+254 700 000 000' ) ); ?></a>
				</div>
				<div class="detail-card">
					<div class="detail-card-label"><?php esc_html_e( 'WhatsApp', 'iphonebay' ); ?></div>
					<a class="detail-card-value" href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/\D+/', '', iphonebay_opt( 'whatsapp', '254700000000' ) ) ); ?>"><?php esc_html_e( 'Chat with the team', 'iphonebay' ); ?></a>
				</div>
				<div class="detail-card">
					<div class="detail-card-label"><?php esc_html_e( 'Visit us', 'iphonebay' ); ?></div>
					<p class="detail-card-copy"><?php esc_html_e( 'Nairobi, Kenya. Deliveries and pick-ups coordinated after order confirmation.', 'iphonebay' ); ?></p>
				</div>
				<?php if ( get_the_content() ) : ?>
					<div class="detail-card detail-card--prose">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="contact-main">
				<?php echo do_shortcode( '[iphonebay_contact_form]' ); ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
