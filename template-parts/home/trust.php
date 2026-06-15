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
		'name' => __( 'Genuine Devices', 'iphonebay' ),
		'desc' => __( 'Every phone IMEI-checked and verified authentic. No clones, no grey imports.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="m16 8 5 2v6h-5V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
		'name' => __( 'Nairobi Delivery', 'iphonebay' ),
		'desc' => __( 'Same-day CBD delivery. County-wide next day. Free above KES 50K.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
		'name' => __( 'M-Pesa Payments', 'iphonebay' ),
		'desc' => __( 'Pay via M-Pesa, bank transfer, or card. Instant confirmation.', 'iphonebay' ),
	),
	array(
		'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
		'name' => __( '6-Month Warranty', 'iphonebay' ),
		'desc' => __( 'All devices covered. Hardware faults replaced or refunded.', 'iphonebay' ),
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
