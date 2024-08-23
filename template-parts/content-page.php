<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

$sunflower_show_post_thumbnail = has_post_thumbnail() && ! get_post_meta( $post->ID, '_sunflower_hide_feature_image', true );
$sunflower_styled_layout       = (bool) get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ?? false;
$sunflower_class               = $args['class'] ?? '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $sunflower_class ); ?>>
	<?php if ( ! $sunflower_styled_layout ) { ?>
		<header class="entry-header text-center <?php echo ( has_post_thumbnail() ) ? 'has-post-thumbnail' : 'has-no-post-thumbnail'; ?>">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
		<?php
	}
	?>

	<?php
	if ( $sunflower_show_post_thumbnail ) {
		sunflower_post_thumbnail( $sunflower_styled_layout, true );
	}
	?>

	<div class="entry-content accordion">
		<?php
			the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sunflower' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
