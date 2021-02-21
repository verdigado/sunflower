<?php

function sunflower_rearrange_comment_fields( $fields ) {

    $fields['comment'] = sprintf('<div class="col-6">%s', $fields['comment'] );

    $fields['author'] = sprintf('<div class="col-6 comment-form-meta">%s', $fields['author'] );
    $fields['cookies'] = sprintf('%s</div>', $fields['cookies'] );
        
    return $fields;
}

add_filter( 'comment_form_fields', 'sunflower_rearrange_comment_fields' );