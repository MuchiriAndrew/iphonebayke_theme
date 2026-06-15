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

$shop_url = iphonebay_shop_url();
?>
<section class="hero" id="hero" aria-label="<?php esc_attr_e( 'Featured promotions', 'iphonebay' ); ?>">
	<div class="hero-track">
		<?php if ( $slides ) : ?>
			<?php foreach ( $slides as $index => $slide ) :
				$img      = get_the_post_thumbnail_url( $slide->ID, 'iphonebay_hero' );
				$eyebrow  = get_post_meta( $slide->ID, 'iphonebay_eyebrow', true );
				$subtitle = get_post_meta( $slide->ID, 'iphonebay_subtitle', true );
				$b1_text  = get_post_meta( $slide->ID, 'iphonebay_btn1_text', true );
				$b1_url   = iphonebay_resolve_cta_url( get_post_meta( $slide->ID, 'iphonebay_btn1_text', true ), get_post_meta( $slide->ID, 'iphonebay_btn1_url', true ) );
				$b2_text  = get_post_meta( $slide->ID, 'iphonebay_btn2_text', true );
				$b2_url   = iphonebay_resolve_cta_url( $b2_text, get_post_meta( $slide->ID, 'iphonebay_btn2_url', true ) );
				?>
				<div class="hero-slide<?php echo 0 === $index ? ' active' : ''; ?>">
					<?php if ( $img ) : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title( $slide ) ); ?>" <?php echo 0 === $index ? '' : 'loading="lazy"'; ?>>
					<?php endif; ?>
					<div class="hero-overlay"></div>
					<div class="hero-content"><div class="hero-inner"><div class="hero-text">
						<?php if ( $eyebrow ) : ?><div class="hero-label"><span class="hero-dot-live"></span> <?php echo esc_html( $eyebrow ); ?></div><?php endif; ?>
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
			<!-- Fallback slide (no Hero Slides created yet) -->
			<div class="hero-slide active">
				<div class="hero-overlay"></div>
				<div class="hero-content"><div class="hero-inner"><div class="hero-text">
					<div class="hero-label"><span class="hero-dot-live"></span> <?php esc_html_e( 'Welcome', 'iphonebay' ); ?></div>
					<h1 class="hero-title"><?php echo wp_kses_post( iphonebay_highlight( __( 'Premium *iPhones* Delivered', 'iphonebay' ) ) ); ?></h1>
					<p class="hero-sub"><?php esc_html_e( 'Ex-UK & Brand New iPhones. Every model. Every colour. Verified genuine, battery health guaranteed.', 'iphonebay' ); ?></p>
					<div class="hero-ctas">
						<a href="<?php echo esc_url( $shop_url ); ?>" class="btn-primary"><?php esc_html_e( 'Shop Now', 'iphonebay' ); ?> &rarr;</a>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=hero_slide' ) ); ?>" class="btn-ghost"><?php esc_html_e( 'Add Hero Slides', 'iphonebay' ); ?></a>
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
