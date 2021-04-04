<?php

add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }
    if ( ! is_user_logged_in() ) {
        return new WP_Error( '401', 'not allowed.', array('status' => 401) );
    }
    return $result;
    }
);