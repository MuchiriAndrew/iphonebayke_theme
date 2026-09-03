<?php
/**
 * Template Name: FAQs Page
 * Description: Grouped, information-rich FAQ page with an accordion per topic.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$groups = array(
	array(
		'title' => __( 'Buying from us', 'iphonebay' ),
		'icon'  => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
		'items' => array(
			array(
				'q' => __( 'What does "Ex-UK" mean?', 'iphonebay' ),
				'a' => __( 'Ex-UK devices are genuine iPhones imported from the UK — usually one-owner units that came off carrier contracts. They\'re not the same as "refurbished": most of our Ex-UK stock has never been repaired at all. Every unit is inspected here in Nairobi, graded, and carries our written warranty.', 'iphonebay' ),
			),
			array(
				'q' => __( 'Are these phones genuine?', 'iphonebay' ),
				'a' => __( "Yes — and verified, not promised. We check every device's IMEI against Apple's records, screen against stolen-device databases, and confirm the screen, battery and cameras are original parts. Clones and part-swapped units are rejected before they reach the shop.", 'iphonebay' ),
			),
			array(
				'q' => __( 'What battery health will my phone have?', 'iphonebay' ),
				'a' => __( 'Ex-UK devices are guaranteed at 85% or better, and the actual reading for your unit is confirmed before dispatch — most stock tests 88–94%. Sealed new devices are 100% by definition.', 'iphonebay' ),
			),
		),
	),
	array(
		'title' => __( 'Delivery & payment', 'iphonebay' ),
		'icon'  => '<path d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h13l4 4v6a2 2 0 0 1-2 2h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		'items' => array(
			array(
				'q' => __( 'Do you deliver outside Nairobi?', 'iphonebay' ),
				'a' => __( 'Yes. Orders confirmed before 3pm are delivered the same day within the CBD and nearby estates. Countrywide, we courier next-day to Mombasa, Kisumu, Nakuru, Eldoret and all major towns — delivery is free on orders above KES 50,000.', 'iphonebay' ),
			),
			array(
				'q' => __( 'How can I pay?', 'iphonebay' ),
				'a' => __( "M-Pesa at checkout (you'll get the STK prompt right on the checkout page), bank transfer, Visa/Mastercard, or cash when you pick up at our CBD office.", 'iphonebay' ),
			),
		),
	),
	array(
		'title' => __( 'Warranty & returns', 'iphonebay' ),
		'icon'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'items' => array(
			array(
				'q' => __( 'What exactly does the warranty cover?', 'iphonebay' ),
				'a' => __( 'Six months on Ex-UK devices, twelve on sealed new — battery, charging port, screen, speakers, cameras, Face ID; the hardware faults that develop through normal use. Full details are on the Warranty & Returns page, and the same terms are printed on the card in your box.', 'iphonebay' ),
			),
			array(
				'q' => __( 'Can I return a phone?', 'iphonebay' ),
				'a' => __( "Within 7 days, if it's not as described, you get a full refund to M-Pesa within 48 hours of inspection. Change-of-mind returns within 7 days are accepted for store credit on as-delivered devices.", 'iphonebay' ),
			),
		),
	),
	array(
		'title' => __( 'Trade-in', 'iphonebay' ),
		'icon'  => '<polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>',
		'items' => array(
			array(
				'q' => __( 'Can I trade in my current phone?', 'iphonebay' ),
				'a' => __( "Yes — against your purchase, or straight cash to M-Pesa. Tell us the model, storage and condition on WhatsApp or the trade-in form and you'll get a quote the same day. As a current example, an iPhone 12 64GB in good condition fetches up to KES 27,000.", 'iphonebay' ),
			),
		),
	),
);

$whatsapp_url = 'https://wa.me/' . preg_replace( '/\D+/', '', iphonebay_opt( 'whatsapp', '254700000000' ) );
$contact_page = get_page_by_path( 'contact-us' );
$contact_url  = $contact_page ? get_permalink( $contact_page ) : home_url( '/contact-us/' );
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Help centre', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'Answers on Ex-UK stock, battery health, delivery, payment, warranty and trade-ins — grouped by topic.', 'iphonebay' ); ?></p>
		</div>

		<section class="entry-wrap page-shell faq-page">
			<?php foreach ( $groups as $group ) : ?>
				<div class="faq-group">
					<div class="faq-group-head">
						<div class="faq-group-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $group['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></div>
						<h2><?php echo esc_html( $group['title'] ); ?></h2>
					</div>
					<div class="faq-list">
						<?php foreach ( $group['items'] as $item ) : ?>
							<details class="faq-item">
								<summary>
									<span><?php echo esc_html( $item['q'] ); ?></span>
									<svg class="faq-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
								</summary>
								<p><?php echo esc_html( $item['a'] ); ?></p>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</section>

		<section class="faq-cta">
			<div class="faq-cta-inner">
				<h2><?php esc_html_e( "Still have a question?", 'iphonebay' ); ?></h2>
				<p><?php esc_html_e( "We answer our own WhatsApp — ask us directly, or send a message and we'll reply the same day.", 'iphonebay' ); ?></p>
				<div class="faq-cta-actions">
					<a href="<?php echo esc_url( $whatsapp_url ); ?>" class="btn-primary" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'iphonebay' ); ?> &rarr;</a>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn-ghost"><?php esc_html_e( 'Contact Us', 'iphonebay' ); ?></a>
				</div>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();
