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
 * Treats the following as local:
 *   - Exact host match (with/without www.)
 *   - Any subdomain of the site's root domain (e.g. cdn.example.com)
 *   - Bunny CDN URLs (*.b-cdn.net)
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

    // Exact match.
    if ( $url_bare === $site_bare ) {
        return true;
    }

    // Subdomain match — url_host ends with .site_bare (e.g. cdn.example.com).
    if ( str_ends_with( $url_bare, '.' . $site_bare ) ) {
        return true;
    }

    // Bunny CDN (*.b-cdn.net) — treat as local / owned CDN.
    if ( str_ends_with( $url_host, '.b-cdn.net' ) ) {
        return true;
    }

    /**
     * Filter to let site owners add custom CDN hosts treated as local.
     *
     * @param bool   $is_local Whether the URL is considered local.
     * @param string $url_host The hostname of the URL being checked.
     * @param string $site_bare The bare (no www.) hostname of the site.
     */
    return (bool) apply_filters( 'acf_blocks_is_local_image_url', false, $url_host, $site_bare );
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

    // Build a readable filename: domain-com-slug-words.ext
    // Keep the old MD5 name for backwards-compat lookups.
    $md5_filename      = md5( $url ) . '.' . $extension;
    $readable_filename = acf_blocks_readable_image_filename( $url, $extension );

    // Check if already downloaded under either name (old MD5 or new readable).
    foreach ( array( $readable_filename, $md5_filename ) as $candidate ) {
        $candidate_path = $dir_info['dir'] . $candidate;
        $candidate_url  = $dir_info['url'] . $candidate;

        if ( file_exists( $candidate_path ) ) {
            acf_blocks_ensure_localised_attachment( $candidate_path, $candidate_url, $candidate );
            return $candidate_url;
        }
    }

    $filename    = $readable_filename;
    $target_path = $dir_info['dir'] . $filename;
    $target_url  = $dir_info['url'] . $filename;

    // WordPress download helper.
    if ( ! function_exists( 'download_url' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    // Block requests to private/internal IPs (SSRF protection).
    if ( function_exists( 'wp_http_validate_url' ) && ! wp_http_validate_url( $url ) ) {
        return false;
    }

    $tmp_file = download_url( $url, 15 );

    if ( is_wp_error( $tmp_file ) ) {
        return false;
    }

    // Verify the downloaded file is a valid image.
    $image_type = wp_get_image_mime( $tmp_file );

    if ( ! $image_type ) {
        // Allow SVGs only when the MIME type also confirms SVG content.
        $finfo = function_exists( 'mime_content_type' ) ? mime_content_type( $tmp_file ) : '';

        if ( $extension !== 'svg' || false === strpos( $finfo, 'svg' ) ) {
            if ( file_exists( $tmp_file ) ) {
                unlink( $tmp_file );
            }
            return false;
        }
    }

    // Move temp file to target directory.
    $moved = rename( $tmp_file, $target_path );

    if ( ! $moved ) {
        $moved = copy( $tmp_file, $target_path );
        if ( file_exists( $tmp_file ) ) {
            unlink( $tmp_file );
        }
    }

    if ( ! $moved ) {
        return false;
    }

    // Register as a WP attachment so WordPress generates image sizes for srcset.
    acf_blocks_ensure_localised_attachment( $target_path, $target_url, $filename );

    return $target_url;
}

/**
 * Ensure a localized image file has a corresponding WordPress attachment.
 *
 * Creates an attachment post and generates image metadata (thumbnails)
 * if one does not already exist. This enables srcset/sizes support for
 * images downloaded by the localizer.
 *
 * @param string $file_path Absolute path to the image file.
 * @param string $file_url  Public URL of the image file.
 * @param string $filename  The filename (e.g. "md5hash.jpg").
 * @return int Attachment ID, or 0 on failure.
 */
function acf_blocks_ensure_localised_attachment( $file_path, $file_url, $filename ) {
    // Check if an attachment already exists for this file.
    $existing_id = acf_blocks_get_attachment_id_by_file( $file_path );
    if ( $existing_id ) {
        return $existing_id;
    }

    // Determine MIME type.
    $mime_type = wp_check_filetype( $filename )['type'];
    if ( ! $mime_type ) {
        return 0;
    }

    // Build the relative path WordPress expects for _wp_attached_file meta.
    $upload_dir = wp_upload_dir();
    $relative_path = str_replace( trailingslashit( $upload_dir['basedir'] ), '', $file_path );

    $attachment_data = array(
        'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
        'post_mime_type' => $mime_type,
        'post_status'    => 'inherit',
    );

    $attachment_id = wp_insert_attachment( $attachment_data, $file_path );

    if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
        return 0;
    }

    // Set the correct relative path so attachment_url_to_postid() can find it.
    update_post_meta( $attachment_id, '_wp_attached_file', $relative_path );

    // Generate image sizes and metadata.
    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
    if ( ! empty( $metadata ) ) {
        wp_update_attachment_metadata( $attachment_id, $metadata );
    }

    return $attachment_id;
}

