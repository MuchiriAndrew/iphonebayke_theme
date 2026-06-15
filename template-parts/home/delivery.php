<?php
/**
 * Home — Delivery & Payment (editable in Customizer → Delivery & Payment).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_id  = iphonebay_opt( 'delivery_image' );
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
?>
<div class="delivery-wrap">
	<div class="delivery-inner">
		<div>
			<h2 class="delivery-title"><?php echo wp_kses_post( nl2br( esc_html( iphonebay_opt( 'delivery_title', "Delivery &\nPayment" ) ) ) ); ?></h2>
			<p class="delivery-desc"><?php echo esc_html( iphonebay_opt( 'delivery_desc', 'We deliver anywhere in Kenya. Same-day delivery within Nairobi CBD and estates. Next-day for Mombasa, Kisumu, Eldoret, and all major towns. Free delivery on orders above KES 50,000.' ) ); ?></p>
			<div class="payment-methods">
				<div class="payment-chip mpesa"><svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><circle cx="5" cy="5" r="5"/></svg> <?php esc_html_e( 'M-Pesa', 'iphonebay' ); ?></div>
				<div class="payment-chip"><?php esc_html_e( 'Bank Transfer', 'iphonebay' ); ?></div>
				<div class="payment-chip"><?php esc_html_e( 'Visa / Mastercard', 'iphonebay' ); ?></div>
				<div class="payment-chip"><?php esc_html_e( 'Cash on Pickup', 'iphonebay' ); ?></div>
			</div>
		</div>
		<div class="delivery-img">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Fast delivery across Kenya', 'iphonebay' ); ?>">
			<?php else : ?>
				<img src="<?php echo esc_url( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'large' ) : IPHONEBAY_URI . '/assets/images/logo.png' ); ?>" alt="">
			<?php endif; ?>
		</div>
	</div>
</div>
