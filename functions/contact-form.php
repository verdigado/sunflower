<?php
/**
 * Methods for the Sunflower contact form.
 *
 * @package Sunflower 26
 */

/**
 * Client IP for rate limiting.
 *
 * By default this is REMOTE_ADDR, the only address a client cannot make up.
 * Behind a reverse proxy REMOTE_ADDR is the proxy, so all visitors share one
 * rate limit. Sites in that situation can opt in to X-Forwarded-For:
 *
 *     define( 'SUNFLOWER_CONTACT_FORM_TRUST_PROXY', true );
 *
 * or with the filter 'sunflower_contact_form_trust_proxy'. Only do this when a
 * proxy you control really is in front - otherwise the header is whatever the
 * client sends. The right-most entry is used: it is the one appended by the
 * nearest proxy, while everything left of it comes from the request itself.
 * Setups with several proxies, or with a header such as CF-Connecting-IP, can
 * supply the address through the filter 'sunflower_contact_form_client_ip'.
 *
 * @return string Client IP, or '' if none could be determined.
 */
function sunflower_contact_form_client_ip() {
	$sunflower_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	/**
	 * Whether to take the client IP from X-Forwarded-For.
	 *
	 * @param bool $trust_proxy Default: the constant SUNFLOWER_CONTACT_FORM_TRUST_PROXY, else false.
	 */
	$sunflower_trust_proxy = (bool) apply_filters( 'sunflower_contact_form_trust_proxy', (bool) sunflower_get_constant( 'SUNFLOWER_CONTACT_FORM_TRUST_PROXY' ) );

	if ( $sunflower_trust_proxy && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$sunflower_xff       = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		$sunflower_forwarded = trim( (string) end( $sunflower_xff ) );
		if ( filter_var( $sunflower_forwarded, FILTER_VALIDATE_IP ) ) {
			$sunflower_ip = $sunflower_forwarded;
		}
	}

	/**
	 * The client IP used for the contact form rate limit.
	 *
	 * @param string $sunflower_ip The detected address.
	 */
	$sunflower_ip = (string) apply_filters( 'sunflower_contact_form_client_ip', $sunflower_ip );

	return filter_var( $sunflower_ip, FILTER_VALIDATE_IP ) ? $sunflower_ip : '';
}

/**
 * Secret for signing the form timestamp and the captcha token.
 *
 * @return string The site's nonce salt.
 */
function sunflower_contact_form_secret() {
	return wp_salt( 'nonce' );
}

/**
 * Signature of the render timestamp (time-trap).
 *
 * @param int $form_ts Unix time at which the form was rendered.
 * @return string The signature.
 */
function sunflower_contact_form_ts_signature( $form_ts ) {
	return hash_hmac( 'sha256', 'ts|' . (int) $form_ts, sunflower_contact_form_secret() );
}

/**
 * Captcha token for a given result.
 *
 * The token is bound to the render timestamp, so a solved captcha is only
 * worth something together with the form it was rendered in - and that form
 * expires after an hour.
 *
 * @param int $sum     The expected result of the arithmetic question.
 * @param int $form_ts Unix time at which the form was rendered.
 * @return string The token.
 */
function sunflower_contact_form_captcha_token( $sum, $form_ts ) {
	return hash_hmac( 'sha256', 'captcha|' . (int) $sum . '|' . (int) $form_ts, sunflower_contact_form_secret() );
}

/**
 * Helper to decide whether the send‑copy feature is enabled.
 *
 * The flag is stored in the block attributes (sendCopy).
 *
 * @param array $block The parsed block array.
 * @return bool Whether the send-copy feature is enabled.
 */
function sunflower_contact_form_send_copy_enabled( $block ) {
	return ! empty( $block['attrs']['sendCopy'] );
}

/**
 * Recursively find the first sunflower/contact-form block.
 *
 * Blocks may be nested inside layout blocks (columns, group, cover, …), so a
 * flat scan of the top-level blocks is not enough.
 *
 * @param array $blocks Parsed blocks (from parse_blocks()).
 * @return array|null The matching block array, or null if none was found.
 */
