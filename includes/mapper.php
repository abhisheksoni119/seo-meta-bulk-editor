<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_find_post_by_url( $url ) {

    $url = trim( $url );

    if ( empty( $url ) ) {
        return 0;
    }

    // Relative URL
    if ( strpos( $url, 'http' ) !== 0 ) {

        $url = home_url( '/' . ltrim( $url, '/' ) );

    }

    return url_to_postid( $url );

}