<?php
	$sunflower_open_graph_image = get_the_post_thumbnail_url( null, 'medium' ) ?: get_sunflower_setting( 'sunflower_open_graph_fallback_image' );

if ( ! $sunflower_open_graph_image ) {
	$sunflower_open_graph_image = sunflower_parent_or_child( 'assets/img/sunflower.svg' );
}
?>
<!-- Facebook Meta Tags -->
<meta property="og:url" content="<?php the_permalink(); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>">
<meta property="og:description" content="<?php echo esc_attr( strip_tags( get_the_excerpt() ) ); ?>">
<meta property="og:image" content="<?php echo $sunflower_open_graph_image; ?>">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="<?php echo get_site_url(); ?>">
<meta property="twitter:url" content="<?php the_permalink(); ?>">
<meta name="twitter:title" content="<?php echo esc_attr( get_the_title() ); ?>">
<meta name="twitter:description" content="<?php echo esc_attr( strip_tags( get_the_excerpt() ) ); ?>">
<meta name="twitter:image" content="<?php echo $sunflower_open_graph_image; ?>">
