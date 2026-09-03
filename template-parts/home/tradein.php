<?php
/**
 * Home — Trade-In section (editable in Customizer → Trade-In Section).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_ids = array_filter( array(
	iphonebay_opt( 'tradein_image' ),
	iphonebay_opt( 'tradein_image2' ),
	iphonebay_opt( 'tradein_image3' ),
) );
$btn_text  = iphonebay_opt( 'tradein_btn_text', 'Get a Quote' );
$btn_url   = iphonebay_resolve_cta_url( $btn_text, iphonebay_opt( 'tradein_btn_url', '#' ) );
$steps     = array(
	iphonebay_opt( 'tradein_step1', 'Tell us your phone model & condition' ),
	iphonebay_opt( 'tradein_step2', 'Get an instant quote — no obligation' ),
	iphonebay_opt( 'tradein_step3', 'Drop off or courier, get paid via M-Pesa' ),
);
?>
<div class="tradein" id="tradein">
	<div class="tradein-inner">
		<div class="tradein-content">
			<div class="tradein-eyebrow"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg> <?php echo esc_html( iphonebay_opt( 'tradein_eyebrow', 'Trade-In' ) ); ?></div>
			<h2 class="tradein-title"><?php echo wp_kses_post( iphonebay_highlight( iphonebay_opt( 'tradein_title', 'Your old phone is *worth more* than a drawer' ) ) ); ?></h2>
			<p class="tradein-desc"><?php echo esc_html( iphonebay_opt( 'tradein_desc', 'Trade in the phone sitting in your drawer and put the value towards your next one. Example: an iPhone 12 64GB in good condition currently fetches up to KES 27,000 — paid to your M-Pesa the same day we verify it.' ) ); ?></p>
			<div class="tradein-steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<div class="tradein-step"><div class="tradein-step-n"><?php echo esc_html( $i + 1 ); ?></div><div class="tradein-step-t"><?php echo esc_html( $step ); ?></div></div>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="btn-primary" style="width:fit-content;"><?php echo esc_html( $btn_text ); ?> &rarr;</a>
		</div>
		<div class="tradein-image" id="tradein-slider">
			<?php if ( $image_ids ) : ?>
				<?php foreach ( $image_ids as $index => $image_id ) :
					$image_url = wp_get_attachment_image_url( $image_id, 'large' );
					if ( ! $image_url ) {
						continue;
					}
					?>
					<div class="tradein-slide<?php echo 0 === $index ? ' active' : ''; ?>">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Trade in your phone', 'iphonebay' ); ?>" <?php echo 0 === $index ? '' : 'loading="lazy"'; ?>>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if ( count( $image_ids ) > 1 ) : ?>
				<div class="tradein-slider-dots" role="tablist" aria-label="<?php esc_attr_e( 'Trade-in image slides', 'iphonebay' ); ?>">
					<?php foreach ( $image_ids as $index => $image_id ) : ?>
						<button class="tradein-slider-dot<?php echo 0 === $index ? ' active' : ''; ?>" role="tab" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'iphonebay' ), $index + 1 ) ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
