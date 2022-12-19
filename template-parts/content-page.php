<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */


$styled_layout = @get_post_meta( $post->ID, '_sunflower_styled_layout' )[0] ? true : false;
$metadata      = $class = false;
extract( $args );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
	<?php if ( ! $styled_layout ) { ?>
		<header class="entry-header text-center <?php echo ( has_post_thumbnail() ) ? 'has-post-thumbnail' : 'has-no-post-thumbnail'; ?>">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php } ?>
	
	<?php sunflower_post_thumbnail( $styled_layout, true ); ?>

	<div class="entry-content">
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
