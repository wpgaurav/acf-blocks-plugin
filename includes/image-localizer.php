<?php
/**
 * ACF Blocks External Image Localizer
 *
 * Scans ACF block field data on post save, downloads external images
 * to uploads/acf-blocks-plugin/images/, and rewrites the URLs.
 * This improves privacy by serving assets from the local domain.
 *
 * @package ACF_Blocks
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Filter post data before saving to localize external images in ACF blocks.
 *
 * Hooks into wp_insert_post_data so URLs are rewritten before the post
 * reaches the database — no recursive wp_update_post calls needed.
 *
 * @param array $data    Slashed post data.
 * @param array $postarr Raw post data including ID.
 * @return array Modified post data.
 */
function acf_blocks_localize_external_images( $data, $postarr ) {
    // Skip auto-saves and revisions.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $data;
    }

    if ( ! empty( $postarr['ID'] ) && wp_is_post_revision( $postarr['ID'] ) ) {
        return $data;
    }

    $content = $data['post_content'] ?? '';

    // Bail early if no ACF blocks present.
    if ( empty( $content ) || strpos( $content, 'wp:acf/' ) === false ) {
        return $data;
    }

    // Prevent re-entrance when this filter triggers another save.
    static $processing = false;
    if ( $processing ) {
        return $data;
    }
    $processing = true;

    $blocks   = parse_blocks( wp_unslash( $content ) );
    $modified = false;

    acf_blocks_walk_blocks_for_images( $blocks, $modified );

    if ( $modified ) {
        $data['post_content'] = wp_slash( serialize_blocks( $blocks ) );
    }

    $processing = false;

    return $data;
}
add_filter( 'wp_insert_post_data', 'acf_blocks_localize_external_images', 10, 2 );

/**
 * Recursively walk a parsed block tree and localize images in ACF blocks.
 *
 * @param array $blocks   Parsed blocks array (by reference).
 * @param bool  $modified Flag set to true when any URL is replaced.
 */
function acf_blocks_walk_blocks_for_images( &$blocks, &$modified ) {
    foreach ( $blocks as &$block ) {
        // Process ACF blocks only.
        if (
            ! empty( $block['blockName'] )
            && strpos( $block['blockName'], 'acf/' ) === 0
            && ! empty( $block['attrs']['data'] )
            && is_array( $block['attrs']['data'] )
        ) {
            acf_blocks_localize_block_data( $block['attrs']['data'], $modified );
        }

        // Recurse into inner blocks.
        if ( ! empty( $block['innerBlocks'] ) ) {
            acf_blocks_walk_blocks_for_images( $block['innerBlocks'], $modified );
        }
    }
}

/**
 * Scan a block's data array for external image URLs and replace them.
 *
 * Handles two cases:
 *   1. A field value that IS a direct image URL.
 *   2. A field value containing HTML with <img> src attributes.
 *
 * @param array $data     Block data key-value pairs (by reference).
 * @param bool  $modified Flag set to true when any URL is replaced.
 */
function acf_blocks_localize_block_data( &$data, &$modified ) {
    $image_extensions = 'jpe?g|png|gif|webp|avif|bmp|svg';
    $url_pattern      = '#(https?://[^\s"\'<>]+\.(?:' . $image_extensions . ')(?:\?[^\s"\'<>]*)?)#i';

    foreach ( $data as $key => &$value ) {
        if ( ! is_string( $value ) || empty( $value ) ) {
            continue;
        }

        if ( ! preg_match_all( $url_pattern, $value, $matches ) ) {
            continue;
        }

        foreach ( array_unique( $matches[1] ) as $url ) {
            if ( acf_blocks_is_local_url( $url ) ) {
                continue;
            }

            $local_url = acf_blocks_download_external_image( $url );

            if ( $local_url ) {
                $value    = str_replace( $url, $local_url, $value );
                $modified = true;
            }
        }
    }
}

