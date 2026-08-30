<?php
/**
 * Home — hero. Single brand-led slide: headline + subhead + two CTAs,
 * optional real product photo in a clean panel right.
 * Reads the first "Hero Slide" CPT entry, falls back to theme copy.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_posts( array(
	'post_type'      => 'hero_slide',
	'posts_per_page' => 1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

$shop_url = iphonebay_shop_url();
$test_url = iphonebay_how_we_test_url();

if ( $slides ) {
	$slide    = $slides[0];
	$eyebrow  = get_post_meta( $slide->ID, 'iphonebay_eyebrow', true );
	$title    = get_the_title( $slide );
	$subtitle = get_post_meta( $slide->ID, 'iphonebay_subtitle', true );
	$b1_text  = get_post_meta( $slide->ID, 'iphonebay_btn1_text', true );
	$b1_url   = iphonebay_resolve_cta_url( $b1_text, get_post_meta( $slide->ID, 'iphonebay_btn1_url', true ) );
	$b2_text  = get_post_meta( $slide->ID, 'iphonebay_btn2_text', true );
	$b2_url   = iphonebay_resolve_cta_url( $b2_text, get_post_meta( $slide->ID, 'iphonebay_btn2_url', true ) );
	$img      = get_the_post_thumbnail_url( $slide->ID, 'iphonebay_hero' );
} else {
	$slide    = null;
	$eyebrow  = __( 'Nairobi · Ex-UK & New iPhones', 'iphonebay' );
	$title    = __( 'Checked part by part. *Priced honestly.*', 'iphonebay' );
	$subtitle = __( 'Every iPhone we sell passes a 30-point inspection — battery health, screen, cameras, Face ID, IMEI. Same-day CBD delivery and a 6-month warranty on every device.', 'iphonebay' );
	$b1_text  = __( 'Shop iPhones', 'iphonebay' );
	$b1_url   = $shop_url;
	$b2_text  = __( 'How we test', 'iphonebay' );
	$b2_url   = $test_url;
	$img      = '';
}
?>
<section class="hero" id="hero" aria-label="<?php esc_attr_e( 'iPhoneBayKE — premium iPhones in Nairobi', 'iphonebay' ); ?>">
	<div class="hero-inner<?php echo $img ? '' : ' hero-inner--text'; ?>">
		<div class="hero-text">
			<?php if ( $eyebrow ) : ?>
				<div class="hero-label"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<h1 class="hero-title"><?php echo wp_kses_post( iphonebay_highlight( $title ) ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="hero-sub"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<div class="hero-ctas">
				<?php if ( $b1_text ) : ?>
					<a href="<?php echo esc_url( $b1_url ? $b1_url : $shop_url ); ?>" class="btn-primary"><?php echo esc_html( $b1_text ); ?> &rarr;</a>
				<?php endif; ?>
				<?php if ( $b2_text ) : ?>
					<a href="<?php echo esc_url( $b2_url ? $b2_url : $shop_url ); ?>" class="btn-ghost"><?php echo esc_html( $b2_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $img ) : ?>
			<div class="hero-media">
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $slide ? get_the_title( $slide ) : __( 'iPhone product photo', 'iphonebay' ) ); ?>" fetchpriority="high">
			</div>
		<?php endif; ?>
	</div>
</section>