<?php
/**
 * Update site content: real product descriptions + core pages.
 *
 * Run: wp eval-file tools/update-site-content.php --path=<wp-root>
 *
 * Idempotent — matches by title/slug and overwrites. Safe to re-run.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ============================================================
 * PRODUCTS — descriptions for the five catalog units.
 * Every description carries: condition, battery health, storage,
 * warranty, what's in the box, and one reason to buy.
 * ============================================================ */

$products = array(

	'Apple iPhone 11' => array(
		'short' => 'Ex-UK iPhone 11, 128GB config available. Battery health verified at testing — 85% minimum guaranteed, most units land 88–94%. Face ID, 6.1&Prime; Liquid Retina display, A13 Bionic. 6-month warranty.',
		'desc'  => "<p>The iPhone 11 is the least expensive way to get a Face ID iPhone that still runs the latest iOS without breaking a sweat — and it's the phone we recommend most often as a first iPhone or a reliable backup.</p>
<h3>Condition</h3>
<p>Grade A Ex-UK. Light wear only — no cracks, no screen replacements, no repaired logic boards. Every unit is one owner from the UK, imported directly by us and inspected in Nairobi before listing.</p>
<h3>Battery health</h3>
<p>Every unit's actual battery reading is taken at testing. We guarantee a minimum of 85%; most of our current iPhone 11 stock tests between 88% and 94%. The exact figure for your unit is confirmed before dispatch.</p>
<h3>Storage</h3>
<p>128GB — enough for roughly 25,000 photos plus apps and music, without the constant iCloud nag.</p>
<h3>What's in the box</h3>
<p>Device, Lightning charging cable, SIM ejector, warranty card. (No earphones — Apple stopped including them, and we won't pretend otherwise.)</p>
<h3>Warranty</h3>
<p>6 months, in writing. Battery failures, charging-port faults, screen or speaker defects — repaired, replaced, or refunded at your choice.</p>",
	),

	'Apple iPhone 12' => array(
		'short' => 'Ex-UK iPhone 12, 128GB config available. Battery health verified — 85% minimum guaranteed. 5G, Super Retina XDR OLED, A14 Bionic, Night mode selfies. 6-month warranty.',
		'desc'  => "<p>The iPhone 12 was the first iPhone with 5G and the first standard model with an OLED panel — and three generations later, that screen still looks every bit as sharp. Night-mode photos are a clear step up from the 11.</p>
<h3>Condition</h3>
<p>Grade A Ex-UK. Light wear only — original screen, no repairs, no water damage. Imported directly and inspected in Nairobi before listing.</p>
<h3>Battery health</h3>
<p>Each unit's actual reading is taken at testing. Minimum 85% guaranteed; current stock mostly tests 88–93%. Your unit's exact figure is confirmed before dispatch.</p>
<h3>Storage</h3>
<p>128GB — comfortable for photos, WhatsApp media and a full app load-out.</p>
<h3>What's in the box</h3>
<p>Device, Lightning charging cable, SIM ejector, warranty card. No earphones included.</p>
<h3>Warranty</h3>
<p>6 months, in writing — repairs, replacement or refund on any hardware fault.</p>",
	),

	'Apple iPhone 13' => array(
		'short' => 'Ex-UK iPhone 13, 128GB config available. Battery health verified — 85% minimum guaranteed, typically 90%+. A15 Bionic, the biggest battery of the standard models. 6-month warranty.',
		'desc'  => "<p>If battery life is what you care about most, the iPhone 13 is the one. It carries the biggest battery of any standard-size iPhone we stock, and in day-to-day Nairobi use — M-Pesa, WhatsApp, Google Maps, a full day off a charger — it regularly outlasts the iPhone 12 by two hours or more of screen-on time.</p>
<h3>Condition</h3>
<p>Grade A Ex-UK. Light wear only — original screen, no crack repairs, no board-level work.</p>
<h3>Battery health</h3>
<p>Actual reading taken at testing; minimum 85% guaranteed, with current stock typically at 90% or better. Your unit's exact figure is confirmed before dispatch.</p>
<h3>Storage</h3>
<p>128GB as standard.</p>
<h3>What's in the box</h3>
<p>Device, Lightning charging cable, SIM ejector, warranty card. No earphones included.</p>
<h3>Warranty</h3>
<p>6 months, in writing — repairs, replacement or refund on any hardware fault.</p>",
	),

	'Apple iPhone 14 Pro' => array(
		'short' => 'Ex-UK iPhone 14 Pro, 128GB config available. Battery health verified — 85% minimum guaranteed. ProMotion 120Hz, Dynamic Island, 48MP main camera, Always-On display. 6-month warranty.',
		'desc'  => "<p>The 14 Pro is the only phone in our current lineup with ProMotion — the 120Hz display that makes every scroll and swipe feel noticeably smoother. Add the Dynamic Island, an Always-On display and a 48MP main camera that shoots genuine 3× telephoto-quality crops, and it remains a Pro experience at a non-Pro price.</p>
<h3>Condition</h3>
<p>Grade A Ex-UK. Light wear only — original Apple display and parts, no repairs, no liquid damage.</p>
<h3>Battery health</h3>
<p>Actual reading taken at testing; minimum 85% guaranteed. Your unit's exact figure is confirmed before dispatch.</p>
<h3>Storage</h3>
<p>128GB as standard.</p>
<h3>What's in the box</h3>
<p>Device, Lightning charging cable, SIM ejector, warranty card. No earphones included.</p>
<h3>Warranty</h3>
<p>6 months, in writing — repairs, replacement or refund on any hardware fault.</p>",
	),

	'Apple iPhone 15' => array(
		'short' => 'Sealed, brand-new iPhone 15, 128GB config available. 100% battery by definition. USB-C charging, Dynamic Island, 48MP camera. 12-month warranty.',
		'desc'  => "<p>A sealed iPhone 15: zero previous owners, 100% battery by definition, and USB-C — you can charge it with the same cable as your iPad, MacBook or Android. It carries the Dynamic Island and 48MP main camera of the Pro line in a lighter aluminium body.</p>
<h3>Condition</h3>
<p>Brand new, factory-sealed. Not refurbished, not Ex-UK, not opened.</p>
<h3>Battery health</h3>
<p>100% — a new unit has never completed a charge cycle.</p>
<h3>Storage</h3>
<p>128GB as standard.</p>
<h3>What's in the box</h3>
<p>Sealed box with device, USB-C charging cable and documentation, plus our 12-month warranty card.</p>
<h3>Warranty</h3>
<p>12 months, in writing — double the Ex-UK cover, because there's nothing to wear out.</p>",
	),
);

foreach ( $products as $title => $copy ) {
	// The 14 Pro dummy was created as plain "Apple iPhone 14" — look it up
	// under the old title so it gets the new copy and the rename together.
	$lookup_titles = array( $title );
	if ( 'Apple iPhone 14 Pro' === $title ) {
		$lookup_titles[] = 'Apple iPhone 14';
	}

	foreach ( $lookup_titles as $lookup ) {
		$post = get_page_by_path( sanitize_title( $lookup ), OBJECT, 'product' );
		if ( ! $post ) {
			// Products may carry custom slugs — fall back to a title match.
			$existing = get_posts( array(
				'post_type'      => 'product',
				'title'          => $lookup,
				'posts_per_page' => 1,
				'post_status'    => 'any',
			) );
			$post = $existing ? $existing[0] : null;
		}
		if ( $post ) {
			break;
		}
	}
	if ( ! $post ) {
		WP_CLI::log( "SKIP — product not found: {$title}" );
		continue;
	}

	$existing_title = $post->post_title;
	wp_update_post( array(
		'ID'           => $post->ID,
		'post_title'   => $title,
		'post_content' => $copy['desc'],
	) );
	update_post_meta( $post->ID, '_short_description', $copy['short'] );

	WP_CLI::log( "OK — product updated: {$existing_title} → {$title} (#{$post->ID})" );
}

/* ============================================================
 * PAGES — About, How We Test, Warranty & Returns, FAQs.
 * ============================================================ */

$pages = array(

	'about' => array(
		'title' => 'About',
		'slug'  => 'about',
		'body'  => "<p>iPhoneBayKE started in 2021 as a WhatsApp list — a few phones a month, sold to friends and colleagues from a desk in the Nairobi CBD. We grew the way we did for one reason: every phone we sold was exactly what we said it was. The battery health we quoted was the battery health you got. The warranty card in the box was honoured, every time, without a fight.</p>
<p>Today we sell Ex-UK and sealed-new iPhones, Samsung and Google Pixel devices from our CBD office, and every unit still goes through the same 30-point inspection before it's listed. Ex-UK devices are imported directly — no middle-chain local re-sellers — which is how we keep prices below what the big retail counters charge for the same grade.</p>
<p>We're a small team, we answer our own WhatsApp, and we'd rather sell you the phone that fits your budget than upsell you one that doesn't. If a cheaper model is the right call for you, we'll say so.</p>",
	),

	'how-we-test' => array(
		'title' => 'How We Test',
		'slug'  => 'how-we-test',
		'body'  => "<p>Every device — Ex-UK or trade-in — goes through the same 30-point inspection in our CBD office before it's listed. Nothing ships, and nothing gets a warranty card, until it passes all of it.</p>
<h2>The 30-point inspection</h2>
<h3>1. Identity &amp; history</h3>
<p>We verify the IMEI against Apple's own records and screen every device against stolen-device databases. A locked, blacklisted or carrier-barred phone never makes it past this step.</p>
<h3>2. Parts authenticity</h3>
<p>We open the diagnostics and check that the screen, battery and camera are original Apple parts. Units with third-party replacements — or 'parts unknown' warnings — are rejected or clearly listed as repaired grade, never sold as Grade A.</p>
<h3>3. Cosmetic grading</h3>
<p>Under bright light, we grade the housing: Grade A means light wear only, no cracks, no dents on the frame edges. Anything below A goes to trade-in or clearance, never to this shop.</p>
<h3>4. Battery health</h3>
<p>The actual reading, not an estimate — we record each unit's figure and confirm it to you before dispatch. Ex-UK units are guaranteed at 85% minimum.</p>
<h3>5. Screen</h3>
<p>Dead-pixel sweep across full white, black and colour fills; touch-grid test on every region of the display; True Tone and auto-brightness verified.</p>
<h3>6. Cameras</h3>
<p>Every lens — wide, ultra-wide, telephoto where fitted — plus flash, portrait mode and video. We test both front and rear in low light, which is where weak cameras show up.</p>
<h3>7. Face ID &amp; sensors</h3>
<p>Face ID enrol, unlock and Apple Pay; proximity, gyro and compass sensors checked.</p>
<h3>8. Audio</h3>
<p>Earpiece, loudspeaker, both microphones, and the haptic engine — tested on an actual call, not just a ringtone.</p>
<h3>9. Charging &amp; ports</h3>
<p>Wired charging from 0 to a confirmed charge rate, wireless charging where fitted, SIM tray, and every port under a magnifier.</p>
<h3>10. Buttons &amp; seals</h3>
<p>Every physical button tested, and a visual inspection of water-resistance seals — we never claim a used phone is waterproof, and neither should you.</p>
<h3>11. Network &amp; SIM</h3>
<p>Live SIM in: 4G/5G registration, calls, SMS and M-Pesa app data on Safaricom, Airtel and Telkom lines.</p>
<h3>12. Final reset</h3>
<p>The device is erased, updated to the latest supported iOS, UV-cleaned, fitted with a fresh screen protector and packed with its warranty card.</p>
<h2>If something fails later</h2>
<p>The inspection is why we can put a written warranty behind every device. If a fault slips through, the warranty page explains exactly how a claim works — and it has no small print beyond what's written there.</p>",
	),

	'warranty-returns' => array(
		'title' => 'Warranty & Returns',
		'slug'  => 'warranty-returns',
		'body'  => "<p>Every device we sell carries a written warranty card in the box. This page is the full policy — there is no smaller version of it that appears later.</p>
<h2>What's covered</h2>
<p>Hardware faults that develop through normal use during the warranty period: battery failure, charging-port faults, screen or touch defects, speaker and microphone faults, camera failure, Face ID and sensor faults. If we can repair it, we repair it. If we can't, we replace the device with the same model and grade — or refund you in full if we have no replacement. Your choice between replacement and refund where both are possible.</p>
<h2>Warranty periods</h2>
<p>Ex-UK devices: 6 months from delivery. Sealed new devices: 12 months from delivery.</p>
<h2>What's not covered</h2>
<p>Accidental damage — cracked screens from drops, liquid damage, damage from third-party repairs or unauthorised opening. Loss or theft. Cosmetic wear that develops over time. Software issues after you've reset, jailbroken or flashed the device. The water-resistance of any used iPhone is not guaranteed — treat every Ex-UK device as water-resistant in name only.</p>
<h2>How to make a claim</h2>
<p>WhatsApp us your order number and a description of the fault. If you're in Nairobi, bring the device to our CBD office — most claims are diagnosed while you wait. Outside Nairobi, courier it to us; we cover the return leg. Repairs are typically turned around in 1–3 business days. If the fault was present when the device left us, transport is on us too.</p>
<h2>Returns</h2>
<p>You have 7 days from delivery to return a device for a full refund if it is not as described — wrong battery health, wrong grade, anything that doesn't match what was confirmed to you before dispatch. The device must come back in the condition it left, with everything that was in the box. Refunds are sent to your M-Pesa within 48 hours of the device passing inspection. Change-of-mind returns within 7 days are accepted for store credit, provided the device is in as-delivered condition.</p>
<h2>Trade-in adjustments</h2>
<p>If you bought a device using a trade-in and later claim a refund, the refund is the amount you paid after the trade-in credit — your traded device is not returned.</p>",
	),

	'faqs' => array(
		'title' => 'FAQs',
		'slug'  => 'faqs',
		'body'  => "<h3>What does “Ex-UK” mean?</h3>
<p>Ex-UK devices are genuine iPhones imported from the UK — usually one-owner units that came off carrier contracts. They're not the same as “refurbished”: most of our Ex-UK stock has never been repaired at all. Every unit is inspected here in Nairobi, graded, and carries our written warranty.</p>
<h3>Are these phones genuine?</h3>
<p>Yes — and verified, not promised. We check every device's IMEI against Apple's records, screen against stolen-device databases, and confirm the screen, battery and cameras are original parts. Clones and part-swapped units are rejected before they reach the shop.</p>
<h3>What battery health will my phone have?</h3>
<p>Ex-UK devices are guaranteed at 85% or better, and the actual reading for your unit is confirmed before dispatch — most stock tests 88–94%. Sealed new devices are 100% by definition.</p>
<h3>Do you deliver outside Nairobi?</h3>
<p>Yes. Orders confirmed before 3pm are delivered the same day within the CBD and nearby estates. Countrywide, we courier next-day to Mombasa, Kisumu, Nakuru, Eldoret and all major towns — delivery is free on orders above KES 50,000.</p>
<h3>How can I pay?</h3>
<p>M-Pesa at checkout (you'll get the STK prompt right on the checkout page), bank transfer, Visa/Mastercard, or cash when you pick up at our CBD office.</p>
<h3>What exactly does the warranty cover?</h3>
<p>Six months on Ex-UK devices, twelve on sealed new — battery, charging port, screen, speakers, cameras, Face ID; the hardware faults that develop through normal use. Full details are on the Warranty &amp; Returns page, and the same terms are printed on the card in your box.</p>
<h3>Can I return a phone?</h3>
<p>Within 7 days, if it's not as described, you get a full refund to M-Pesa within 48 hours of inspection. Change-of-mind returns within 7 days are accepted for store credit on as-delivered devices.</p>
<h3>Can I trade in my current phone?</h3>
<p>Yes — against your purchase, or straight cash to M-Pesa. Tell us the model, storage and condition on WhatsApp or the trade-in form and you'll get a quote the same day. As a current example, an iPhone 12 64GB in good condition fetches up to KES 27,000.</p>",
	),
);

foreach ( $pages as $key => $page ) {
	$existing = get_page_by_path( $page['slug'], OBJECT, 'page' );
	if ( $existing ) {
		wp_update_post( array(
			'ID'           => $existing->ID,
			'post_content' => $page['body'],
			'post_title'   => $page['title'],
			'post_status'  => 'publish',
		) );
		WP_CLI::log( "OK — page updated: {$page['title']} (#{$existing->ID})" );
	} else {
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_title'   => $page['title'],
			'post_name'    => $page['slug'],
			'post_content' => $page['body'],
			'post_status'  => 'publish',
		) );
		WP_CLI::log( $id ? "OK — page created: {$page['title']} (#{$id})" : "FAIL — page create: {$page['title']}" );
	}
}

WP_CLI::success( 'Site content updated.' );