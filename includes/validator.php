<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_validate_rows( $rows ) {

    $validated = [];

    $summary = [
        'total'      => count( $rows ),
        'success'    => 0,
        'warning'    => 0,
        'error'      => 0,
        'can_update' => true,
    ];

    $url_counts = [];

    // Count duplicate URLs
    foreach ( $rows as $row ) {

        $url = trim( $row['url'] );

        if ( empty( $url ) ) {
            continue;
        }

        if ( ! isset( $url_counts[ $url ] ) ) {
            $url_counts[ $url ] = 0;
        }

        $url_counts[ $url ]++;

    }

    // Validate each row
    foreach ( $rows as $row ) {

        $status      = 'success';
        $validation  = 'Ready';
        $selected    = true;

        $url = trim( $row['url'] );

        if ( empty( $url ) ) {

            $status      = 'error';
            $validation  = 'URL Missing';
            $selected    = false;

        }
        elseif ( $url_counts[ $url ] > 1 ) {

            $status      = 'warning';
            $validation  = 'Duplicate URL';
            $selected    = true;

        }
        elseif ( ! smbe_find_post_by_url( $url ) ) {

            $status      = 'error';
            $validation  = 'Page Not Found';
            $selected    = false;

        }
        elseif ( empty( $row['title'] ) ) {

            $status      = 'warning';
            $validation  = 'SEO Title Missing';
            $selected    = true;

        }
        elseif ( empty( $row['description'] ) ) {

            $status      = 'warning';
            $validation  = 'Meta Description Missing';
            $selected    = true;

        }

        if ( $status === 'error' ) {
            $summary['can_update'] = false;
        }

        $summary[ $status ]++;

        $row['status']      = $status;
        $row['validation']  = $validation;
        $row['selected']    = $selected;

        $validated[] = $row;

    }

    return [
        'rows'    => $validated,
        'summary' => $summary,
    ];

}