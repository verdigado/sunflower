<?php
/**
 * Methods for the Sunflower contact form.
 *
 * @package Sunflower 26
 */

/**
 * Whether the optional "send copy to sender" feature is enabled site-wide.
 *
 * The feature lets the contact form email a copy of the submitted message
 * back to the address typed into the form. Because both the recipient
 * (the sender's own address) and the body are attacker-controlled, this turns
 * the form into an open relay. It is therefore disabled by default and must
 * be opted in to via a constant in wp-config.php:
 *
 *     define( 'SUNFLOWER_ALLOW_CONTACT_FORM_SEND_COPY', true );
 */
function sunflower_contact_form_send_copy_enabled(): bool {
	return defined( 'SUNFLOWER_ALLOW_CONTACT_FORM_SEND_COPY' ) && SUNFLOWER_ALLOW_CONTACT_FORM_SEND_COPY;
}

/**
 * Look up the recipient address configured on a contact form block in a post.
 *
 * The recipient is read from the post content server-side so a submitter
 * cannot redirect mail by tampering with form fields.
 *
 * @param int $post_id Post that hosts the contact form block.
 */
function sunflower_contact_form_lookup_recipient( int $post_id ): string {
	if ( ! $post_id ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return '';
	}

	$mail = '';
	$find = static function ( array $blocks ) use ( &$find, &$mail ): void {
		foreach ( $blocks as $block ) {
			if ( 'sunflower/contact-form' === ( $block['blockName'] ?? '' ) && ! empty( $block['attrs']['mailTo'] ) ) {
				$mail = (string) $block['attrs']['mailTo'];
				return;
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$find( $block['innerBlocks'] );
				if ( '' !== $mail ) {
					return;
				}
			}
		}
	};
	$find( parse_blocks( $post->post_content ) );

	return sanitize_email( $mail );
}

/**
 * Handle a Sunflower contact form submission.
 */
function sunflower_contact_form() {

	// Do not send, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_contact_form' ) ) {
		return;
	}

	$captcha = (int) sanitize_text_field( $_POST['captcha'] );

	if ( 2 !== $captcha ) {
		echo wp_json_encode(
			array(
				'code' => 500,
				'text' => __(
					'Form not sent. Captcha wrong. Please try again.',
					'sunflower'
				),
			)
		);
		die();
	}

	$message = array();

	$name = sanitize_text_field( $_POST['name'] );
	if ( $name ) {
		$message[] = sprintf( __( 'Name', 'sunflower-contact-form' ) . ': %s', $name );
	}

	$mail = sanitize_email( $_POST['mail'] );
	if ( $mail ) {
		$message[] = sprintf( __( 'E-Mail', 'sunflower-contact-form' ) . ': %s', $mail );
	}

	$phone = sanitize_text_field( $_POST['phone'] );
	if ( $phone ) {
		$message[] = sprintf( __( 'Phone', 'sunflower-contact-form' ) . ': %s', $phone );
	}

	$message[] = "\n" . __( 'Message', 'sunflower-contact-form' ) . ': ' . sanitize_textarea_field( $_POST['message'] );

	$title = sanitize_text_field( $_POST['title'] );

	$response = __( 'Thank you. The form has been sent.', 'sunflower-contact-form' );

	$post_id = absint( $_POST['post_id'] ?? 0 );
	$to      = sunflower_contact_form_lookup_recipient( $post_id );

	if ( empty( $to ) ) {
		$to = sunflower_get_setting( 'sunflower_contact_form_to' ) ? sunflower_get_setting( 'sunflower_contact_form_to' ) : get_option( 'admin_email' );
	}

	$subject     = __( 'New Message from', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
	$message_str = sprintf( '%s', implode( "\n", $message ) );
	$headers     = '';

	if ( ! empty( $mail ) ) {
		$headers = 'Reply-To: ' . $mail;
	}

	if ( '' === $headers ) {
		wp_mail( $to, $subject, $message_str );
	} else {
		wp_mail( $to, $subject, $message_str, $headers );
	}

	// Send copy to sender only when the admin has globally opted in.
	if (
		sunflower_contact_form_send_copy_enabled()
		&& ! empty( $mail )
		&& sanitize_text_field( $_POST['sendCopy'] ?? '' )
	) {
		$headers = 'Reply-To: ' . $to;
		$subject = __( 'Your Message on', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
		wp_mail( $mail, $subject, $message_str, $headers );
	}

	echo wp_json_encode(
		array(
			'code' => 200,
			'text' => $response,
		)
	);
	die();
}

add_action( 'wp_ajax_sunflower_contact_form', 'sunflower_contact_form' );
add_action( 'wp_ajax_nopriv_sunflower_contact_form', 'sunflower_contact_form' );
