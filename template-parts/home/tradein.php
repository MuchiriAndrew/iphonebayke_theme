<?php
/**
 * Home — Trade-In section (editable in Customizer → Trade-In Section).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_id  = iphonebay_opt( 'tradein_image' );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
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
			<div class="tradein-eyebrow"><svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg> <?php echo esc_html( iphonebay_opt( 'tradein_eyebrow', 'Trade-In Program' ) ); ?></div>
			<h2 class="tradein-title"><?php echo wp_kses_post( iphonebay_highlight( iphonebay_opt( 'tradein_title', 'Your Old Phone is *Worth More*' ) ) ); ?></h2>
			<p class="tradein-desc"><?php echo esc_html( iphonebay_opt( 'tradein_desc', "Don't let your old device collect dust. Trade it in and get instant credit towards your next purchase — or cash via M-Pesa." ) ); ?></p>
			<div class="tradein-steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<div class="tradein-step"><div class="tradein-step-n"><?php echo esc_html( $i + 1 ); ?></div><div class="tradein-step-t"><?php echo esc_html( $step ); ?></div></div>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="btn-primary" style="width:fit-content;"><?php echo esc_html( $btn_text ); ?> &rarr;</a>
		</div>
		<div class="tradein-image">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Trade in your phone', 'iphonebay' ); ?>">
			<?php endif; ?>
		</div>
	</div>
</div>
