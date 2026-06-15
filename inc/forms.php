<?php
/**
 * Theme-native contact and newsletter forms.
 *
 * @package IphoneBayKE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the current form status from the URL.
 *
 * @param string $key Query arg key.
 * @return string
 */
function iphonebay_form_status( $key ) {
	return isset( $_GET[ $key ] ) ? sanitize_key( wp_unslash( $_GET[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Render a small inline status message.
 *
 * @param string $status Status slug.
 * @param array<string, string> $messages Message map.
 * @return string
 */
function iphonebay_inline_form_notice( $status, $messages ) {
	if ( ! $status || empty( $messages[ $status ] ) ) {
		return '';
	}

	$class = 'is-error';
	if ( 'success' === $status ) {
		$class = 'is-success';
	}

	return '<div class="inline-form-notice ' . esc_attr( $class ) . '">' . esc_html( $messages[ $status ] ) . '</div>';
}

/**
 * Render the contact form.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function iphonebay_contact_form_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'topic'  => isset( $_GET['topic'] ) ? sanitize_key( wp_unslash( $_GET['topic'] ) ) : 'general', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'title'  => __( 'Send us a message', 'iphonebay' ),
			'intro'  => __( 'Ask about a device, request a trade-in quote, or get help with an order.', 'iphonebay' ),
			'button' => __( 'Send message', 'iphonebay' ),
		),
		$atts,
		'iphonebay_contact_form'
	);

	$topic   = sanitize_key( $atts['topic'] );
	$status  = iphonebay_form_status( 'contact_status' );
	$notice  = iphonebay_inline_form_notice(
		$status,
		array(
			'success' => __( 'Your message is on its way. We will get back to you shortly.', 'iphonebay' ),
			'error'   => __( 'We could not send that message. Please try again.', 'iphonebay' ),
			'missing' => __( 'Please complete the required fields before sending.', 'iphonebay' ),
		)
	);
	ob_start();
	?>
	<div class="contact-form-card">
		<div class="contact-form-head">
			<h2 class="contact-form-title"><?php echo esc_html( $atts['title'] ); ?></h2>
			<p class="contact-form-intro"><?php echo esc_html( $atts['intro'] ); ?></p>
		</div>
		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="iphonebay_contact_form">
			<input type="hidden" name="topic" value="<?php echo esc_attr( $topic ); ?>">
			<?php wp_nonce_field( 'iphonebay_contact_form', 'iphonebay_contact_nonce' ); ?>
			<div class="contact-form-grid">
				<label class="form-field">
					<span><?php esc_html_e( 'Full name', 'iphonebay' ); ?></span>
					<input type="text" name="contact_name" required>
				</label>
				<label class="form-field">
					<span><?php esc_html_e( 'Email address', 'iphonebay' ); ?></span>
					<input type="email" name="contact_email" required>
				</label>
				<label class="form-field">
					<span><?php esc_html_e( 'Phone / WhatsApp', 'iphonebay' ); ?></span>
					<input type="text" name="contact_phone">
				</label>
				<label class="form-field">
					<span><?php esc_html_e( 'Topic', 'iphonebay' ); ?></span>
					<select name="contact_topic">
						<?php
						$topics = array(
							'general'  => __( 'General enquiry', 'iphonebay' ),
							'purchase' => __( 'Buying help', 'iphonebay' ),
							'trade-in' => __( 'Trade-in quote', 'iphonebay' ),
							'order'    => __( 'Track an order', 'iphonebay' ),
						);
						foreach ( $topics as $value => $label ) :
							?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $topic, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</div>
			<label class="form-field">
				<span><?php esc_html_e( 'Message', 'iphonebay' ); ?></span>
				<textarea name="contact_message" rows="6" required></textarea>
			</label>
			<button type="submit" class="btn-primary"><?php echo esc_html( $atts['button'] ); ?></button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'iphonebay_contact_form', 'iphonebay_contact_form_shortcode' );

/**
 * Handle contact form submissions.
 *
 * @return void
 */
function iphonebay_handle_contact_form() {
	if ( empty( $_POST['iphonebay_contact_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['iphonebay_contact_nonce'] ), 'iphonebay_contact_form' ) ) {
		wp_die( esc_html__( 'Invalid request.', 'iphonebay' ) );
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$phone   = isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '';
	$topic   = isset( $_POST['contact_topic'] ) ? sanitize_key( wp_unslash( $_POST['contact_topic'] ) ) : 'general';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';
	$target  = wp_get_referer() ? wp_get_referer() : iphonebay_contact_url( $topic );

	if ( ! $name || ! $email || ! $message ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'missing', $target ) );
		exit;
	}

	$recipient = apply_filters( 'iphonebay_contact_form_recipient', get_option( 'admin_email' ), $topic );
	$subject   = sprintf( '[%s] %s', get_bloginfo( 'name' ), ucfirst( str_replace( '-', ' ', $topic ) ) );
	$body      = implode(
		"\n\n",
		array(
			'Name: ' . $name,
			'Email: ' . $email,
			'Phone: ' . ( $phone ? $phone : '—' ),
			'Topic: ' . $topic,
			'Message:',
			$message,
		)
	);
	$headers   = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $recipient, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'contact_status', $sent ? 'success' : 'error', $target ) );
	exit;
}
add_action( 'admin_post_nopriv_iphonebay_contact_form', 'iphonebay_handle_contact_form' );
add_action( 'admin_post_iphonebay_contact_form', 'iphonebay_handle_contact_form' );

/**
 * Render the footer newsletter form.
 *
 * @return string
 */
function iphonebay_newsletter_form() {
	$status = iphonebay_form_status( 'newsletter_status' );
	$notice = iphonebay_inline_form_notice(
		$status,
		array(
			'success' => __( 'Thanks for subscribing. We will keep you posted.', 'iphonebay' ),
			'error'   => __( 'We could not save that subscription. Please try again.', 'iphonebay' ),
			'missing' => __( 'Enter a valid email address to subscribe.', 'iphonebay' ),
		)
	);
	ob_start();
	?>
	<div class="footer-newsletter">
		<div class="footer-col-head"><?php esc_html_e( 'Newsletter', 'iphonebay' ); ?></div>
		<p class="footer-newsletter-copy"><?php esc_html_e( 'Get fresh drops, weekly deals, and stock alerts in your inbox.', 'iphonebay' ); ?></p>
		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<form class="footer-newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="iphonebay_newsletter_form">
			<?php wp_nonce_field( 'iphonebay_newsletter_form', 'iphonebay_newsletter_nonce' ); ?>
			<label class="screen-reader-text" for="footerNewsletterEmail"><?php esc_html_e( 'Email address', 'iphonebay' ); ?></label>
			<input id="footerNewsletterEmail" type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Email address', 'iphonebay' ); ?>" required>
			<button type="submit" class="btn-primary"><?php esc_html_e( 'Subscribe', 'iphonebay' ); ?></button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Handle footer newsletter submissions.
 *
 * @return void
 */
function iphonebay_handle_newsletter_form() {
	if ( empty( $_POST['iphonebay_newsletter_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['iphonebay_newsletter_nonce'] ), 'iphonebay_newsletter_form' ) ) {
		wp_die( esc_html__( 'Invalid request.', 'iphonebay' ) );
	}

	$email  = isset( $_POST['newsletter_email'] ) ? sanitize_email( wp_unslash( $_POST['newsletter_email'] ) ) : '';
	$target = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! $email || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter_status', 'missing', $target ) );
		exit;
	}

	$recipient = apply_filters( 'iphonebay_newsletter_recipient', get_option( 'admin_email' ) );
	$subject   = sprintf( '[%s] %s', get_bloginfo( 'name' ), __( 'New newsletter signup', 'iphonebay' ) );
	$body      = sprintf(
		"Email: %s\nSource: Footer newsletter form\nSite: %s",
		$email,
		home_url( '/' )
	);
	$sent = wp_mail( $recipient, $subject, $body );

	wp_safe_redirect( add_query_arg( 'newsletter_status', $sent ? 'success' : 'error', $target ) );
	exit;
}
add_action( 'admin_post_nopriv_iphonebay_newsletter_form', 'iphonebay_handle_newsletter_form' );
add_action( 'admin_post_iphonebay_newsletter_form', 'iphonebay_handle_newsletter_form' );
