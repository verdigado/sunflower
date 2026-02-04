<?php
/**
 * Render the Sunflower next events block.
 *
 * @package Sunflower 26
 */

$sunflower_tag         = $attributes['tag'] ?? '';
$sunflower_count       = isset( $attributes['count'] ) ? (int) $attributes['count'] : 3;
$sunflower_title       = ( $attributes['title'] ?? '' ) ? ( $attributes['title'] ?? '' ) : __( 'Next events', 'sunflower-next-events' );
$sunflower_classes     = $attributes['className'] ?? '';
$sunflower_archive_url = get_post_type_archive_link( 'sunflower_event' );

$sunflower_next_events = sunflower_get_next_events( $sunflower_count, $sunflower_tag );


$sunflower_return = sprintf(
	'<div class="wp-block-group alignfull has-text-color has-white-color has-background is-vertical is-content-justification-center is-layout-flex wp-block-group-is-layout-flex next-events %s">
		<div class="wp-block-group__inner-container">
			<h2 class="wp-block-heading has-text-align-center h1">%s</h2>
			<div class="row">',
	esc_attr( $sunflower_classes ),
	esc_html( $sunflower_title )
);

ob_start();

$sunflower_cols = match ( (int) $sunflower_next_events->post_count ) {
	1 => '',
	2 => 'col-md-6',
	default => 'col-md-6 col-lg-4',
};

while ( $sunflower_next_events->have_posts() ) {
	$sunflower_next_events->the_post();

	printf( '<div class="col-12 %s mb-4">', esc_attr( $sunflower_cols ) );
	get_template_part( 'template-parts/archive', 'event' );
	echo '</div>';
}
wp_reset_postdata();

if ( 0 === (int) $sunflower_next_events->post_count ) {
	printf(
		'<div class="col-12 text-center h4 text-white">%s</div>',
		esc_html__( 'Currently there are no coming events.', 'sunflower' )
	);
}

$sunflower_return .= ob_get_clean();


$sunflower_return .= sprintf(
	'</div>
			<div class="wp-block-button text-center mb-5">
				<a class="wp-block-button__link" href="%s" rel="">%s</a>
			</div>
		</div>
	</div>',
	esc_url( $sunflower_archive_url ),
	esc_html__( 'all events', 'sunflower' )
);

echo wp_kses_post( $sunflower_return );
