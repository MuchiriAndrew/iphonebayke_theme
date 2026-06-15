<?php
/**
 * Footer.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iphonebay_logo_id = get_theme_mod( 'custom_logo' );
$iphonebay_privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : home_url( '/privacy-policy/' );
$iphonebay_terms_page  = get_page_by_path( 'terms-of-service' );
$iphonebay_terms_url   = $iphonebay_terms_page ? get_permalink( $iphonebay_terms_page ) : home_url( '/terms-of-service/' );
$iphonebay_cookies_url = $iphonebay_privacy_url ? $iphonebay_privacy_url . '#cookies' : home_url( '/privacy-policy/#cookies' );
?>

<footer class="footer">
	<div class="footer-inner">
		<div class="footer-grid">
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-link">
					<?php
					if ( $iphonebay_logo_id ) {
						echo wp_get_attachment_image( $iphonebay_logo_id, 'full', false, array( 'class' => 'footer-logo-img', 'alt' => get_bloginfo( 'name' ) ) );
					} else {
						echo '<img class="footer-logo-img" src="' . esc_url( IPHONEBAY_URI . '/assets/images/logo.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
					}
					?>
				</a>
				<p class="footer-desc"><?php echo esc_html( iphonebay_opt( 'footer_desc', "Kenya's trusted source for genuine iPhones, Samsung, and premium smartphones. Ex-UK, Brand New, and Refurbished — all verified." ) ); ?></p>
				<?php echo iphonebay_newsletter_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="footer-socials"><?php echo iphonebay_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>

			<div>
				<div class="footer-col-head"><?php esc_html_e( 'Products', 'iphonebay' ); ?></div>
				<?php
				if ( has_nav_menu( 'footer_products' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_products', 'container' => false, 'menu_class' => 'footer-links', 'depth' => 1 ) );
				} else {
					echo '<ul class="footer-links">';
					$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
					foreach ( array( 'iPhones', 'Samsung', 'Google Pixel', 'Accessories' ) as $item ) {
						echo '<li><a href="' . esc_url( $shop ) . '">' . esc_html( $item ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>
			</div>

			<div>
				<div class="footer-col-head"><?php esc_html_e( 'Support', 'iphonebay' ); ?></div>
				<?php
				if ( has_nav_menu( 'footer_support' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_support', 'container' => false, 'menu_class' => 'footer-links', 'depth' => 1 ) );
				} else {
					echo '<ul class="footer-links">';
					foreach ( array( 'Track My Order', 'Trade-In', 'Warranty Claims', 'WhatsApp Us' ) as $item ) {
						echo '<li><a href="#">' . esc_html( $item ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>
			</div>

			<div>
				<div class="footer-col-head"><?php esc_html_e( 'Company', 'iphonebay' ); ?></div>
				<?php
				if ( has_nav_menu( 'footer_company' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer_company', 'container' => false, 'menu_class' => 'footer-links', 'depth' => 1 ) );
				} else {
					echo '<ul class="footer-links">';
					foreach ( array( 'About Us', 'Sell to Us', 'Privacy Policy', 'Terms of Service' ) as $item ) {
						echo '<li><a href="#">' . esc_html( $item ) . '</a></li>';
					}
					echo '</ul>';
				}
				?>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved. Nairobi, Kenya.', 'iphonebay' ); ?></div>
			<div class="footer-legal">
				<a href="<?php echo esc_url( $iphonebay_privacy_url ); ?>"><?php esc_html_e( 'Privacy', 'iphonebay' ); ?></a>
				<a href="<?php echo esc_url( $iphonebay_terms_url ); ?>"><?php esc_html_e( 'Terms', 'iphonebay' ); ?></a>
				<a href="<?php echo esc_url( $iphonebay_cookies_url ); ?>"><?php esc_html_e( 'Cookies', 'iphonebay' ); ?></a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
