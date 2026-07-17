<?php
/**
 * Performance controls, block usage inventory, and diagnostics.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const ACF_BLOCKS_USAGE_STATE_OPTION = 'acf_blocks_usage_scan_state';
const ACF_BLOCKS_USAGE_EVENT        = 'acf_blocks_usage_scan_batch';

/**
 * Handle Block Manager and diagnostics actions.
 */
function acf_blocks_performance_handle_actions() {
    if ( empty( $_POST['acf_blocks_performance_action'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    check_admin_referer( 'acf_blocks_performance', 'acf_blocks_performance_nonce' );
    $action = sanitize_key( wp_unslash( $_POST['acf_blocks_performance_action'] ) );

    if ( 'save_blocks' === $action ) {
        $known    = wp_list_pluck( acf_blocks_get_block_metadata_cache( true ), 'metadata' );
        $known    = wp_list_pluck( $known, 'name' );
        $disabled = isset( $_POST['disabled_blocks'] ) ? (array) wp_unslash( $_POST['disabled_blocks'] ) : array();
        $disabled = array_values( array_intersect( $known, array_map( 'sanitize_text_field', $disabled ) ) );
        update_option( 'acf_blocks_disabled_blocks', $disabled, false );
        acf_blocks_build_site_editor_bundle( $disabled );
    } elseif ( 'scan_usage' === $action ) {
        acf_blocks_usage_scan_start();
    } elseif ( 'process_images' === $action && function_exists( 'acf_blocks_process_image_queue' ) ) {
        acf_blocks_process_image_queue();
    }

    wp_safe_redirect( admin_url( 'options-general.php?page=acf-blocks-license&acf_blocks_performance_updated=1#acf-blocks-performance' ) );
    exit;
}
add_action( 'admin_init', 'acf_blocks_performance_handle_actions' );

/**
 * Build a site-specific editor bundle that excludes disabled blocks.
 *
 * @param string[] $disabled Disabled block names.
 * @return bool
 */
function acf_blocks_build_site_editor_bundle( $disabled ) {
    $css_files = array();
    foreach ( acf_blocks_get_block_metadata_cache( true ) as $block_info ) {
        if ( in_array( $block_info['metadata']['name'], $disabled, true ) ) {
            continue;
        }
        foreach ( array( 'style', 'editorStyle' ) as $style_key ) {
            foreach ( (array) ( $block_info['metadata'][ $style_key ] ?? array() ) as $style ) {
                if ( is_string( $style ) && 0 === strpos( $style, 'file:./' ) ) {
                    $path = $block_info['folder'] . substr( $style, 7 );
                    if ( is_readable( $path ) ) {
                        $css_files[ $path ] = true;
                    }
                }
            }
        }
    }

    $css = "/* Generated site-specific ACF Blocks editor bundle. */\n";
    foreach ( array_keys( $css_files ) as $path ) {
        $css .= "\n" . (string) file_get_contents( $path );
    }

    $hash    = substr( hash( 'sha256', $css ), 0, 16 );
    $uploads = wp_upload_dir();
    if ( ! empty( $uploads['error'] ) ) {
        return false;
    }
    $dir = trailingslashit( $uploads['basedir'] ) . 'acf-blocks-plugin/';
    if ( ! wp_mkdir_p( $dir ) ) {
        return false;
    }

    $filename = 'editor-blocks-' . $hash . '.css';
    $path     = $dir . $filename;
    if ( false === file_put_contents( $path, $css, LOCK_EX ) ) {
        return false;
    }

    foreach ( (array) glob( $dir . 'editor-blocks-*.css' ) as $old_bundle ) {
        if ( $old_bundle !== $path && is_file( $old_bundle ) ) {
            wp_delete_file( $old_bundle );
        }
    }

    update_option( 'acf_blocks_editor_bundle', array(
        'path'    => $path,
        'url'     => trailingslashit( $uploads['baseurl'] ) . 'acf-blocks-plugin/' . $filename,
        'version' => $hash,
    ), false );
    return true;
}

/**
 * Start or restart the asynchronous block usage scan.
 */
function acf_blocks_usage_scan_start() {
    $counts = array();
    foreach ( acf_blocks_get_block_metadata_cache( true ) as $block_info ) {
        $counts[ $block_info['metadata']['name'] ] = 0;
    }

    update_option( ACF_BLOCKS_USAGE_STATE_OPTION, array(
        'cursor'        => 0,
        'posts_scanned' => 0,
        'counts'        => $counts,
        'running'       => true,
        'completed_at'  => 0,
    ), false );

    if ( ! wp_next_scheduled( ACF_BLOCKS_USAGE_EVENT ) ) {
        wp_schedule_single_event( time() + 1, ACF_BLOCKS_USAGE_EVENT );
    }
}

/**
 * Process a bounded usage-inventory batch.
 */
function acf_blocks_usage_scan_batch() {
    global $wpdb;

    $state = get_option( ACF_BLOCKS_USAGE_STATE_OPTION, array() );
    if ( empty( $state['running'] ) ) {
        return;
    }

    $cursor = absint( $state['cursor'] ?? 0 );
    $limit  = 100;
    $ids    = $wpdb->get_col( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts}
         WHERE ID > %d
           AND post_status NOT IN ('auto-draft','inherit','trash')
           AND post_content LIKE %s
         ORDER BY ID ASC
         LIMIT %d",
        $cursor,
        '%<!-- wp:acf/%',
        $limit
    ) );

    foreach ( $ids as $post_id ) {
        $content = (string) get_post_field( 'post_content', $post_id );
        acf_blocks_usage_count_blocks( parse_blocks( $content ), $state['counts'] );
        $state['posts_scanned']++;
        $state['cursor'] = (int) $post_id;
    }

    if ( count( $ids ) < $limit ) {
        $state['running']      = false;
        $state['completed_at'] = time();
    } else {
        wp_schedule_single_event( time() + 5, ACF_BLOCKS_USAGE_EVENT );
    }

    update_option( ACF_BLOCKS_USAGE_STATE_OPTION, $state, false );
}
add_action( ACF_BLOCKS_USAGE_EVENT, 'acf_blocks_usage_scan_batch' );

/**
 * Count ACF blocks recursively.
 *
 * @param array $blocks Parsed blocks.
 * @param array $counts Counts by block name.
 */
function acf_blocks_usage_count_blocks( $blocks, &$counts ) {
    foreach ( $blocks as $block ) {
        $name = $block['blockName'] ?? '';
        if ( 0 === strpos( $name, 'acf/' ) ) {
            if ( ! isset( $counts[ $name ] ) ) {
                $counts[ $name ] = 0;
            }
            $counts[ $name ]++;
        }
        if ( ! empty( $block['innerBlocks'] ) ) {
            acf_blocks_usage_count_blocks( $block['innerBlocks'], $counts );
        }
    }
}

/**
 * Render performance controls on the plugin options page.
 */
function acf_blocks_render_performance_manager() {
    $all_blocks = acf_blocks_get_block_metadata_cache( true );
    $disabled   = acf_blocks_get_disabled_blocks();
    $usage      = get_option( ACF_BLOCKS_USAGE_STATE_OPTION, array() );
    $image_queue = function_exists( 'acf_blocks_get_image_queue' ) ? acf_blocks_get_image_queue() : array();
    $manifest   = ACF_BLOCKS_PLUGIN_DIR . 'includes/generated-block-manifest.php';
    $editor_css = ACF_BLOCKS_PLUGIN_DIR . 'assets/css/editor-blocks.css';
    ?>
    <div id="acf-blocks-performance" class="card" style="max-width:1000px;margin-top:20px;">
        <h2 style="margin-top:0;"><?php esc_html_e( 'Performance & Block Manager', 'acf-blocks' ); ?></h2>
        <p><?php esc_html_e( 'Disable blocks the site does not use. Disabled blocks are not registered and their field groups and editor assets are skipped.', 'acf-blocks' ); ?></p>

        <form method="post">
            <?php wp_nonce_field( 'acf_blocks_performance', 'acf_blocks_performance_nonce' ); ?>
            <input type="hidden" name="acf_blocks_performance_action" value="save_blocks">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:8px 16px;">
                <?php foreach ( $all_blocks as $block_info ) :
                    $name  = $block_info['metadata']['name'];
                    $title = $block_info['metadata']['title'] ?? $name;
                    ?>
                    <label style="display:flex;gap:8px;align-items:center;">
                        <input type="checkbox" name="disabled_blocks[]" value="<?php echo esc_attr( $name ); ?>" <?php checked( in_array( $name, $disabled, true ) ); ?>>
                        <span><?php echo esc_html( $title ); ?> <code><?php echo esc_html( $name ); ?></code></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p><button class="button button-primary" type="submit"><?php esc_html_e( 'Save Block Settings', 'acf-blocks' ); ?></button></p>
        </form>

        <hr>
        <h3><?php esc_html_e( 'Diagnostics', 'acf-blocks' ); ?></h3>
        <table class="widefat striped" style="max-width:760px;">
            <tbody>
                <tr><td><?php esc_html_e( 'Registered blocks', 'acf-blocks' ); ?></td><td><?php echo esc_html( count( $all_blocks ) - count( $disabled ) ); ?> / <?php echo esc_html( count( $all_blocks ) ); ?></td></tr>
                <tr><td><?php esc_html_e( 'Current request registration time', 'acf-blocks' ); ?></td><td><?php echo esc_html( number_format_i18n( (float) ( $GLOBALS['acf_blocks_runtime_metrics']['registration_ms'] ?? 0 ), 2 ) ); ?> ms</td></tr>
                <tr><td><?php esc_html_e( 'Generated manifest', 'acf-blocks' ); ?></td><td><?php echo esc_html( is_file( $manifest ) ? size_format( filesize( $manifest ) ) : __( 'Missing', 'acf-blocks' ) ); ?></td></tr>
                <tr><td><?php esc_html_e( 'Editor CSS bundle', 'acf-blocks' ); ?></td><td><?php echo esc_html( is_file( $editor_css ) ? size_format( filesize( $editor_css ) ) : __( 'Missing', 'acf-blocks' ) ); ?></td></tr>
                <tr><td><?php esc_html_e( 'Queued image-localization posts', 'acf-blocks' ); ?></td><td><?php echo esc_html( count( $image_queue ) ); ?></td></tr>
            </tbody>
        </table>

        <h3><?php esc_html_e( 'Block Usage Inventory', 'acf-blocks' ); ?></h3>
        <?php if ( ! empty( $usage ) ) : ?>
            <p><?php printf( esc_html__( 'Scanned %d posts. Status: %s.', 'acf-blocks' ), absint( $usage['posts_scanned'] ?? 0 ), ! empty( $usage['running'] ) ? esc_html__( 'running', 'acf-blocks' ) : esc_html__( 'complete', 'acf-blocks' ) ); ?></p>
            <table class="widefat striped" style="max-width:760px;"><tbody>
                <?php foreach ( (array) ( $usage['counts'] ?? array() ) as $name => $count ) : ?>
                    <tr><td><code><?php echo esc_html( $name ); ?></code></td><td><?php echo esc_html( $count ); ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>

        <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
            <form method="post">
                <?php wp_nonce_field( 'acf_blocks_performance', 'acf_blocks_performance_nonce' ); ?>
                <input type="hidden" name="acf_blocks_performance_action" value="scan_usage">
                <button class="button" type="submit"><?php esc_html_e( 'Refresh Usage Inventory', 'acf-blocks' ); ?></button>
            </form>
            <?php if ( ! empty( $image_queue ) ) : ?>
                <form method="post">
                    <?php wp_nonce_field( 'acf_blocks_performance', 'acf_blocks_performance_nonce' ); ?>
                    <input type="hidden" name="acf_blocks_performance_action" value="process_images">
                    <button class="button" type="submit"><?php esc_html_e( 'Process Image Queue Now', 'acf-blocks' ); ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
add_action( 'acf_blocks_options_page_after_cards', 'acf_blocks_render_performance_manager', 20 );
