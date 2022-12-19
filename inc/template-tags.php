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

		echo '<span class="posted-on">' . $time_string . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'sunflower_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function sunflower_posted_by() {
		$byline = '| <span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>';

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'sunflower_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function sunflower_entry_footer( $showSharers = false ) {

		?>
			<div class="d-flex mt-2 mb-2">
				<?php
				if ( $showSharers ) {
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
				printf( '<span class="cat-links small">%s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'sunflower' ) );

			if ( $categories_list and $tags_list ) {
				echo '<br>';
			}

			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links small">%s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		?>
				</div>
			</div>
		<div> 

		<?php
	}
endif;

function sunflower_the_social_media_sharers() {
	$twitter = $facebook = $mail = false;
	if ( get_sunflower_setting( 'sunflower_sharer_twitter' ) ) {
		$twitter = sprintf(
			'<a href="https://twitter.com/intent/tweet?text=%s&url=%s&via=%s" target="_blank" title="%s" class="sharer"><i class="fab fa-twitter"></i></a>',
			urlencode( get_the_title() ),
			get_permalink(),
			false,
			__( 'Share on twitter ', 'sunflower' )
		);
	}

	if ( get_sunflower_setting( 'sunflower_sharer_facebook' ) ) {
		$facebook = sprintf(
			"<i class='fab fa-facebook sharer' onclick=\"window.open('https://www.facebook.com/sharer/sharer.php?u=%s', 'sharer', 'width=626,height=436')\" title=\"%s\"></i>",
			get_permalink(),
			__( 'Share on facebook ', 'sunflower' )
		);
	}

	if ( get_sunflower_setting( 'sunflower_sharer_mail' ) ) {
		$mail = sprintf(
			'<a href="MAILTO:?subject=%s&body=%s" target="_blank title="%s" class="sharer"><i class="fas fa-envelope"></i></a>',
			urlencode( get_the_title() ),
			get_permalink(),
			__( 'send mail ', 'sunflower' )
		);
	}

	if ( $twitter || $facebook || $mail ) {
		printf(
			'<div class="social-media-sharers mb-5">%s %s %s</div>',
			$twitter,
			$facebook,
			$mail
		);
	}
}

if ( ! function_exists( 'sunflower_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
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
				echo ' mt-1';}
			?>
			 ">
				<?php the_post_thumbnail( 'null', array( 'class' => 'w-100 border-radius' ) ); ?>
	  
			<?php
			if ( $caption ) {
				$caption = get_post( get_post_thumbnail_id() )->post_excerpt;
				if ( ! empty( $caption ) ) {
					?>
				<figcaption><?php echo $caption; ?></figcaption>
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
						'class' => join( ' ', $classes ),
					)
				);
			?>


			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