/**
 * Check whether a URL belongs to the current site.
 *
 * Compares host names with and without the www. prefix so that
 * example.com and www.example.com are both treated as local.
 *
 * @param string $url The URL to check.
 * @return bool True if the URL is local.
 */
function acf_blocks_is_local_url( $url ) {
    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $url_host  = wp_parse_url( $url, PHP_URL_HOST );

    if ( ! $url_host ) {
        return true; // Relative or malformed — treat as local.
    }

    // Strip www. for comparison.
    $site_bare = preg_replace( '/^www\./i', '', $site_host );
    $url_bare  = preg_replace( '/^www\./i', '', $url_host );

    return ( $url_bare === $site_bare );
}

/**
 * Download an external image and store it locally.
 *
 * Images are saved to uploads/acf-blocks-plugin/images/ using an
 * MD5-based filename derived from the source URL to avoid collisions
 * while ensuring idempotency (same URL → same file, no re-download).
 *
 * @param string $url External image URL.
 * @return string|false Local URL on success, false on failure.
 */
function acf_blocks_download_external_image( $url ) {
    $dir_info = acf_blocks_get_local_image_dir();

    if ( ! $dir_info ) {
        return false;
    }

    // Determine file extension from the URL path.
    $path_info = pathinfo( wp_parse_url( $url, PHP_URL_PATH ) );
    $extension = strtolower( $path_info['extension'] ?? 'jpg' );

    $allowed = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp' );

    if ( ! in_array( $extension, $allowed, true ) ) {
        $extension = 'jpg';
    }

    $filename    = md5( $url ) . '.' . $extension;
    $target_path = $dir_info['dir'] . $filename;
    $target_url  = $dir_info['url'] . $filename;

    // Already downloaded — return cached path.
    if ( file_exists( $target_path ) ) {
        return $target_url;
    }

    // WordPress download helper.
    if ( ! function_exists( 'download_url' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $tmp_file = download_url( $url, 15 );

    if ( is_wp_error( $tmp_file ) ) {
        return false;
    }

    // Verify the downloaded file is a valid image.
    $image_type = wp_get_image_mime( $tmp_file );

    if ( ! $image_type ) {
        // Allow SVGs which wp_get_image_mime does not recognise.
        $finfo = function_exists( 'mime_content_type' ) ? mime_content_type( $tmp_file ) : '';

        if ( $extension !== 'svg' || strpos( $finfo, 'svg' ) === false ) {
            @unlink( $tmp_file );
            return false;
        }
    }

    // Move temp file to target directory.
    $moved = @rename( $tmp_file, $target_path );

    if ( ! $moved ) {
        $moved = @copy( $tmp_file, $target_path );
        @unlink( $tmp_file );
    }

    if ( ! $moved ) {
        return false;
    }

    return $target_url;
}

/**
 * Get (and create if needed) the local image storage directory.
 *
 * @return array{dir: string, url: string}|false Directory info or false on failure.
 */
function acf_blocks_get_local_image_dir() {
    $upload_dir = wp_upload_dir();

    if ( ! empty( $upload_dir['error'] ) ) {
        return false;
    }

    $dir = trailingslashit( $upload_dir['basedir'] ) . 'acf-blocks-plugin/images/';
    $url = trailingslashit( $upload_dir['baseurl'] ) . 'acf-blocks-plugin/images/';

    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );

        // Protect the directory from PHP execution.
        $htaccess = $dir . '.htaccess';
        if ( ! file_exists( $htaccess ) ) {
            @file_put_contents( $htaccess, "Options -Indexes\n<Files *.php>\ndeny from all\n</Files>\n" );
        }

        // Blank index for directory listing protection.
        $index = $dir . 'index.php';
        if ( ! file_exists( $index ) ) {
            @file_put_contents( $index, "<?php\n// Silence is golden.\n" );
        }
    }

    return array( 'dir' => $dir, 'url' => $url );
}
