<?php
/**
 * Methods for the Sunflower contact form.
 *
 * @package Sunflower 26
 */

/**
 * Best-effort client IP for rate limiting.
 *
 * Behind a reverse proxy / DDEV router, REMOTE_ADDR is the proxy address and is
 * therefore shared by all visitors. A trusted proxy sets X-Forwarded-For with the
 * real client IP as the left-most entry, so we prefer that. IMPORTANT:
 * X-Forwarded-For is client-supplied and spoofable UNLESS a trusted proxy
 * overwrites/appends it - only rely on it when such a proxy is actually in front
 * (otherwise keep REMOTE_ADDR). Behind Cloudflare prefer CF-Connecting-IP.
 *
 * @return string Client IP, or '' if none could be determined.
 */
function sunflower_contact_form_client_ip() {
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$sunflower_xff = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		$sunflower_ip  = trim( $sunflower_xff[0] );
		if ( filter_var( $sunflower_ip, FILTER_VALIDATE_IP ) ) {
			return $sunflower_ip;
		}
	}
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

/**
 * Render the Sunflower contact form.
 */
function sunflower_contact_form() {

	// Do not send, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_contact_form' ) ) {
		return;
	}

	$sunflower_spam_salt = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'sunflower_default_fallback_salt';

	// Honeypot: a hidden field humans never see. If filled, silently accept and drop
	// (fake success so bots get no signal to adapt).
	if ( ! empty( $_POST['website'] ) ) {
		echo wp_json_encode(
			array(
				'code' => 200,
				'text' => __( 'Thank you. The form has been sent.', 'sunflower-contact-form' ),
			)
		);
		die();
	}

	// Time-trap: reject forms submitted implausibly fast or long stale. The render
	// timestamp is HMAC-signed, so it cannot be forged or replayed with a new value.
	$sunflower_form_ts     = isset( $_POST['form_ts'] ) ? (int) $_POST['form_ts'] : 0;
	$sunflower_form_ts_sig = isset( $_POST['form_ts_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['form_ts_sig'] ) ) : '';
	$sunflower_captcha_on  = ( isset( $_POST['captcha_on'] ) && '0' === (string) $_POST['captcha_on'] ) ? '0' : '1';
	$sunflower_expected_ts = hash_hmac( 'sha256', $sunflower_form_ts . '|' . $sunflower_captcha_on, $sunflower_spam_salt );

	if ( ! $sunflower_form_ts || ! hash_equals( $sunflower_expected_ts, $sunflower_form_ts_sig ) ) {
		echo wp_json_encode(
			array(
				'code' => 500,
				'text' => __( 'Form not sent. Please reload the page and try again.', 'sunflower-contact-form' ),
			)
		);
		die();
	}

	$sunflower_elapsed = time() - $sunflower_form_ts;
	if ( $sunflower_elapsed < 3 || $sunflower_elapsed > HOUR_IN_SECONDS ) {
		echo wp_json_encode(
			array(
				'code' => 500,
				'text' => __( 'Form not sent. Please take a moment and try again.', 'sunflower-contact-form' ),
			)
		);
		die();
	}

	// Rate limiting: cap submissions per client per short, fixed window to throttle
	// scripted floods. The window is anchored on the first hit so it truly resets
	// (the old code re-extended a 1h TTL on every hit and effectively never expired).
	$sunflower_ip = sunflower_contact_form_client_ip();
	if ( $sunflower_ip ) {
		$sunflower_rl_window = MINUTE_IN_SECONDS;
		$sunflower_rl_max    = 5;
		$sunflower_rl_key    = 'sunflower_cf_rl_' . md5( $sunflower_ip );
		$sunflower_rl_bucket = get_transient( $sunflower_rl_key );

		// Old int format or missing -> start a fresh bucket (also self-heals the
		// previous hour-long integer transient).
		if ( ! is_array( $sunflower_rl_bucket ) || ! isset( $sunflower_rl_bucket['count'], $sunflower_rl_bucket['start'] ) ) {
			$sunflower_rl_bucket = array(
				'count' => 0,
				'start' => time(),
			);
		}

		// Window elapsed -> reset counter.
		if ( ( time() - (int) $sunflower_rl_bucket['start'] ) >= $sunflower_rl_window ) {
			$sunflower_rl_bucket = array(
				'count' => 0,
				'start' => time(),
			);
		}

		if ( $sunflower_rl_bucket['count'] >= $sunflower_rl_max ) {
			echo wp_json_encode(
				array(
					'code' => 500,
					'text' => __( 'Too many messages from your address. Please try again later.', 'sunflower-contact-form' ),
				)
			);
			die();
		}

		++$sunflower_rl_bucket['count'];
		// TTL = remaining window, so the transient self-expires with the window and
		// blocked requests (which return above) never extend it.
		$sunflower_rl_ttl = $sunflower_rl_window - ( time() - (int) $sunflower_rl_bucket['start'] );
		set_transient( $sunflower_rl_key, $sunflower_rl_bucket, max( 1, $sunflower_rl_ttl ) );
	}

	// Captcha only when this form was rendered with it enabled (flag is signed).
	if ( '1' === $sunflower_captcha_on ) {
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
