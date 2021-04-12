<?php

function sunflower_next_events_render( $block_attributes, $content ) {
    $next_events = sunflower_get_next_events( 3 );

    $classes = (isset($block_attributes['className'])) ? $block_attributes['className'] : '';

    $return = sprintf(' <div class="wp-block-group has-background next-events %s">
                        <div class="wp-block-group__inner-container">
                            <h2 class="text-center h1">%s</h2>
                            <div class="wp-block-button text-center mb-5"><a class="wp-block-button__link no-border-radius" href="%s?post_type=sunflower_event" rel="">%s</a></div>
                        <div class="row">',
                        $classes,
                        __('Next events', 'sunflower'),
                        home_url(),
                        __('all events', 'sunflower')
    );

    ob_start();
    switch($next_events->post_count){
        case 1:
            $cols = '';
            break;
        case 2:
            $cols = 'col-md-6';
            break;
        default:
            $cols = 'col-md-6 col-lg-4';
    }

    while ( $next_events->have_posts() ) {
        $next_events->the_post();

        printf('<div class="col-12 %s mb-2">', $cols);
            get_template_part( 'template-parts/archive', 'event');
        echo '</div>';
    }

    if($next_events->post_count === 0 ){
        printf('<div class="col-12 text-center">%s</div>', __('Currently there are no coming events.', 'sunflower'));
    }
  
           
    $return .= ob_get_contents();
    ob_end_clean();

    $return .= '</div></div></div>';

    return $return;
}