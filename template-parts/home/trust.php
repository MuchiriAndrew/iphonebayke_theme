<?php
/**
 * Home — trust strip.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = array(
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
		'name' => __( '30-Point Check', 'iphonebay' ),
		'desc' => __( 'Screen, battery, cameras, Face ID, speakers and IMEI — inspected one by one before a phone is listed.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
		'name' => __( '6-Month Warranty', 'iphonebay' ),
		'desc' => __( 'Six months on Ex-UK devices, twelve on sealed new. Hardware faults are repaired, replaced or refunded.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
		'name' => __( 'M-Pesa Accepted', 'iphonebay' ),
		'desc' => __( 'Pay with M-Pesa, bank transfer, card, or cash on pickup at our CBD office.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h13l4 4v6a2 2 0 0 1-2 2h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>',
		'name' => __( 'Same-Day CBD Delivery', 'iphonebay' ),
		'desc' => __( 'Order before 3pm and get your phone the same day within Nairobi. Next-day countrywide.', 'iphonebay' ),
	),
);
?>
<div class="trust-strip">
	<div class="trust-inner">
		<?php foreach ( $items as $item ) : ?>
			<div class="trust-item">
				<div class="trust-icon"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div class="trust-name"><?php echo esc_html( $item['name'] ); ?></div>
				<div class="trust-desc"><?php echo esc_html( $item['desc'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
