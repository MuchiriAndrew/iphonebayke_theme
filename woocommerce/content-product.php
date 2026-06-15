<?php
/**
 * The template for displaying product content within loops (uses our card).
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'product-grid-item', $product ); ?>>
	<?php iphonebay_product_card( $product ); ?>
</li>
