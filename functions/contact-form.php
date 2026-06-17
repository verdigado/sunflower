<?php
/**
 * Methods for the Sunflower contact form.
 *
 * @package Sunflower 26
 */

/**
 * Render the Sunflower contact form.
 */
function sunflower_contact_form() {

	// Do not send, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_contact_form' ) ) {
		return;
	}

	$captcha_user_input = (int) sanitize_text_field( $_POST['captcha'] );
	$captcha_token      = sanitize_text_field( $_POST['captcha_token'] );
	$captcha_salt       = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'sunflower_default_fallback_salt';
	$expected_token     = '';

	// We need to find the sum that produces this token.
	// Since we only use numbers 1-9, there are very few possibilities (2 to 18).
	for ( $i = 2; $i <= 18; $i++ ) {
		if ( hash( 'sha256', $i . $captcha_salt ) === $captcha_token ) {
			$expected_sum = $i;
			break;
		}
	}

	if ( ! isset( $expected_sum ) || $captcha_user_input !== $expected_sum ) {
		echo wp_json_encode(
			array(
				'code' => 500,
				'text' => __(
					'Form not sent. Captcha wrong. Please try again.',
					'sunflower-contact-form'
				),
			)
		);
		die();
	}

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

	$mail_to = '';
	if ( ! empty( $_POST['postId'] ) ) {
		$post_id = (int) $_POST['postId'];
		$post    = get_post( $post_id );

		if ( $post ) {
			$blocks = parse_blocks( $post->post_content );
			$found  = false;
			// Look for the specific contact-form block instance by index.
			foreach ( $blocks as $block ) {
				if ( 'sunflower/contact-form' === $block['blockName'] ) {
					$mail_to = $block['attrs']['mailTo'] ?? '';
					if ( sanitize_email( $mail_to ) ) {
						$found = true;
						break;
					}
				}
			}
		}
	}

	if ( ! empty( $mail_to ) ) {
		$to = sanitize_email( $mail_to );
	}

	if ( empty( $to ) ) {
		$to = sunflower_get_setting( 'sunflower_contact_form_to' ) ? sunflower_get_setting( 'sunflower_contact_form_to' ) : get_option( 'admin_email' );
	}

	$subject     = __( 'New Message from', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
	$message_str = sprintf( '%s', implode( "\n", $message ) );

	if ( ! empty( $mail ) ) {
		$headers = 'Reply-To: ' . $mail;
	}

	if ( '' === $headers || '0' === $headers ) {
		wp_mail( $to, $subject, $message_str );
	} else {
		wp_mail( $to, $subject, $message_str, $headers );
	}

	// Send mail to sender if selected and email address is available.
	if ( ! empty( $mail ) && sanitize_text_field( $_POST['sendCopy'] ) ) {
		$headers = 'Reply-To: ' . $to;
		$subject = __( 'Your Message on', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
		wp_mail( $mail, $subject, $response, $headers );
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
