<?php
/**
 * Comments template.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area" style="margin-top:3rem;">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title" style="font-family:var(--font-display);color:var(--color-navy);">
			<?php
			$count = get_comments_number();
			printf( esc_html( _n( '%s Comment', '%s Comments', $count, 'iphonebay' ) ), esc_html( number_format_i18n( $count ) ) );
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 44,
			) );
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
