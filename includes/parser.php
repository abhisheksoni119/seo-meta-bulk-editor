<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function smbe_parse_bulk_data( $bulk_data ) {

    $rows = explode( "\n", trim( $bulk_data ) );

    $parsed = [];

    foreach ( $rows as $row ) {

        $row = trim( $row );

        if ( empty( $row ) ) {
            continue;
        }

        $columns = array_map( 'trim', explode( '|', $row ) );

        $parsed[] = [
            'url'         => $columns[0] ?? '',
            'title'       => $columns[1] ?? '',
            'description' => $columns[2] ?? '',
        ];

    }

    return $parsed;

}