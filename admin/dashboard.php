<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Collect POST values.
$bulk_data        = isset( $_POST['bulk_data'] ) ? wp_unslash( $_POST['bulk_data'] ) : '';
$first_row_header = isset( $_POST['first_row_header'] );
$smbe_action      = isset( $_POST['smbe_action'] ) ? sanitize_key( $_POST['smbe_action'] ) : '';

// Run parse + validate when either submit button was used.
$validated     = false;
$rows          = array();
$summary       = array();
$update_result = null;
$empty_input   = $smbe_action && '' === trim( $bulk_data );

if ( $smbe_action && ! empty( $bulk_data ) ) {

	// Capability check — applies to both validate and update actions.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'seo-meta-bulk-editor' ) );
	}

	// Nonce verification — applies to both validate and update actions.
	if ( ! isset( $_POST['smbe_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['smbe_nonce'] ), 'smbe_bulk_update' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'seo-meta-bulk-editor' ) );
	}

	$data_to_parse = trim( $bulk_data );

	// Strip header row before parsing if the checkbox was checked.
	if ( $first_row_header ) {
		$lines         = explode( "\n", $data_to_parse );
		array_shift( $lines );
		$data_to_parse = implode( "\n", $lines );
	}

	$parsed    = smbe_parse_bulk_data( $data_to_parse );
	$result    = smbe_validate_rows( $parsed );
	$rows      = $result['rows'];
	$summary   = $result['summary'];
	$validated = true;

}

// Update branch — runs only when the "Update Selected" button was pressed.
if ( 'update' === $smbe_action && $validated ) {

	// Sanitise the selected post IDs submitted via checkboxes.
	$selected_post_ids = array();

	if ( isset( $_POST['selected_rows'] ) && is_array( $_POST['selected_rows'] ) ) {
		foreach ( $_POST['selected_rows'] as $raw_id ) {
			$post_id = absint( $raw_id );
			if ( $post_id > 0 ) {
				$selected_post_ids[] = $post_id;
			}
		}
	}

	if ( empty( $selected_post_ids ) ) {
		$update_result = 'no_selection';
	} else {
		$update_result = smbe_update_rows( $rows, $selected_post_ids );
	}

}

?>

<div class="wrap">

	<h1>🚀 SEO Meta Bulk Editor</h1>

	<p>Bulk update SEO Meta Titles and Meta Descriptions for WordPress pages.</p>

	<form method="post">

		<?php wp_nonce_field( 'smbe_bulk_update', 'smbe_nonce' ); ?>

		<label for="smbe_bulk_data"><strong>Bulk SEO Data</strong></label>

		<textarea
			id="smbe_bulk_data"
			name="bulk_data"
			rows="18"
			style="width:100%;font-family:monospace;"
			placeholder="Paste your data here...

URL | SEO Title | Meta Description"><?php echo esc_textarea( $bulk_data ); ?></textarea>

		<p>
			<label>
				<input type="checkbox" name="first_row_header"<?php checked( $first_row_header ); ?>>
				First row contains headers
			</label>
		</p>

		<p>
			<button type="submit" name="smbe_action" value="validate" class="button button-primary">
				Validate Data
			</button>
		</p>

		<?php if ( $empty_input ) : ?>
		<div class="notice notice-warning">
			<p>Please enter some data before validating.</p>
		</div>
		<?php endif; ?>

		<?php if ( $validated ) : ?>

		<div style="background:#fff;border:1px solid #ccd0d4;padding:18px;margin:20px 0;">

			<h2 style="margin-top:0;">Validation Summary</h2>

			<table>

				<tr>
					<td style="padding-right:40px;"><strong>Total Rows</strong></td>
					<td><?php echo esc_html( $summary['total'] ); ?></td>
				</tr>

				<tr>
					<td><strong>✅ Ready</strong></td>
					<td><?php echo esc_html( $summary['success'] ); ?></td>
				</tr>

				<tr>
					<td><strong>⚠ Warnings</strong></td>
					<td><?php echo esc_html( $summary['warning'] ); ?></td>
				</tr>

				<tr>
					<td><strong>❌ Errors</strong></td>
					<td><?php echo esc_html( $summary['error'] ); ?></td>
				</tr>

			</table>

		</div>

		<?php if ( 'no_selection' === $update_result ) : ?>
		<div class="notice notice-warning">
			<p>Please select at least one row before updating.</p>
		</div>
		<?php elseif ( is_array( $update_result ) ) : ?>
		<?php
		$notice_class = 'notice-success';
		if ( $update_result['failed'] > 0 ) {
			$notice_class = 'notice-warning';
		} elseif ( 0 === $update_result['updated'] && $update_result['skipped'] > 0 && 0 === $update_result['failed'] ) {
			$notice_class = 'notice-warning';
		}
		?>
		<div class="notice <?php echo esc_attr( $notice_class ); ?>">
			<p>
				<strong>Update complete.</strong>
				Updated: <?php echo esc_html( $update_result['updated'] ); ?> &nbsp;|&nbsp;
				Skipped: <?php echo esc_html( $update_result['skipped'] ); ?> &nbsp;|&nbsp;
				Failed: <?php echo esc_html( $update_result['failed'] ); ?>
			</p>
		</div>
		<?php endif; ?>

		<h2 style="margin-top:30px;">Preview</h2>

		<table class="widefat striped">

			<thead>
				<tr>
					<th width="40"><span class="screen-reader-text">Select</span></th>
					<th width="70">Status</th>
					<th width="180">Validation</th>
					<th width="70">ID</th>
					<th width="90">Type</th>
					<th>Current Page</th>
					<th>New SEO Title</th>
					<th>New Meta Description</th>
				</tr>
			</thead>

			<tbody>

			<?php foreach ( $rows as $row ) : ?>

				<?php
				$found_post_id = smbe_find_post_by_url( $row['url'] );
				$post          = $found_post_id ? get_post( $found_post_id ) : null;

				if ( $post ) {
					$type          = get_post_type( $found_post_id );
					$current_title = $post->post_title;
					$display_id    = $found_post_id;
				} else {
					$display_id    = '-';
					$type          = '-';
					$current_title = 'Page Not Found';
				}

				// Only rows that resolved to a real post and are not hard errors can be selected.
				$is_updatable = $found_post_id > 0 && 'error' !== $row['status'];
				?>

				<tr>

					<td>
						<?php if ( $is_updatable ) : ?>
						<input
							type="checkbox"
							name="selected_rows[]"
							value="<?php echo esc_attr( $found_post_id ); ?>"
							aria-label="<?php echo esc_attr( $current_title ); ?>"
							checked
						>
						<?php endif; ?>
					</td>

					<td>
						<?php
						switch ( $row['status'] ) {
							case 'success':
								echo '<span aria-hidden="true">✅</span><span class="screen-reader-text">Ready</span>';
								break;
							case 'warning':
								echo '<span aria-hidden="true">⚠️</span><span class="screen-reader-text">Warning</span>';
								break;
							default:
								echo '<span aria-hidden="true">❌</span><span class="screen-reader-text">Error</span>';
						}
						?>
					</td>

					<td><?php echo esc_html( $row['validation'] ); ?></td>

					<td><?php echo esc_html( $display_id ); ?></td>

					<td><?php echo esc_html( $type ); ?></td>

					<td><?php echo esc_html( $current_title ); ?></td>

					<td><?php echo esc_html( $row['title'] ); ?></td>

					<td><?php echo esc_html( $row['description'] ); ?></td>

				</tr>

			<?php endforeach; ?>

			</tbody>

		</table>

		<p style="margin-top:16px;">
			<button type="submit" name="smbe_action" value="update" class="button button-primary">
				Update Selected
			</button>
		</p>

		<?php endif; ?>

	</form>

</div>