/**
 * Find an existing attachment ID by its file path.
 *
 * @param string $file_path Absolute path to the image file.
 * @return int Attachment ID, or 0 if not found.
 */
function acf_blocks_get_attachment_id_by_file( $file_path ) {
    $upload_dir = wp_upload_dir();
    $relative_path = str_replace( trailingslashit( $upload_dir['basedir'] ), '', $file_path );

    global $wpdb;
    $attachment_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
            $relative_path
        )
    );

    return $attachment_id ? (int) $attachment_id : 0;
}

/**
 * Generate a readable image filename from a URL.
 *
 * Produces names like: example-com-getting-started.jpg
 * A short hash suffix is appended to avoid collisions between
 * different images on the same domain/path.
 *
 * @param string $url       The source image URL.
 * @param string $extension File extension without dot.
 * @return string Sanitized filename.
 */
function acf_blocks_readable_image_filename( $url, $extension ) {
    $parsed = wp_parse_url( $url );
    $host   = isset( $parsed['host'] ) ? $parsed['host'] : 'image';
    $path   = isset( $parsed['path'] ) ? $parsed['path'] : '';

    // Convert domain: example.com → example-com
    $domain_part = str_replace( '.', '-', preg_replace( '/^www\./', '', $host ) );

    // Extract slug words from path.
    $slug_part = '';
    if ( ! empty( $path ) && '/' !== $path ) {
        // Remove file extension from path.
        $clean_path = preg_replace( '/\.[a-zA-Z0-9]{2,5}$/', '', $path );
        $segments   = preg_split( '/[\\/\\-_]+/', trim( $clean_path, '/' ), -1, PREG_SPLIT_NO_EMPTY );
        $words      = array();
        foreach ( $segments as $seg ) {
            $seg = strtolower( $seg );
            if ( strlen( $seg ) > 1 && ! is_numeric( $seg ) ) {
                $words[] = $seg;
            }
            if ( count( $words ) >= 2 ) {
                break;
            }
        }
        if ( ! empty( $words ) ) {
            $slug_part = implode( '-', $words );
        }
    }

    $name = $domain_part;
    if ( ! empty( $slug_part ) ) {
        $name .= '-' . $slug_part;
    }

    // Append a short hash to ensure uniqueness per URL.
    $name .= '-' . substr( md5( $url ), 0, 6 );

    return sanitize_file_name( $name . '.' . $extension );
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
            file_put_contents( $htaccess, "Options -Indexes\n<Files *.php>\ndeny from all\n</Files>\n" );
        }

        // Blank index for directory listing protection.
        $index = $dir . 'index.php';
        if ( ! file_exists( $index ) ) {
            file_put_contents( $index, "<?php\n// Silence is golden.\n" );
        }
    }

    return array( 'dir' => $dir, 'url' => $url );
}
