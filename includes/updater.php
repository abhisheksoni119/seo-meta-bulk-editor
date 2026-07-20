<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_update_rank_math( $post_id, $title ) {

    update_post_meta(
        $post_id,
        'rank_math_title',
        $title
    );

}