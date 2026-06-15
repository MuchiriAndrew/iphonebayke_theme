<?php
/**
 * Home — gold marquee bar (editable in Customizer → Marquee Bar).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$raw   = iphonebay_opt( 'marquee', 'Apple | Samsung | Google Pixel | M-Pesa Accepted | Genuine Devices | Nairobi Delivery | 6-Month Warranty | Battery Verified' );
$items = array_filter( array_map( 'trim', explode( '|', $raw ) ) );
if ( empty( $items ) ) {
	return;
}
$apple = IPHONEBAY_URI . '/assets/images/apple-logo.png';

$render_set = function () use ( $items, $apple ) {
	$out = '';
	foreach ( $items as $item ) {
		$out .= '<span class="marquee-item">';
		if ( 'apple' === strtolower( $item ) ) {
			$out .= '<img class="marquee-logo" src="' . esc_url( $apple ) . '" alt="">';
		}
		$out .= '<span class="marquee-brand">' . esc_html( $item ) . '</span></span>';
		$out .= '<span class="marquee-sep">&middot;</span>';
	}
	return $out;
};
?>
<div class="marquee-bar" aria-hidden="true">
	<div class="marquee-track">
		<?php
		echo $render_set(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $render_set(); // duplicate for seamless loop. phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>
