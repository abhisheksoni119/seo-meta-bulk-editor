<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_validate_rows( $rows ) {

    $validated = [];

    $summary = [
        'total' => count( $rows ),
        'success' => 0,
        'warning' => 0,
        'error' => 0,
        'can_update' => true,
    ];

    $url_counts = [];

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

    foreach ( $rows as $row ) {

        $status = 'success';
        $validation = 'Ready';

        $url = trim( $row['url'] );

        if ( empty( $url ) ) {

            $status = 'error';
            $validation = 'URL Missing';

        }
        elseif ( $url_counts[ $url ] > 1 ) {

            $status = 'warning';
            $validation = 'Duplicate URL';

        }
        elseif ( ! smbe_find_post_by_url( $url ) ) {

            $status = 'error';
            $validation = 'Page Not Found';

        }
        elseif ( empty( $row['title'] ) ) {

            $status = 'warning';
            $validation = 'SEO Title Missing';

        }
        elseif ( empty( $row['description'] ) ) {

            $status = 'warning';
            $validation = 'Meta Description Missing';

        }

        if ( $status === 'error' ) {
            $summary['can_update'] = false;
        }

        $summary[ $status ]++;

        $row['status'] = $status;
        $row['validation'] = $validation;

        $validated[] = $row;

    }

    return [
        'rows' => $validated,
        'summary' => $summary,
    ];

}