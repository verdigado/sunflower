<?php

$tag         = $attributes['tag'] ?? '';
$count       = isset( $attributes['count'] ) ? (int) $attributes['count'] : 3;
$next_events = sunflower_get_next_events( $count, $tag );

$classes = $attributes['className'] ?? '';

$is_grid = false;
if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
	$is_grid = true;
}

$return = sprintf(
	' <div class="wp-block-group sunflower-has-background-dim has-background next-events %s">
                    <div class="wp-block-group__inner-container">
                        <h2 class="text-center h1 text-white">%s</h2>
                        <div class="wp-block-button text-center mb-5"><a class="wp-block-button__link no-border-radius" href="%s" rel="">%s</a></div>
                    <div class="row">',
	$classes,
	( $attributes['title'] ?? '' ) ?: __( 'Next events', 'sunflower-next-events' ),
	get_post_type_archive_link( 'sunflower_event' ),
	__( 'all events', 'sunflower' )
);

ob_start();

if ( $is_grid ) {
	$cols = match ( $next_events->post_count ) {
		1 => '',
		2 => 'col-md-6',
		default => 'col-md-6 col-lg-4',
	};
} else {
	$cols = '';
}

while ( $next_events->have_posts() ) {
	$next_events->the_post();

	printf( '<div class="col-12 %s mb-4">', $cols );
	get_template_part( 'template-parts/archive', 'event' );
	echo '</div>';
}

if ( $next_events->post_count === 0 ) {
	printf( '<div class="col-12 text-center h4 text-white">%s</div>', __( 'Currently there are no coming events.', 'sunflower' ) );
}

$return .= ob_get_contents();
ob_end_clean();

echo $return . '</div></div></div>';
