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
$sunflower_placeholder_captcha = __( 'How much is 1 + 1?', 'sunflower-contact-form' ) . '*';
?>

<div class="comment-respond mb-5">
	<?php if ( ! empty( $sunflower_title ) ) : ?>
		<h2 id="contact-form-title" class="text-center h1">
			<?php echo esc_html( $sunflower_title ); ?>
		</h2>
	<?php endif; ?>

	<form id="sunflower-contact-form" method="post" class="row">
		<?php wp_nonce_field( 'sunflower_contact_form' ); ?>

		<div class="col-12 col-md-6">
			<p class="comment-form-comment">
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
			</p>

		</div>

		<div class="col-12 col-md-6">
			<p class="comment-form-author">
				<label for="name">
					<?php esc_html_e( 'Name', 'sunflower-contact-form' ); ?></label>

				<div class="input-with-icon">
				<i class="forkawesome fa-user"></i>
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
			</p>

			<p class="comment-form-email">
				<label for="mail"><?php esc_html_e( 'E-Mail', 'sunflower-contact-form' ); ?></label>
				<input
					id="mail"
					name="mail"
					type="email"
					value=""
					size="30"
					maxlength="100"
					<?php echo $sunflower_require_mail ? 'required' : ''; ?>
					placeholder="<?php echo esc_attr( $sunflower_placeholder_email ); ?>"
				/>
			</p>

			<?php if ( $sunflower_display_phone ) : ?>
				<p class="comment-form-email">
					<label for="phone"><?php esc_html_e( 'Phone', 'sunflower-contact-form' ); ?></label>

			<div class="input-with-icon">
				<i class="forkawesome fa-phone"></i>
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
				</p>
			<?php endif; ?>

			<p class="comment-form-email">
				<label for="captcha"><?php esc_html_e( 'How much is 1 + 1?', 'sunflower-contact-form' ); ?> <span class="required">*</span></label>
			<div class="input-with-icon">
				<i class="forkawesome fa-calculator"></i>

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
			</div>
			</p>

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
			printf(
				'<input id="mail-to" name="mail-to" type="hidden" value="%s" />',
				esc_attr( strrev( base64_encode( $sunflower_mailto ) ) ) // phpcs:ignore
			);
		}
		if ( $sunflower_sendcopy ) {
			echo '<input id="send-copy" name="send-copy" type="hidden" value="1" />';
		}
		?>

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
