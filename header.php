<?php
/**
 * Header — nav + off-canvas.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iphonebay_logo_id = get_theme_mod( 'custom_logo' );
$iphonebay_cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$iphonebay_cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$iphonebay_search_url = function_exists( 'iphonebay_product_search_url' ) ? iphonebay_product_search_url() : home_url( '/shop/' );
$iphonebay_account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$iphonebay_account_label = is_user_logged_in() ? __( 'My account', 'iphonebay' ) : __( 'Login or register', 'iphonebay' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'iphonebay' ); ?></a>

<header class="nav" id="nav">
	<div class="nav-inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			if ( $iphonebay_logo_id ) {
				echo wp_get_attachment_image( $iphonebay_logo_id, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				echo '<img src="' . esc_url( IPHONEBAY_URI . '/assets/images/logo.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
			}
			?>
		</a>

		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'nav-links',
			'menu_id'        => 'primary-menu',
			'depth'          => 2,
			'walker'         => new IphoneBay_Nav_Walker(),
			'fallback_cb'    => 'iphonebay_default_menu',
		) );
		?>

		<div class="nav-actions">
			<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
				<button type="button" class="nav-icon-btn nav-search-toggle" id="searchToggle" aria-label="<?php esc_attr_e( 'Search products', 'iphonebay' ); ?>" aria-expanded="false" aria-controls="siteSearch">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
				</button>
			<?php endif; ?>

			<a href="<?php echo esc_url( $iphonebay_account_url ); ?>" class="nav-icon-btn" aria-label="<?php echo esc_attr( $iphonebay_account_label ); ?>">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>
			</a>

			<a href="<?php echo esc_url( $iphonebay_cart_url ); ?>" class="nav-icon-btn" aria-label="<?php esc_attr_e( 'View cart', 'iphonebay' ); ?>">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="23" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
				<span class="cart-badge" id="cart-count"><?php echo esc_html( $iphonebay_cart_count ); ?></span>
			</a>

			<button class="nav-hamburger" id="hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'iphonebay' ); ?>" aria-expanded="false" aria-controls="offcanvas">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<!-- OFF-CANVAS -->
<div class="offcanvas-backdrop" id="backdrop" aria-hidden="true"></div>
<div class="offcanvas" id="offcanvas" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Navigation', 'iphonebay' ); ?>">
	<div class="offcanvas-head">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="offcanvas-logo" data-close-menu>
			<?php
			if ( $iphonebay_logo_id ) {
				echo wp_get_attachment_image( $iphonebay_logo_id, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				echo '<img src="' . esc_url( IPHONEBAY_URI . '/assets/images/logo.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
			}
			?>
		</a>
		<button class="offcanvas-close" data-close-menu aria-label="<?php esc_attr_e( 'Close menu', 'iphonebay' ); ?>">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>
	</div>
	<nav class="offcanvas-body" aria-label="<?php esc_attr_e( 'Mobile', 'iphonebay' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 2,
			'walker'         => new IphoneBay_Mobile_Nav_Walker(),
			'fallback_cb'    => 'iphonebay_default_mobile_menu',
		) );
		?>
	</nav>
	<div class="offcanvas-foot">
		<span class="offcanvas-foot-label"><?php esc_html_e( 'Find us on', 'iphonebay' ); ?></span>
		<div class="offcanvas-socials">
			<?php
			$mobile_socials = array(
				'TikTok'    => iphonebay_opt( 'social_tiktok' ),
				'Instagram' => iphonebay_opt( 'social_instagram' ),
				'WhatsApp'  => iphonebay_opt( 'whatsapp' ) ? 'https://wa.me/' . preg_replace( '/\D/', '', iphonebay_opt( 'whatsapp' ) ) : '',
				'Facebook'  => iphonebay_opt( 'social_facebook' ),
			);
			$labels = array( 'TikTok' => 'TK', 'Instagram' => 'IG', 'WhatsApp' => 'WA', 'Facebook' => 'FB' );
			foreach ( $mobile_socials as $name => $url ) {
				echo '<a href="' . esc_url( $url ? $url : '#' ) . '" class="offcanvas-social" aria-label="' . esc_attr( $name ) . '"' . ( $url ? ' target="_blank" rel="noopener"' : '' ) . '>' . esc_html( $labels[ $name ] ) . '</a>';
			}
			?>
		</div>
	</div>
</div>

<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
	<div class="search-overlay" id="siteSearch" aria-hidden="true" hidden>
		<button type="button" class="search-overlay-backdrop" data-close-search aria-label="<?php esc_attr_e( 'Close search', 'iphonebay' ); ?>"></button>
		<div class="search-panel" role="dialog" aria-modal="true" aria-labelledby="siteSearchTitle">
			<div class="search-panel-head">
				<div>
					<p class="search-kicker"><?php esc_html_e( 'Search the catalog', 'iphonebay' ); ?></p>
					<h2 class="search-panel-title" id="siteSearchTitle"><?php esc_html_e( 'Find a device fast', 'iphonebay' ); ?></h2>
				</div>
				<button type="button" class="search-close" data-close-search aria-label="<?php esc_attr_e( 'Close search', 'iphonebay' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
				</button>
			</div>

			<form class="search-form" id="siteSearchForm" action="<?php echo esc_url( $iphonebay_search_url ); ?>" method="get">
				<input type="hidden" name="post_type" value="product">
				<label class="screen-reader-text" for="siteSearchInput"><?php esc_html_e( 'Search products', 'iphonebay' ); ?></label>
				<div class="search-input-wrap">
					<svg class="search-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
					<input
						type="search"
						id="siteSearchInput"
						class="search-input"
						name="s"
						placeholder="<?php esc_attr_e( 'Search iPhone 15, Samsung, 256GB, Ex-UK…', 'iphonebay' ); ?>"
						autocomplete="off"
					>
				</div>
			</form>

			<div class="search-panel-meta">
				<p class="search-panel-hint"><?php esc_html_e( 'Type a model, brand, storage, or condition to narrow down what is available.', 'iphonebay' ); ?></p>
				<a href="<?php echo esc_url( $iphonebay_search_url ); ?>" class="search-panel-link" id="siteSearchBrowse"><?php esc_html_e( 'Browse the full shop', 'iphonebay' ); ?> &rarr;</a>
			</div>

			<div class="search-status" id="siteSearchStatus"><?php esc_html_e( 'Popular right now', 'iphonebay' ); ?></div>
			<div class="search-results" id="siteSearchResults" aria-live="polite"></div>
		</div>
	</div>
<?php endif; ?>