function sunflower_contact_form_find_block( $blocks ) {
	foreach ( $blocks as $block ) {
		if ( isset( $block['blockName'] ) && 'sunflower/contact-form' === $block['blockName'] ) {
			return $block;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$nested = sunflower_contact_form_find_block( $block['innerBlocks'] );
			if ( $nested ) {
				return $nested;
			}
		}
	}

	return null;
}

/**
 * Render the Sunflower contact form.
 */
function sunflower_contact_form() {

	// Do not send, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_contact_form' ) ) {
		return;
	}

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

	// Resolve the hosting contact-form block from its trusted server-side
	// attributes. All feature flags (captcha, send-copy), the title and the
	// recipient are read from here - never from the request - so the client
	// cannot forge them.
	$found_block = null;
	if ( ! empty( $_POST['postId'] ) ) {
		$sunflower_post = get_post( (int) $_POST['postId'] );

		// Only a post the sender can actually see may supply the form settings.
		// Otherwise any post ID could be named to borrow, say, a draft's
		// recipient or a form with the captcha switched off.
		if ( $sunflower_post && ( 'publish' === $sunflower_post->post_status || current_user_can( 'read_post', $sunflower_post->ID ) ) ) {
			$found_block = sunflower_contact_form_find_block( parse_blocks( $sunflower_post->post_content ) );
		}
	}

	// Captcha is on unless the block explicitly disables it (showCaptcha=false).
	$sunflower_show_captcha = (bool) ( $found_block['attrs']['showCaptcha'] ?? true );

	// Time-trap: reject forms submitted implausibly fast or long stale. The render
	// timestamp is HMAC-signed, so it cannot be forged or replayed with a new value.
	$sunflower_form_ts     = isset( $_POST['form_ts'] ) ? (int) $_POST['form_ts'] : 0;
	$sunflower_form_ts_sig = isset( $_POST['form_ts_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['form_ts_sig'] ) ) : '';
	$sunflower_expected_ts = sunflower_contact_form_ts_signature( $sunflower_form_ts );

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

	// Captcha only when the block enables it (read from trusted block attributes).
	if ( $sunflower_show_captcha ) {
		$captcha_user_input = isset( $_POST['captcha'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['captcha'] ) ) : 0;
		$captcha_token      = isset( $_POST['captcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['captcha_token'] ) ) : '';

		// The token must be the one issued for this answer and this form's
		// (already verified) timestamp. Answers range from 2 to 18.
		$captcha_valid = $captcha_user_input >= 2
			&& $captcha_user_input <= 18
			&& hash_equals( sunflower_contact_form_captcha_token( $captcha_user_input, $sunflower_form_ts ), $captcha_token );

		if ( ! $captcha_valid ) {
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

	// $found_block was already resolved above (before the time-trap) from the
	// trusted block attributes. Derive the title and recipient from it.
	$title   = sanitize_text_field( $found_block['attrs']['title'] ?? '' );
	$mail_to = $found_block['attrs']['mailTo'] ?? '';

	$response = __( 'Thank you. The form has been sent.', 'sunflower-contact-form' );

	if ( ! empty( $mail_to ) ) {
		$to = sanitize_email( $mail_to );
	}

	if ( empty( $to ) ) {
		$to = sunflower_get_setting( 'sunflower_contact_form_to' ) ? sunflower_get_setting( 'sunflower_contact_form_to' ) : get_option( 'admin_email' );
	}

	$subject     = __( 'New Message from', 'sunflower-contact-form' ) . ' ' . ( $title ? $title : __( 'Contact Form', 'sunflower-contact-form' ) );
	$message_str = implode( "\n", $message );

	if ( ! empty( $mail ) ) {
		$headers = 'Reply-To: ' . $mail;
	}

	if ( empty( $headers ) ) {
		wp_mail( $to, $subject, $message_str );
	} else {
		wp_mail( $to, $subject, $message_str, $headers );
	}

	// Send mail to sender if selected and email address is available.
	if ( ! empty( $mail ) && sunflower_contact_form_send_copy_enabled( $found_block ?? array() ) ) {
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
