<?php
/**
 * Render the Sunflower contact form.
 *
 * @package Sunflower 26
 */

$sunflower_title         = $attributes['title'] ?? __( 'Contact Form', 'sunflower-contact-form' );
$sunflower_mailto        = $attributes['mailTo'] ?? '';
$sunflower_sendcopy      = $attributes['sendCopy'] ?? 0;
$sunflower_display_phone = $attributes['displayPhone'] ?? false;
$sunflower_require_phone = $attributes['requirePhone'] ?? false;
$sunflower_require_mail  = $attributes['requireMail'] ?? false;

// Placeholder with '*' appended if required.
$sunflower_placeholder_message = __( 'Your Message', 'sunflower-contact-form' ) . '*';
$sunflower_placeholder_name    = __( 'Name', 'sunflower-contact-form' ) . '*';
$sunflower_placeholder_email   = __( 'E-Mail', 'sunflower-contact-form' ) . ( $sunflower_require_mail ? '*' : '' );
$sunflower_placeholder_phone   = __( 'Phone', 'sunflower-contact-form' ) . ( $sunflower_require_phone ? '*' : '' );
// Dynamic Captcha generation.
$sunflower_captcha_num1  = wp_rand( 1, 9 );
$sunflower_captcha_num2  = wp_rand( 1, 9 );
$sunflower_captcha_sum   = $sunflower_captcha_num1 + $sunflower_captcha_num2;
$sunflower_captcha_salt  = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'sunflower_default_fallback_salt';
$sunflower_captcha_token = hash( 'sha256', $sunflower_captcha_sum . $sunflower_captcha_salt );

// translators: %1$d and %2$d are random numbers for a math captcha.
$sunflower_captcha_expr = sprintf( __( '%1$d + %2$d', 'sunflower-contact-form' ), $sunflower_captcha_num1, $sunflower_captcha_num2 );
// translators: %s is the arithmetic expression (e.g., "3 + 5").
$sunflower_placeholder_captcha = sprintf( __( 'How much is %s?', 'sunflower-contact-form' ), $sunflower_captcha_expr ) . '*';
?>

<div class="wp-block-sunflower-contact-form">
	<?php if ( ! empty( $sunflower_title ) ) : ?>
		<h2 id="contact-form-title" class="text-center h1">
			<?php echo esc_html( $sunflower_title ); ?>
		</h2>
	<?php endif; ?>

	<form id="sunflower-contact-form" method="post" class="row">
		<?php wp_nonce_field( 'sunflower_contact_form' ); ?>

		<div class="col-12 col-md-6">
			<div class="comment-form-comment">
				<label for="message">
					<?php esc_html_e( 'Message', 'sunflower-contact-form' ); ?> <span class="required">*</span></label>
				<textarea
					id="message"
					name="message"
					cols="45"
					rows="8"
					maxlength="2000"
					required
					placeholder="<?php echo esc_attr( $sunflower_placeholder_message ); ?>"
				></textarea>
			</div>
		</div>

		<div class="col-12 col-md-6">
			<div class="comment-form-author">
				<label for="name">
					<?php esc_html_e( 'Name', 'sunflower-contact-form' ); ?></label>

				<div class="input-with-icon">
					<i class="fa-solid fa-user"></i>
					<input
						id="name"
						name="name"
						type="text"
						value=""
						size="30"
						maxlength="245"
						required
						placeholder="<?php echo esc_attr( $sunflower_placeholder_name ); ?>"
					/>
				</div>
			</div>

			<div class="comment-form-email">
				<label for="mail"><?php esc_html_e( 'E-Mail', 'sunflower-contact-form' ); ?></label>
				<div class="input-with-icon">
					<i class="fa-solid fa-envelope"></i>
					<input
						id="mail"
						name="mail"
						type="email"
						value=""
						size="30"
						minlength="5"
						maxlength="100"
						<?php echo $sunflower_require_mail ? 'required' : ''; ?>
						placeholder="<?php echo esc_attr( $sunflower_placeholder_email ); ?>"
					/>
				</div>
			</div>

			<?php if ( $sunflower_display_phone ) : ?>
			<div class="comment-form-email">
				<label for="phone"><?php esc_html_e( 'Phone', 'sunflower-contact-form' ); ?></label>

				<div class="input-with-icon">
					<i class="fa-solid fa-phone"></i>
						<input
							id="phone"
							name="phone"
							type="tel"
							value=""
							size="30"
							pattern="[0-9\-\+\s]*"
							<?php echo $sunflower_require_phone ? 'required' : ''; ?>
							placeholder="<?php echo esc_attr( $sunflower_placeholder_phone ); ?>"
						/>
				</div>
			</div>
			<?php endif; ?>

			<div class="comment-form-email">
				<label for="captcha">
				<?php
					/* translators: %s is the arithmetic expression (e.g., "3 + 5") */
					echo esc_html( sprintf( __( 'How much is %s?', 'sunflower-contact-form' ), $sunflower_captcha_expr ) );
				?>
				<span class="required">*</span></label>
				<div class="input-with-icon">
					<i class="fa-solid fa-calculator"></i>

					<input
						id="captcha"
						name="captcha"
						type="text"
						value=""
						size="30"
						maxlength="100"
						required
						placeholder="<?php echo esc_attr( $sunflower_placeholder_captcha ); ?>"
					/>
					<input type="hidden" name="captcha_token" value="<?php echo esc_attr( $sunflower_captcha_token ); ?>" />
				</div>

			</div>

			<div>
				<p class="small">
					<?php echo wp_kses_post( __( 'Please fill in all required (<span class="required">*</span>) fields.', 'sunflower-contact-form' ) ); ?>
				</p>

				<p class="small">
					<?php
					echo wp_kses_post( __( 'By using this form, you consent to the storage and processing of your data through our website. Additional information can be found in our privacy policy on <a href="#" id="privacy_policy_url">Datenschutzerklärung</a>.', 'sunflower-contact-form' ) );
					?>
				</p>
			</div>

	<?php
	if ( $sunflower_mailto ) {
		echo '<input id="post-id" name="post_id" type="hidden" value="' . esc_attr( get_the_ID() ) . '" />';
	}

	if ( $sunflower_sendcopy ) {
		echo '<input id="send-copy" name="send-copy" type="hidden" value="1" />';
	}

	?>

		</div>
		<p class="form-submit">
			<input
				name="submit"
				type="submit"
				id="submit"
				class="submit"
				value="<?php echo esc_attr__( 'submit', 'sunflower-contact-form' ); ?>"
			/>
		</p>
		<div id="form-error" class="bg-danger p-4 text-white" style="display:none;"></div>
	</form>
</div>
