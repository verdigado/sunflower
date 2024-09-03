<?php
/**
 * Methods for the Sunflower contact form.
 *
 * @package sunflower
 */

/**
 * Render the Sunflower contact form.
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

	$mail_to = sanitize_text_field( $_POST['mailTo'] );
	if ( $mail_to ) {
		$to = sanitize_email( base64_decode( strrev( (string) $mail_to ) ) ); // phpcs:ignore
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
