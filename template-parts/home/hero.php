<?php
/**
 * Home — hero slider. Reads "Hero Slides" CPT, falls back to a default slide.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_posts( array(
	'post_type'      => 'hero_slide',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

$shop_url    = iphonebay_shop_url();
$warranty_url = iphonebay_how_we_test_url();
?>
<section class="hero" id="hero" aria-label="<?php esc_attr_e( 'Featured promotions', 'iphonebay' ); ?>">
	<div class="hero-track">
		<?php if ( $slides ) : ?>
			<?php foreach ( $slides as $index => $slide ) :
				$img      = get_the_post_thumbnail_url( $slide->ID, 'iphonebay_hero' );
				$subtitle = get_post_meta( $slide->ID, 'iphonebay_subtitle', true );
				$b1_text  = get_post_meta( $slide->ID, 'iphonebay_btn1_text', true );
				$b1_url   = iphonebay_resolve_cta_url( $b1_text, get_post_meta( $slide->ID, 'iphonebay_btn1_url', true ) );
				$b2_text  = get_post_meta( $slide->ID, 'iphonebay_btn2_text', true );
				$b2_url   = iphonebay_resolve_cta_url( $b2_text, get_post_meta( $slide->ID, 'iphonebay_btn2_url', true ) );
				?>
				<div class="hero-slide<?php echo 0 === $index ? ' active' : ''; ?>">
					<?php if ( $img ) : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title( $slide ) ); ?>" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
					<?php endif; ?>
					<div class="hero-overlay"></div>
					<div class="hero-content"><div class="hero-inner"><div class="hero-text">
						<h1 class="hero-title"><?php echo wp_kses_post( iphonebay_highlight( get_the_title( $slide ) ) ); ?></h1>
						<?php if ( $subtitle ) : ?><p class="hero-sub"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
						<div class="hero-ctas">
							<?php if ( $b1_text ) : ?><a href="<?php echo esc_url( $b1_url ? $b1_url : $shop_url ); ?>" class="btn-primary"><?php echo esc_html( $b1_text ); ?> &rarr;</a><?php endif; ?>
							<?php if ( $b2_text ) : ?><a href="<?php echo esc_url( $b2_url ? $b2_url : $shop_url ); ?>" class="btn-ghost"><?php echo esc_html( $b2_text ); ?></a><?php endif; ?>
						</div>
					</div></div></div>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="hero-slide active">
				<div class="hero-overlay"></div>
				<div class="hero-content"><div class="hero-inner"><div class="hero-text">
					<h1 class="hero-title"><?php echo wp_kses_post( iphonebay_highlight( __( 'Checked part by part. *Priced honestly.*', 'iphonebay' ) ) ); ?></h1>
					<p class="hero-sub"><?php esc_html_e( 'Every iPhone we sell passes a 30-point inspection — battery health, screen, cameras, Face ID, IMEI. Same-day CBD delivery and a 6-month warranty on every device.', 'iphonebay' ); ?></p>
					<div class="hero-ctas">
						<a href="<?php echo esc_url( $shop_url ); ?>" class="btn-primary"><?php esc_html_e( 'Shop iPhones', 'iphonebay' ); ?> &rarr;</a>
						<a href="<?php echo esc_url( $warranty_url ); ?>" class="btn-ghost"><?php esc_html_e( 'Our warranty', 'iphonebay' ); ?></a>
					</div>
				</div></div></div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $slides && count( $slides ) > 1 ) : ?>
		<div class="hero-controls" role="tablist" aria-label="<?php esc_attr_e( 'Slide indicators', 'iphonebay' ); ?>">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button class="hero-dot<?php echo 0 === $index ? ' active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'iphonebay' ), $index + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
