<?php
/**
 * Template Name: Categories Page
 * Description: Full category hub — browse the catalog by device family.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$preferred_cards = array(
	'iphones'        => array(
		'label' => __( 'Most Popular', 'iphonebay' ),
		'title' => __( 'iPhones', 'iphonebay' ),
		'sub'   => __( 'Ex-UK · Refurbished · Brand New', 'iphonebay' ),
		'copy'  => __( 'Every unit passes a 30-point inspection before it is listed — battery health, screen, cameras, Face ID, IMEI. Ex-UK and sealed-new, across every current model.', 'iphonebay' ),
	),
	'samsung-phones' => array(
		'label' => __( 'Android Flagship', 'iphonebay' ),
		'title' => __( 'Samsung', 'iphonebay' ),
		'sub'   => __( 'S-Series & A-Series', 'iphonebay' ),
		'copy'  => __( 'Galaxy S and A-Series, checked the same way as everything else we sell — verified genuine, battery health guaranteed, written warranty included.', 'iphonebay' ),
	),
	'google-pixel'   => array(
		'label' => __( 'Pure Android', 'iphonebay' ),
		'title' => __( 'Google Pixel', 'iphonebay' ),
		'sub'   => __( 'Pixel 7 · 8 · 9 Series', 'iphonebay' ),
		'copy'  => __( 'Stock Android, Google\'s own camera tuning. Popular with buyers who want flagship photography without the iOS/Samsung ecosystem lock-in.', 'iphonebay' ),
	),
	'accessories'    => array(
		'label' => __( 'Complete Your Setup', 'iphonebay' ),
		'title' => __( 'Accessories', 'iphonebay' ),
		'sub'   => __( 'AirPods · Chargers · Cases', 'iphonebay' ),
		'copy'  => __( 'Cases, chargers, and earbuds picked to match what we sell — not a generic accessories bin. Add them at checkout or shop them on their own.', 'iphonebay' ),
	),
);

$cards = array();
if ( taxonomy_exists( 'product_cat' ) ) {
	foreach ( $preferred_cards as $slug => $config ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$cards[] = array(
				'term'   => $term,
				'config' => $config,
			);
		}
	}
}
?>
<main id="main" class="site-main">
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="page-hero page-hero--split">
			<div>
				<div class="section-eyebrow"><?php esc_html_e( 'Browse the catalog', 'iphonebay' ); ?></div>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
			<p class="page-hero-copy"><?php esc_html_e( 'Every category gets the same 30-point inspection and written warranty — pick the device family, we handle the checking.', 'iphonebay' ); ?></p>
		</div>

		<?php if ( $cards ) : ?>
			<div class="cat-jump page-shell">
				<?php foreach ( $cards as $card ) :
					$cat      = $card['term'];
					$config   = $card['config'];
					$thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					$jump_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
					?>
					<a class="cat-jump-item" href="#cat-<?php echo esc_attr( $cat->slug ); ?>">
						<img src="<?php echo esc_url( $jump_url ); ?>" alt="" loading="lazy">
						<span><?php echo esc_html( $config['title'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="entry-wrap page-shell">
			<div class="cat-page-list">
				<?php foreach ( $cards as $card ) :
					$cat       = $card['term'];
					$config    = $card['config'];
					$thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
					$image_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : wc_placeholder_img_src( 'large' );
					?>
					<a class="cat-page-row" id="cat-<?php echo esc_attr( $cat->slug ); ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
						<div class="cat-page-row-media">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy">
						</div>
						<div class="cat-page-row-body">
							<div class="cat-page-row-label"><?php echo esc_html( $config['label'] ); ?></div>
							<h2 class="cat-page-row-title"><?php echo esc_html( $config['title'] ); ?></h2>
							<p class="cat-page-row-sub"><?php echo esc_html( $config['sub'] ); ?><?php if ( $cat->count > 0 ) : ?> &middot; <?php echo esc_html( number_format_i18n( $cat->count ) ); ?> <?php echo esc_html( _n( 'item', 'items', $cat->count, 'iphonebay' ) ); ?><?php endif; ?></p>
							<p class="cat-page-row-copy"><?php echo esc_html( $config['copy'] ); ?></p>
							<span class="cat-page-row-cta"><?php esc_html_e( 'Shop', 'iphonebay' ); ?> <?php echo esc_html( $config['title'] ); ?> &rarr;</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
