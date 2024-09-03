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

$sunflower_mailto        = $attributes['mailTo'] ?? '';
$sunflower_sendcopy      = $attributes['sendCopy'] ?? 0;
$sunflower_display_phone = $attributes['displayPhone'] ?? false;
$sunflower_require_phone = $attributes['requirePhone'] ?? false;
$sunflower_require_mail  = $attributes['requireMail'] ?? false;

?>

<div class="comment-respond mb-5">
	<?php printf( '<h2 id="contact-form-title" class="text-center h1">%s</h2>', esc_attr( $sunflower_title ) ); ?>
	<form id="sunflower-contact-form" method="post" class="row">
	<?php wp_nonce_field( 'sunflower_contact_form' ); ?>

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
		<p class="comment-form-email">
			<label for="captcha"><?php esc_attr_e( 'How much is 1 + 1 ?', 'sunflower-contact-form' ); ?> <span class="required">*</span></label>
			<input id="captcha" name="captcha" type="text" value="" size="30" maxlength="100" required="required"/>
		</p>

	</div>

		<?php
		if ( $sunflower_mailto ) {
			printf( '<input id="mail-to" name="mail-to" type="hidden" value="%s" />', esc_attr( strrev( base64_encode( $sunflower_mailto ) ) ) ); // phpcs:ignore
		}
		if ( $sunflower_sendcopy ) {
			printf( '<input id="send-copy" name="send-copy" type="hidden" value="1" />' );
		}
		?>

		<p class="form-submit">
			<input name="submit" type="submit" id="submit" class="submit" value="<?php esc_attr_e( 'submit', 'sunflower-contact-form' ); ?>"/>
		</p>
	</form>
</div>
