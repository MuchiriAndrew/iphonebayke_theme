<?php
/**
 * Custom post types + native meta boxes (no ACF needed).
 *
 * - hero_slide : homepage hero slides (repeatable via "add new").
 * - testimonial: customer reviews.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post types.
 */
function iphonebay_register_post_types() {

	register_post_type( 'hero_slide', array(
		'labels' => array(
			'name'               => __( 'Hero Slides', 'iphonebay' ),
			'singular_name'      => __( 'Hero Slide', 'iphonebay' ),
			'add_new'            => __( 'Add Slide', 'iphonebay' ),
			'add_new_item'       => __( 'Add New Slide', 'iphonebay' ),
			'edit_item'          => __( 'Edit Slide', 'iphonebay' ),
			'new_item'           => __( 'New Slide', 'iphonebay' ),
			'view_item'          => __( 'View Slide', 'iphonebay' ),
			'all_items'          => __( 'All Slides', 'iphonebay' ),
			'menu_name'          => __( 'Hero Slides', 'iphonebay' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-images-alt2',
		'menu_position' => 26,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'   => false,
		'rewrite'       => false,
	) );

	register_post_type( 'testimonial', array(
		'labels' => array(
			'name'          => __( 'Testimonials', 'iphonebay' ),
			'singular_name' => __( 'Testimonial', 'iphonebay' ),
			'add_new_item'  => __( 'Add New Testimonial', 'iphonebay' ),
			'edit_item'     => __( 'Edit Testimonial', 'iphonebay' ),
			'all_items'     => __( 'All Testimonials', 'iphonebay' ),
			'menu_name'     => __( 'Testimonials', 'iphonebay' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-format-status',
		'menu_position' => 27,
		'supports'      => array( 'title', 'editor', 'page-attributes' ),
		'has_archive'   => false,
		'rewrite'       => false,
	) );
}
add_action( 'init', 'iphonebay_register_post_types' );

/**
 * Meta boxes.
 */
function iphonebay_add_meta_boxes() {
	add_meta_box( 'iphonebay_slide_fields', __( 'Slide Content', 'iphonebay' ), 'iphonebay_slide_fields_cb', 'hero_slide', 'normal', 'high' );
	add_meta_box( 'iphonebay_testimonial_fields', __( 'Reviewer Details', 'iphonebay' ), 'iphonebay_testimonial_fields_cb', 'testimonial', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'iphonebay_add_meta_boxes' );

/**
 * Helper: render a labelled text field.
 */
function iphonebay_meta_text( $post_id, $key, $label, $type = 'text', $placeholder = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	echo '<p style="margin:0 0 14px;">';
	echo '<label for="' . esc_attr( $key ) . '" style="display:block;font-weight:600;margin-bottom:4px;">' . esc_html( $label ) . '</label>';
	if ( 'textarea' === $type ) {
		echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" class="widefat" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="widefat" placeholder="' . esc_attr( $placeholder ) . '">';
	}
	echo '</p>';
}

/**
 * Hero slide fields.
 */
function iphonebay_slide_fields_cb( $post ) {
	wp_nonce_field( 'iphonebay_slide_save', 'iphonebay_slide_nonce' );
	echo '<p style="color:#666;margin-top:0;">' . esc_html__( 'The slide background uses the Featured Image (set it on the right). The "Title" is shown as the large heading — wrap a word in *asterisks* to highlight it gold. Use Order (Page Attributes) to control slide sequence.', 'iphonebay' ) . '</p>';
	iphonebay_meta_text( $post->ID, 'iphonebay_eyebrow', __( 'Eyebrow label (small text above heading)', 'iphonebay' ), 'text', 'New Season Drop' );
	iphonebay_meta_text( $post->ID, 'iphonebay_subtitle', __( 'Subtitle / description', 'iphonebay' ), 'textarea', 'Ex-UK & Brand New iPhones. Every model. Every colour.' );
	echo '<div style="display:flex;gap:16px;flex-wrap:wrap;">';
	echo '<div style="flex:1;min-width:200px;">';
	iphonebay_meta_text( $post->ID, 'iphonebay_btn1_text', __( 'Primary button text', 'iphonebay' ), 'text', 'Shop iPhones' );
	iphonebay_meta_text( $post->ID, 'iphonebay_btn1_url', __( 'Primary button URL', 'iphonebay' ), 'url', '/shop/' );
	echo '</div>';
	echo '<div style="flex:1;min-width:200px;">';
	iphonebay_meta_text( $post->ID, 'iphonebay_btn2_text', __( 'Secondary button text', 'iphonebay' ), 'text', 'View All Deals' );
	iphonebay_meta_text( $post->ID, 'iphonebay_btn2_url', __( 'Secondary button URL', 'iphonebay' ), 'url', '/shop/' );
	echo '</div>';
	echo '</div>';
}

/**
 * Testimonial fields.
 */
function iphonebay_testimonial_fields_cb( $post ) {
	wp_nonce_field( 'iphonebay_testimonial_save', 'iphonebay_testimonial_nonce' );
	echo '<p style="color:#666;margin-top:0;">' . esc_html__( 'The review text is the main editor content. Title can be the reviewer name.', 'iphonebay' ) . '</p>';
	iphonebay_meta_text( $post->ID, 'iphonebay_author', __( 'Reviewer name', 'iphonebay' ), 'text', 'Wanjiku K.' );
	iphonebay_meta_text( $post->ID, 'iphonebay_location', __( 'Location', 'iphonebay' ), 'text', 'Westlands, Nairobi' );
	iphonebay_meta_text( $post->ID, 'iphonebay_rating', __( 'Rating (1–5)', 'iphonebay' ), 'number', '5' );
}

/**
 * Save meta.
 */
function iphonebay_save_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$slide_keys = array(
		'iphonebay_eyebrow'   => 'sanitize_text_field',
		'iphonebay_subtitle'  => 'sanitize_textarea_field',
		'iphonebay_btn1_text' => 'sanitize_text_field',
		'iphonebay_btn1_url'  => 'esc_url_raw',
		'iphonebay_btn2_text' => 'sanitize_text_field',
		'iphonebay_btn2_url'  => 'esc_url_raw',
	);
	$testimonial_keys = array(
		'iphonebay_author'   => 'sanitize_text_field',
		'iphonebay_location' => 'sanitize_text_field',
		'iphonebay_rating'   => 'absint',
	);

	if ( isset( $_POST['iphonebay_slide_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['iphonebay_slide_nonce'] ), 'iphonebay_slide_save' ) ) {
		foreach ( $slide_keys as $key => $sanitize ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}

	if ( isset( $_POST['iphonebay_testimonial_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['iphonebay_testimonial_nonce'] ), 'iphonebay_testimonial_save' ) ) {
		foreach ( $testimonial_keys as $key => $sanitize ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}
}
add_action( 'save_post', 'iphonebay_save_meta' );
