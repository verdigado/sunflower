<?php

function sunflower_rearrange_comment_fields( $fields ) {
    $fields[] = '</div>';

    return $fields;
}

add_filter( 'comment_form_fields', 'sunflower_rearrange_comment_fields' );