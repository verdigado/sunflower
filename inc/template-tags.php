<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package sunflower
 */

if ( ! function_exists( 'sunflower_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function sunflower_posted_on() {

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		echo '<span class="posted-on">' . wp_kses(
			$time_string,
			array(
				'post',
				'time' => array(
					'class'    => array(),
					'datetime' => array(),
				),
			)
		) . '</span>';
	}
endif;

if ( ! function_exists( 'sunflower_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function sunflower_posted_by() {
		$byline = '| <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>';

		echo '<span class="byline"> ' . wp_kses_post( $byline ) . '</span>';
	}
endif;

if ( ! function_exists( 'sunflower_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 *
	 * @param boolean $show_sharers Wether show sharer in footer.
	 */
	function sunflower_entry_footer( $show_sharers = false ) {
		?>
			<div class="d-flex mt-2 mb-2">
				<?php
				if ( $show_sharers ) {
					sunflower_the_social_media_sharers();
				}
				?>
				<div>
		<?php

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'sunflower' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links small">%s</span>', wp_kses_post( $categories_list ) );
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'sunflower' ) );

			if ( $categories_list && $tags_list ) {
				echo '<br>';
			}

			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links small">%s</span>', wp_kses_post( $tags_list ) );
			}
		}

		?>

				</div>
			</div>
		<?php
	}
endif;

/**
 * Make the Sunflower social sharers.
 */
function sunflower_the_social_media_sharers() {

	$sharer = array();
	if ( sunflower_get_setting( 'sunflower_sharer_twitter' ) || sunflower_get_setting( 'sunflower_sharer_x_twitter' ) ) {
		$sharer[] = sprintf(
			'<a href="https://twitter.com/intent/tweet?text=%s&url=%s" target="_blank" title="%s" class="sharer"><i class="fab fa-x-twitter"></i></a>',
			rawurlencode( (string) get_the_title() ),
			get_permalink(),
			__( 'Share on X (Twitter) ', 'sunflower' )
		);
	}

	if ( sunflower_get_setting( 'sunflower_sharer_facebook' ) ) {
		$sharer[] = sprintf(
			'<a href="https://www.facebook.com/sharer/sharer.php?u=%s" target="_blank" title="%s" class="sharer"><i class="fab fa-facebook-f"></i></a>',
			rawurlencode( (string) get_the_title() ),
			__( 'Share on Facebook ', 'sunflower' )
		);
	}

	if ( sunflower_get_setting( 'sunflower_sharer_whatsapp' ) ) {
		$sharer[] = sprintf(
			'<a href="https://wa.me/?text=%s" target="_blank" title="%s" class="sharer"><i class="fab fa-whatsapp"></i></a>',
			rawurlencode( (string) get_the_title() ),
			__( 'Share on WhatsApp ', 'sunflower' )
		);
	}

	if ( sunflower_get_setting( 'sunflower_sharer_mail' ) ) {
		$sharer[] = sprintf(
			'<a href="MAILTO:?subject=%s&body=%s" target="_blank" title="%s" class="sharer"><i class="fas fa-envelope"></i></a>',
			rawurlencode( (string) get_the_title() ),
			get_permalink(),
			__( 'send mail ', 'sunflower' )
		);
	}

	if ( count( $sharer ) > 0 ) {
		printf(
			'<div class="social-media-sharers mb-5">%s</div>',
			wp_kses_post( implode( ' ', $sharer ) )
		);
	}
}

if ( ! function_exists( 'sunflower_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 *
	 * @param boolean $styled_layout Is this a styled layout.
	 * @param boolean $caption Show the caption.
	 */
	function sunflower_post_thumbnail( $styled_layout = false, $caption = false ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		global $post;

		if ( is_singular() ) :
			?>
			<div class="post-thumbnail
			<?php
			if ( $styled_layout ) {
				echo ' mt-1';
			}
			?>
			">
				<?php
				the_post_thumbnail(
					'null',
					array(
						'class' => 'w-100 border-radius',
					)
				);
				?>

			<?php
			if ( $caption ) {
				$caption = get_post( get_post_thumbnail_id() )->post_excerpt;
				if ( ! empty( $caption ) ) {
					?>
				<figcaption><?php echo esc_attr( $caption ); ?></figcaption>
					<?php
				}
			}
			?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>
			<?php
				$classes = array( 'post-thumbnail' );

			the_post_thumbnail(
				'medium_large',
				array(
					'alt'   => the_title_attribute(
						array(
							'echo' => false,
						)
					),
					'class' => implode( ' ', $classes ),
				)
			);
			?>


			<?php
		endif; // End is_singular().
	}
endif;
