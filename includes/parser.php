<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function smbe_parse_bulk_data( $bulk_data ) {

	$rows = preg_split( "/\r\n|\r|\n/", trim( $bulk_data ) );

	$parsed = [];

	foreach ( $rows as $row ) {

		$row = trim( $row );

		if ( empty( $row ) ) {
			continue;
		}

		/*
		 * Supported formats:
		 * 1. Pipe-separated
		 *    URL | SEO Title | Meta Description
		 *
		 * 2. Tab-separated
		 *    (Excel / Google Sheets copy-paste)
		 */

		if ( strpos( $row, "\t" ) !== false ) {

			$columns = preg_split( "/\t+/", $row );

		} else {

			$columns = preg_split( "/\s*\|\s*/", $row );

		}

		$columns = array_map( 'trim', $columns );

		$parsed[] = [
			'url'         => $columns[0] ?? '',
			'title'       => $columns[1] ?? '',
			'description' => $columns[2] ?? '',
		];

	}

	return $parsed;

}