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

            if ( isset( $_POST['bulk_data'] ) ) {
                echo esc_textarea( $_POST['bulk_data'] );
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

if ( isset( $_POST['bulk_data'] ) ) :

    $parsed = smbe_parse_bulk_data( trim( $_POST['bulk_data'] ) );

$result = smbe_validate_rows( $parsed );

$rows = $result['rows'];

$summary = $result['summary'];

?>

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

<h2 style="margin-top:30px;">Preview</h2>

<table class="widefat striped">

    <thead>

        <tr>

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

        $post_id = smbe_find_post_by_url( $row['url'] );

        if ( $post_id ) {

            $post = get_post( $post_id );

            $type = get_post_type( $post_id );

            $current_title = $post->post_title;

        } else {

            $post_id = "-";
            $type = "-";
            $current_title = "Page Not Found";

        }

        ?>

        <tr>

            <td>

                <?php

                switch ( $row['status'] ) {

                    case 'success':
                        echo '✅';
                        break;

                    case 'warning':
                        echo '⚠️';
                        break;

                    default:
                        echo '❌';

                }

                ?>

            </td>

            <td>

                <?php echo esc_html( $row['validation'] ); ?>

            </td>

            <td>

                <?php echo esc_html( $post_id ); ?>

            </td>

            <td>

                <?php echo esc_html( $type ); ?>

            </td>

            <td>

                <?php echo esc_html( $current_title ); ?>

            </td>

            <td>

                <?php echo esc_html( $row['title'] ); ?>

            </td>

            <td>

                <?php echo esc_html( $row['description'] ); ?>

            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<?php endif; ?>

</div>