<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div class="full-width bg-lightgreen pt-5 pb-5">
	<div class="container">
		<h4 class="h2 text-center mt-3 mb-5"><?php _e( 'Comment article', 'sunflower' ); ?></h4>
		<?php
		$divider = ( is_user_logged_in() ) ? '' : '</div><div class="col-12 col-md-6">';
		$args    = array(
			'title_reply'          => __( 'comment article', 'sunflower' ),
			'label_submit'         => __( 'send', 'sunflower' ),
			'comment_notes_before' => '<div class="col-12 col-md-6">',
			'comment_notes_after'  => '<p>' . sprintf(
				__( 'Your mail will not be published. Required fields are marked with a *. For more info see <a href="%s">privacy</a>', 'sunflower' ),
				get_privacy_policy_url()
			) . '</p>' . $divider,
			'class_form'           => 'row',
			'title_reply'          => '',
				// 'submit_field'            => '<p class="form-submit col-12">%1$s %2$s</p>'
		);

		comment_form( $args );
		?>
	</div>
</div>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
		<?php
		$sunflower_comment_count = get_comments_number();
		if ( '1' === $sunflower_comment_count ) {
			_e( 'One comment', 'sunflower' );
		} else {
			printf(
			/* translators: 1: comment count number, 2: title. */
				esc_html( _nx( '%1$s commment', '%1$s comments', $sunflower_comment_count, 'context', 'sunflower' ) ),
				number_format_i18n( $sunflower_comment_count ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 0,
			)
		);
		?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'sunflower' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().


	?>

</div><!-- #comments -->
