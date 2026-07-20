<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_find_post_by_url( $url ) {

    $post_id = url_to_postid( $url );

    return $post_id;

}