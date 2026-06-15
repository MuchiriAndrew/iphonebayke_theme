<?php
/**
 * Home — customer reviews (Testimonials CPT, falls back to samples).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$testimonials = get_posts( array(
	'post_type'      => 'testimonial',
	'posts_per_page' => 3,
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
) );

$reviews = array();

if ( $testimonials ) {
	foreach ( $testimonials as $t ) {
		$reviews[] = array(
			'text'   => wp_strip_all_tags( $t->post_content ),
			'author' => get_post_meta( $t->ID, 'iphonebay_author', true ) ? get_post_meta( $t->ID, 'iphonebay_author', true ) : get_the_title( $t ),
			'loc'    => get_post_meta( $t->ID, 'iphonebay_location', true ),
			'rating' => (int) ( get_post_meta( $t->ID, 'iphonebay_rating', true ) ?: 5 ),
		);
	}
} else {
	$reviews = array(
		array( 'text' => 'Ordered an iPhone 14 Ex-UK on a Wednesday, it arrived Thursday morning. Battery at 91% as described and the phone felt brand new. Will definitely buy again.', 'author' => 'Wanjiku K.', 'loc' => 'Westlands, Nairobi', 'rating' => 5 ),
		array( 'text' => 'Best place to buy phones in Nairobi. Got my Samsung S23 at a price 20K cheaper than everywhere else. Legit, fast, and M-Pesa checkout is seamless.', 'author' => 'Brian M.', 'loc' => 'Thika Road, Nairobi', 'rating' => 5 ),
		array( 'text' => 'The trade-in process was incredibly smooth. Got KES 32,000 for my old iPhone 12 and used it towards an iPhone 15. Whole thing took less than 30 minutes.', 'author' => 'Amina O.', 'loc' => 'Kilimani, Nairobi', 'rating' => 5 ),
	);
}

if ( empty( $reviews ) ) {
	return;
}

$initials = function ( $name ) {
	$parts = preg_split( '/\s+/', trim( $name ) );
	$i = '';
	foreach ( array_slice( $parts, 0, 2 ) as $p ) {
		$i .= strtoupper( substr( $p, 0, 1 ) );
	}
	return $i ? $i : '★';
};
?>
<div class="section">
	<div class="section-header">
		<h2 class="section-title"><?php echo wp_kses_post( iphonebay_highlight( __( 'What *Customers Say*', 'iphonebay' ) ) ); ?></h2>
	</div>
	<div class="reviews-grid">
		<?php foreach ( $reviews as $r ) : ?>
			<div class="review-card">
				<div class="review-stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'iphonebay' ), $r['rating'] ) ); ?>"><?php echo esc_html( str_repeat( '★', max( 1, min( 5, $r['rating'] ) ) ) ); ?></div>
				<p class="review-text">&ldquo;<?php echo esc_html( $r['text'] ); ?>&rdquo;</p>
				<div class="review-author">
					<div class="review-avatar"><?php echo esc_html( $initials( $r['author'] ) ); ?></div>
					<div>
						<div class="review-author-name"><?php echo esc_html( $r['author'] ); ?></div>
						<?php if ( $r['loc'] ) : ?><div class="review-author-loc"><?php echo esc_html( $r['loc'] ); ?></div><?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
