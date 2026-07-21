<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update Rank Math SEO meta for the selected posts.
 *
 * Loops through validated rows, skips any row that is not in the selected
 * post ID list or that carries a hard error, and writes rank_math_title and
 * rank_math_description via update_post_meta() for every row that passes.
 *
 * @param array $rows             Validated rows returned by smbe_validate_rows().
 * @param int[] $selected_post_ids WordPress post IDs chosen by the user.
 * @return array { updated: int, skipped: int, failed: int }
 */
function smbe_update_rows( $rows, $selected_post_ids ) {

	$counts = array(
		'updated' => 0,
		'skipped' => 0,
		'failed'  => 0,
	);

	$processed_ids = array();

	foreach ( $rows as $row ) {

		// Hard-error rows cannot be updated.
		if ( 'error' === $row['status'] ) {
			$counts['skipped']++;
			continue;
		}

		$post_id = smbe_find_post_by_url( $row['url'] );

		// Row did not resolve to a real post.
		if ( ! $post_id ) {
			$counts['skipped']++;
			continue;
		}

		// Row was not selected by the user.
		if ( ! in_array( $post_id, $selected_post_ids, true ) ) {
			$counts['skipped']++;
			continue;
		}

		// Write both Rank Math meta keys.
		$new_title = sanitize_text_field( $row['title'] );
		$new_desc  = sanitize_textarea_field( $row['description'] );

		$title_result = update_post_meta( $post_id, 'rank_math_title',       $new_title );
		$desc_result  = update_post_meta( $post_id, 'rank_math_description', $new_desc );

		// update_post_meta() returns false on both a DB error and a same-value no-op.
		// Re-read the stored value to tell them apart: if it matches what we wanted,
		// the meta is correct (success); if not, a genuine failure occurred.
		$title_ok = ( false !== $title_result ) || ( get_post_meta( $post_id, 'rank_math_title',       true ) === $new_title );
		$desc_ok  = ( false !== $desc_result  ) || ( get_post_meta( $post_id, 'rank_math_description', true ) === $new_desc );

		if ( $title_ok && $desc_ok ) {
			$processed_ids[] = $post_id;
			$counts['updated']++;
		}

	}

	// Any selected ID that was never matched to a valid row counts as failed.
	$counts['failed'] = count( array_diff( $selected_post_ids, $processed_ids ) );

	return $counts;

}
