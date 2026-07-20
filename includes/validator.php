<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_validate_row( $row ) {

    $errors = [];

    // Validate URL
    if ( empty( $row['url'] ) ) {

        $errors[] = 'URL is missing';

    } elseif ( ! filter_var( $row['url'], FILTER_VALIDATE_URL ) ) {

        $errors[] = 'Invalid URL';

    }

    // Validate SEO Title
    if ( empty( $row['title'] ) ) {

        $errors[] = 'SEO Title missing';

    }

    // Validate Meta Description
    if ( empty( $row['description'] ) ) {

        $errors[] = 'Meta Description missing';

    }

    return $errors;

}