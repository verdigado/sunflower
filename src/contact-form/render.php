<?php
/**
 * Render the Sunflower contact form.
 *
 * @package sunflower
 */

if ( isset( $attributes['title'] ) && ! empty( $attributes['title'] ) ) {
	$sunflower_title = $attributes['title'];
} else {
	$sunflower_title = __( 'Contact Form', 'sunflower-contact-form' );
}

$sunflower_display_phone = $attributes['displayPhone'] ?? false;
$sunflower_require_phone = $attributes['requirePhone'] ?? false;
$sunflower_require_mail  = $attributes['requireMail'] ?? false;
$sunflower_show_captcha  = $attributes['showCaptcha'] ?? true;

// Dynamic Captcha generation.
$sunflower_captcha_num1  = wp_rand( 1, 9 );
$sunflower_captcha_num2  = wp_rand( 1, 9 );
$sunflower_captcha_sum   = $sunflower_captcha_num1 + $sunflower_captcha_num2;
$sunflower_captcha_salt  = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'sunflower_default_fallback_salt';
$sunflower_captcha_token = hash( 'sha256', $sunflower_captcha_sum . $sunflower_captcha_salt );

$sunflower_captcha_expr = sprintf( '%1$d + %2$d', $sunflower_captcha_num1, $sunflower_captcha_num2 );
// translators: %s is the arithmetic expression (e.g., "3 + 5").
$sunflower_placeholder_captcha = sprintf( __( 'How much is %s?', 'sunflower-contact-form' ), $sunflower_captcha_expr );
?>

<div class="comment-respond mb-5">
	<?php printf( '<h2 id="contact-form-title" class="text-center h1">%s</h2>', esc_attr( $sunflower_title ) ); ?>
	<form id="sunflower-contact-form" method="post" class="row">
		<?php wp_nonce_field( 'sunflower_contact_form' ); ?>
		<?php
		// Spam protection: signed render timestamp (time-trap). Captcha state is no
		// longer a client field - the handler reads showCaptcha from the block.
		$sunflower_form_ts     = time();
		$sunflower_form_ts_sig = hash_hmac( 'sha256', (string) $sunflower_form_ts, $sunflower_captcha_salt );
		?>
		<input type="hidden" name="form_ts" value="<?php echo esc_attr( $sunflower_form_ts ); ?>" />
		<input type="hidden" name="form_ts_sig" value="<?php echo esc_attr( $sunflower_form_ts_sig ); ?>" />

		<?php // Honeypot: hidden from humans; bots that fill it are rejected. ?>
		<div class="comment-form-website" aria-hidden="true" style="position:absolute;left:-5000px;top:auto;width:1px;height:1px;overflow:hidden;">
			<label for="website"><?php esc_html_e( 'Leave this field empty', 'sunflower-contact-form' ); ?></label>
			<input id="website" name="website" type="text" tabindex="-1" autocomplete="off" value="" />
		</div>

	<div class="col-12 col-md-6">
		<p class="comment-form-comment">
			<label for="message"><?php esc_attr_e( 'Message', 'sunflower-contact-form' ); ?> <span class="required">*</span></label>
			<textarea id="message" name="message" cols="45" rows="8" maxlength="2000" required="required"></textarea>
		</p>
		<p class="small"><?php echo wp_kses_post( __( 'Please fill in all required (<span class="required">*</span>) fields.', 'sunflower-contact-form' ) ); ?></p>
		<p class="small">
		<?php
			echo wp_kses_post(
				__(
					'By using this form, you consent to the storage and processing of your data through our website.
            Additional information can be found in our privacy policy on <a href="#" id="privacy_policy_url">Datenschutzerklärung</a>',
					'sunflower-contact-form'
				)
			)
			?>
			.
		</p>
	</div>
	<div class="col-12 col-md-6"><p class="comment-form-author">
		<label for="name"><?php esc_attr_e( 'Name', 'sunflower-contact-form' ); ?></label>
		<input id="name" name="name" type="text" value="" size="30" maxlength="245"/>

		</p>
		<p class="comment-form-email">
			<label for="mail">
			<?php
			esc_attr_e( 'E-Mail', 'sunflower-contact-form' );
			$sunflower_require_mail ? print( ' <span class="required">*</span>' ) : '';
			?>
			</label>
			<input id="mail" name="mail" type="email" value="" size="30" maxlength="100" <?php $sunflower_require_mail ? print( 'required="required"' ) : ''; ?>/>
		</p>
		<?php
		if ( $sunflower_display_phone ) {
			?>
			<p class="comment-form-email">
				<label for="phone">
				<?php
				esc_attr_e( 'Phone', 'sunflower-contact-form' );
				$sunflower_require_phone ? print( ' <span class="required">*</span>' ) : '';
				?>
				</label>
				<input id="phone" name="phone" type="tel" value="" size="30" pattern="[0-9\-\+\s]*" <?php $sunflower_require_phone ? print( 'required="required"' ) : ''; ?>/>
			</p>
			<?php
		}
		?>
		<?php if ( $sunflower_show_captcha ) : ?>
			<p class="comment-form-email">
				<label for="captcha">
				<?php
					/* translators: %s is the arithmetic expression (e.g., "3 + 5") */
					echo esc_html( $sunflower_placeholder_captcha );
				?>
				<span class="required">*</span>
			</label>

			<input
						id="captcha"
						name="captcha"
						type="text"
						value=""
						size="30"
						maxlength="100"
						required
					/>
					<input type="hidden" name="captcha_token" value="<?php echo esc_attr( $sunflower_captcha_token ); ?>" />
			</p>
		</div>
		<?php endif; ?>

		<?php
		// Always emit the post ID so the handler can look up this block's trusted
		// server-side attributes (recipient + send-copy flag) via $_POST['postId'].
		echo '<input id="post-id" name="post_id" type="hidden" value="' . esc_attr( get_the_ID() ) . '" />';
		?>

		<p class="form-submit">
			<input name="submit" type="submit" id="submit" class="submit" value="<?php esc_attr_e( 'submit', 'sunflower-contact-form' ); ?>"/>
		</p>
		<div id="form-error" class="bg-danger p-4 text-white" style="display:none;"></div>
	</form>
</div>
