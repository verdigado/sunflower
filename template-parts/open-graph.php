<?php
/**
 * Get open graph image from
 * - featured image
 * - from sunflower settings
 *
 * @package sunflower
 */

$sunflower_open_graph_image = get_the_post_thumbnail_url( null, 'medium' ) ? get_the_post_thumbnail_url( null, 'medium' ) : sunflower_get_setting( 'sunflower_open_graph_fallback_image' );

// If still empty, take the default image.
if ( ! $sunflower_open_graph_image ) {
	$sunflower_open_graph_image = sunflower_parent_or_child( 'assets/img/sunflower1.jpg' );
}
?>

<!-- Facebook Meta Tags -->
<meta property="og:url" content="<?php esc_url( the_permalink() ); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>">
<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( (string) get_the_excerpt() ) ); ?>">
<meta property="og:image" content="<?php echo esc_url( $sunflower_open_graph_image ); ?>">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="<?php echo esc_url( get_site_url() ); ?>">
<meta property="twitter:url" content="<?php the_permalink(); ?>">
<meta name="twitter:title" content="<?php echo esc_attr( get_the_title() ); ?>">
<meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( (string) get_the_excerpt() ) ); ?>">
<meta name="twitter:image" content="<?php echo esc_url( $sunflower_open_graph_image ); ?>">
