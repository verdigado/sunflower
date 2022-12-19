<?php
function sunflower_contact_form() {
	 $captcha = (int) sanitize_text_field( $_POST['captcha'] );

	if ( $captcha != 2 ) {
		echo json_encode(
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

	$response = __( 'Thank you. The form has been sent.', 'sunflower' );
	$to       = get_sunflower_setting( 'sunflower_contact_form_to' ) ?: get_option( 'admin_email' );

	$subject = __( 'New contact form', 'sunflower' );
	$message = sprintf( "Name: %s\nE-Mail:%s\n\n%s", $name, $mail, $message );
	wp_mail( $to, $subject, $message );

	echo json_encode(
		array(
			'code' => 200,
			'text' => $response,
		)
	);
	die();
}

add_action( 'wp_ajax_sunflower_contact_form', 'sunflower_contact_form' );
add_action( 'wp_ajax_nopriv_sunflower_contact_form', 'sunflower_contact_form' );

