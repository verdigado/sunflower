<?php

function sunflower_rearrange_comment_fields( $fields ) {
    print_r($fields);
    $comment_field = $fields['notes'];
    unset( $fields['notes'] );
    $fields['notes'] = $comment_field;
    return $fields;
}

//add_filter( 'comment_form_fields', 'sunflower_rearrange_comment_fields' );