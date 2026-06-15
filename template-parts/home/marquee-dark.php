<?php
/**
 * Home — dark marquee (product line-up). Pulls top product category names,
 * falls back to a default brand string.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$apple = IPHONEBAY_URI . '/assets/images/apple-logo.png';
$sets  = array(
	array( 'iPhone 15', 'iPhone 14', 'iPhone 13', 'iPhone 12' ),
	array( 'Samsung Galaxy S24', 'A-Series' ),
	array( 'Google Pixel 8', '9 Series' ),
	array( 'AirPods', 'Apple Watch', 'iPad' ),
);

$render_set = function () use ( $sets, $apple ) {
	$out = '';
	foreach ( $sets as $index => $items ) {
		$out .= '<span class="marquee-item">';
		if ( 0 === $index ) {
			$out .= '<img class="marquee-logo" src="' . esc_url( $apple ) . '" alt="">';
		}
		$out .= '<span class="marquee-brand">' . esc_html( implode( ' · ', $items ) ) . '</span></span>';
		$out .= '<span class="marquee-sep">&middot;</span>';
	}
	return $out;
};
?>
<div class="dark-marquee" aria-hidden="true">
	<div class="marquee-track">
		<?php
		echo $render_set(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $render_set(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $render_set(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $render_set(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>
