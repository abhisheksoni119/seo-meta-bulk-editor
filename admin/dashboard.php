<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div class="wrap">

<h1>🚀 SEO Meta Bulk Editor</h1>

<p>Bulk update SEO Meta Titles and Meta Descriptions for WordPress pages.</p>

<form method="post">

<textarea
name="bulk_data"
rows="18"
style="width:100%;font-family:monospace;"
placeholder="Paste your data here...

URL | SEO Title | Meta Description"><?php

if ( isset($_POST['bulk_data']) ) {
    echo esc_textarea($_POST['bulk_data']);
}

?></textarea>

<p>

<label>

<input type="checkbox" name="first_row_header">

First row contains headers

</label>

</p>

<p>

<button class="button button-primary">

Validate Data

</button>

</p>

</form>

<?php

if ( isset($_POST['bulk_data']) ) :

$rows = smbe_parse_bulk_data( trim($_POST['bulk_data']) );

?>

<h2 style="margin-top:30px;">Preview</h2>

<table class="widefat striped">

<thead>

<tr>

<th width="70">Status</th>

<th width="70">ID</th>

<th width="90">Type</th>

<th>Current Page</th>

<th>New SEO Title</th>

<th>New Meta Description</th>

</tr>

</thead>

<tbody>

<?php

foreach ( $rows as $row ) :

$post_id = smbe_find_post_by_url( $row['url'] );

if ( $post_id ) {

$status = "✅";

$post = get_post( $post_id );

$type = get_post_type( $post_id );

$current_title = $post->post_title;

} else {

$status = "❌";

$type = "-";

$current_title = "Page Not Found";

}

?>

<tr>

<td><?php echo $status; ?></td>

<td><?php echo $post_id ?: "-"; ?></td>

<td><?php echo esc_html($type); ?></td>

<td><?php echo esc_html($current_title); ?></td>

<td><?php echo esc_html($row['title']); ?></td>

<td><?php echo esc_html($row['description']); ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

</div>