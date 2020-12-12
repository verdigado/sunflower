<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sunflower-sass
 */

?>
<div class="row">
	<div class="col-12">
		<footer id="colophon" class="site-footer">
			<div class="site-info">
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'sunflower-sass' ) ); ?>">
					<?php
					/* translators: %s: CMS name, i.e. WordPress. */
					printf( esc_html__( 'Proudly powered by %s', 'sunflower-sass' ), 'WordPress' );
					?>
				</a>
				<span class="sep"> | </span>
					<?php
					/* translators: 1: Theme name, 2: Theme author. */
					printf( esc_html__( 'Theme: %1$s by %2$s.', 'sunflower-sass' ), 'sunflower-sass', '<a href="http://underscores.me/">Tom Rose</a>' );
					?>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div>
</div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
