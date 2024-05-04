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

	$message = sanitize_textarea_field( $_POST['message'] );
	$name    = sanitize_text_field( $_POST['name'] );
	$mail    = sanitize_email( $_POST['mail'] );
	$title   = sanitize_text_field( $_POST['title'] );

	$response = __( 'Thank you. The form has been sent.', 'sunflower-contact-form' );
	$to       = sunflower_get_setting( 'sunflower_contact_form_to' ) ? sunflower_get_setting( 'sunflower_contact_form_to' ) : get_option( 'admin_email' );

	$subject = __( 'New Message from', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
	$message = sprintf( "Name: %s\nE-Mail: %s\n\n%s", $name, $mail, $message );

	if ( ! empty( $mail ) ) {
		$headers = 'Reply-To: ' . $mail;
	}

	if ( '' === $headers || '0' === $headers ) {
		wp_mail( $to, $subject, $message );
	} else {
		wp_mail( $to, $subject, $message, $headers );
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
