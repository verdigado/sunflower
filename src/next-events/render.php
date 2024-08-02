<?php
/**
 * Render the Sunflower next events block.
 *
 * @package sunflower
 */

$sunflower_tag         = $attributes['tag'] ?? '';
$sunflower_count       = isset( $attributes['count'] ) ? (int) $attributes['count'] : 3;
$sunflower_next_events = sunflower_get_next_events( $sunflower_count, $sunflower_tag );

$sunflower_classes = $attributes['className'] ?? '';

$sunflower_is_grid = false;
if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
	$sunflower_is_grid = true;
}

$sunflower_return = sprintf(
	' <div class="wp-block-group alignfull sunflower-has-background-dim has-text-color has-white-color has-background is-vertical is-content-justification-center is-content-justification-center is-layout-flex wp-block-group-is-layout-flex next-events %s">
                    <div class="wp-block-group__inner-container">
                        <h2 class="wp-block-heading has-text-align-center h1">%s</h2>
                        <div class="wp-block-button text-center mb-5"><a class="wp-block-button__link no-border-radius" href="%s" rel="">%s</a></div>
                    <div class="row">',
	$sunflower_classes,
	( $attributes['title'] ?? '' ) ? ( $attributes['title'] ?? '' ) : __( 'Next events', 'sunflower-next-events' ),
	get_post_type_archive_link( 'sunflower_event' ),
	__( 'all events', 'sunflower' )
);

ob_start();

if ( $sunflower_is_grid ) {
	$sunflower_cols = match ( $sunflower_next_events->post_count ) {
		1 => '',
		2 => 'col-md-6',
		default => 'col-md-6 col-lg-4',
	};
} else {
	$sunflower_cols = '';
}

while ( $sunflower_next_events->have_posts() ) {
	$sunflower_next_events->the_post();

	printf( '<div class="col-12 %s mb-4">', esc_attr( $sunflower_cols ) );
	get_template_part( 'template-parts/archive', 'event' );
	echo '</div>';
}

if ( 0 === $sunflower_next_events->post_count ) {
	printf( '<div class="col-12 text-center h4 text-white">%s</div>', esc_attr__( 'Currently there are no coming events.', 'sunflower' ) );
}

$sunflower_return .= ob_get_contents();
ob_end_clean();

echo wp_kses_post( $sunflower_return ) . '</div></div></div>';
