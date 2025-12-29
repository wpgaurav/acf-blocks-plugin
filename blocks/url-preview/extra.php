<?php
/**
 * URL Preview Block - Extra functionality
 *
 * Handles AJAX requests for fetching URL metadata and importing images.
 *
 * @package ACF_Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue admin scripts for the URL Preview block
 */
function acf_url_preview_admin_scripts() {
    if ( ! is_admin() ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->base, array( 'post', 'page' ), true ) ) {
        return;
    }

    // Only enqueue once
    static $enqueued = false;
    if ( $enqueued ) {
        return;
    }
    $enqueued = true;

    $script_path = plugin_dir_path( __FILE__ ) . 'admin.js';
    $script_url = plugin_dir_url( __FILE__ ) . 'admin.js';

    // Create inline script if file doesn't exist
    if ( ! file_exists( $script_path ) ) {
        $inline_script = acf_url_preview_get_admin_script();
        wp_register_script( 'acf-url-preview-admin', '', array( 'jquery' ), '1.0.0', true );
        wp_enqueue_script( 'acf-url-preview-admin' );
        wp_add_inline_script( 'acf-url-preview-admin', $inline_script );
    } else {
        wp_enqueue_script( 'acf-url-preview-admin', $script_url, array( 'jquery' ), filemtime( $script_path ), true );
    }

    wp_localize_script( 'acf-url-preview-admin', 'acfUrlPreview', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'fetchNonce' => wp_create_nonce( 'acf_url_preview_fetch' ),
        'importNonce' => wp_create_nonce( 'acf_url_preview_import' ),
        'fetchingText' => __( 'Fetching...', 'acf-blocks' ),
        'importingText' => __( 'Importing...', 'acf-blocks' ),
        'successText' => __( 'Done!', 'acf-blocks' ),
        'errorText' => __( 'Error occurred', 'acf-blocks' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'acf_url_preview_admin_scripts' );

/**
 * Get the admin JavaScript code
 *
 * @return string JavaScript code
 */
function acf_url_preview_get_admin_script() {
    return <<<'JS'
(function($) {
    'use strict';

    // Debounce function to prevent rapid clicks
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Handle fetch button clicks
    $(document).on('click', '.acf-url-preview-fetch-btn', debounce(function(e) {
        e.preventDefault();

        var $btn = $(this);
        var $status = $btn.siblings('.acf-url-preview-fetch-status');
        var $block = $btn.closest('.acf-block-component, .acf-fields');

        // Find the URL field
        var $urlField = $block.find('[data-name="source_url"] input');
        var url = $urlField.val();

        if (!url) {
            $status.text('Please enter a URL first').css('color', '#d63638');
            return;
        }

        // Disable button and show loading
        $btn.prop('disabled', true);
        $status.text(acfUrlPreview.fetchingText).css('color', '#2271b1');

        $.ajax({
            url: acfUrlPreview.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_url_preview_fetch',
                nonce: acfUrlPreview.fetchNonce,
                url: url
            },
            success: function(response) {
                if (response.success && response.data) {
                    var data = response.data;

                    // Populate title
                    if (data.title) {
                        $block.find('[data-name="preview_title"] input').val(data.title).trigger('change');
                    }

                    // Populate description
                    if (data.description) {
                        $block.find('[data-name="preview_description"] textarea').val(data.description).trigger('change');
                    }

                    // Populate external image URL
                    if (data.image) {
                        $block.find('[data-name="external_image_url"] input').val(data.image).trigger('change');
                    }

                    // Populate image alt
                    if (data.title && !$block.find('[data-name="image_alt"] input').val()) {
                        $block.find('[data-name="image_alt"] input').val(data.title).trigger('change');
                    }

                    $status.text(acfUrlPreview.successText + ' ✓').css('color', '#00a32a');
                } else {
                    $status.text(response.data || acfUrlPreview.errorText).css('color', '#d63638');
                }
            },
            error: function() {
                $status.text(acfUrlPreview.errorText).css('color', '#d63638');
            },
            complete: function() {
                $btn.prop('disabled', false);
                setTimeout(function() {
                    $status.text('');
                }, 3000);
            }
        });
    }, 300));

    // Handle import button clicks
    $(document).on('click', '.acf-url-preview-import-btn', debounce(function(e) {
        e.preventDefault();

        var $btn = $(this);
        var $status = $btn.siblings('.acf-url-preview-import-status');
        var $block = $btn.closest('.acf-block-component, .acf-fields');

        // Find the external image URL
        var $externalField = $block.find('[data-name="external_image_url"] input');
        var imageUrl = $externalField.val();

        if (!imageUrl) {
            $status.text('No external image URL to import').css('color', '#d63638');
            return;
        }

        // Get post ID
        var postId = $('#post_ID').val() || wp.data.select('core/editor').getCurrentPostId();

        // Disable button and show loading
        $btn.prop('disabled', true);
        $status.text(acfUrlPreview.importingText).css('color', '#2271b1');

        $.ajax({
            url: acfUrlPreview.ajaxUrl,
            type: 'POST',
            data: {
                action: 'acf_url_preview_import_image',
                nonce: acfUrlPreview.importNonce,
                image_url: imageUrl,
                post_id: postId
            },
            success: function(response) {
                if (response.success && response.data) {
                    $status.text(acfUrlPreview.successText + ' - Refresh to see image').css('color', '#00a32a');
                    // Note: ACF image field population requires page refresh or complex ACF JS API
                } else {
                    $status.text(response.data || acfUrlPreview.errorText).css('color', '#d63638');
                }
            },
            error: function() {
                $status.text(acfUrlPreview.errorText).css('color', '#d63638');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    }, 300));

})(jQuery);
JS;
}

/**
 * AJAX handler for fetching URL metadata
 */
function acf_url_preview_fetch_handler() {
    // Verify nonce
    if ( ! check_ajax_referer( 'acf_url_preview_fetch', 'nonce', false ) ) {
        wp_send_json_error( __( 'Security check failed', 'acf-blocks' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( __( 'Permission denied', 'acf-blocks' ) );
    }

    $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

    if ( empty( $url ) ) {
        wp_send_json_error( __( 'Invalid URL', 'acf-blocks' ) );
    }

    // Check for cached result (5-minute cache)
    $cache_key = 'acf_url_preview_' . md5( $url );
    $cached = get_transient( $cache_key );

    if ( false !== $cached ) {
        wp_send_json_success( $cached );
    }

    // Fetch the URL
    $response = wp_remote_get( $url, array(
        'timeout' => 15,
        'user-agent' => 'Mozilla/5.0 (compatible; WordPress/' . get_bloginfo( 'version' ) . '; +' . home_url() . ')',
        'headers' => array(
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( $response->get_error_message() );
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    if ( $status_code < 200 || $status_code >= 400 ) {
        wp_send_json_error( sprintf( __( 'HTTP Error: %d', 'acf-blocks' ), $status_code ) );
    }

    $body = wp_remote_retrieve_body( $response );

    if ( empty( $body ) ) {
        wp_send_json_error( __( 'Empty response from URL', 'acf-blocks' ) );
    }

    // Parse the HTML
    $data = acf_url_preview_parse_html( $body, $url );

    // Cache the result for 5 minutes
    set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

    wp_send_json_success( $data );
}
add_action( 'wp_ajax_acf_url_preview_fetch', 'acf_url_preview_fetch_handler' );

/**
 * Parse HTML to extract Open Graph and meta data
 *
 * @param string $html The HTML content
 * @param string $url The original URL (for resolving relative URLs)
 * @return array Extracted data
 */
function acf_url_preview_parse_html( $html, $url ) {
    $data = array(
        'title' => '',
        'description' => '',
        'image' => '',
    );

    // Suppress libxml errors
    libxml_use_internal_errors( true );

    $doc = new DOMDocument();
    $doc->loadHTML( '<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING );

    $xpath = new DOMXPath( $doc );

    // Get Open Graph title
    $og_title = $xpath->query( '//meta[@property="og:title"]/@content' );
    if ( $og_title->length > 0 ) {
        $data['title'] = trim( $og_title->item( 0 )->nodeValue );
    }

    // Fallback to Twitter title
    if ( empty( $data['title'] ) ) {
        $twitter_title = $xpath->query( '//meta[@name="twitter:title"]/@content' );
        if ( $twitter_title->length > 0 ) {
            $data['title'] = trim( $twitter_title->item( 0 )->nodeValue );
        }
    }

    // Fallback to <title> tag
    if ( empty( $data['title'] ) ) {
        $title_tags = $xpath->query( '//title' );
        if ( $title_tags->length > 0 ) {
            $data['title'] = trim( $title_tags->item( 0 )->nodeValue );
        }
    }

    // Get Open Graph description
    $og_desc = $xpath->query( '//meta[@property="og:description"]/@content' );
    if ( $og_desc->length > 0 ) {
        $data['description'] = trim( $og_desc->item( 0 )->nodeValue );
    }

    // Fallback to meta description
    if ( empty( $data['description'] ) ) {
        $meta_desc = $xpath->query( '//meta[@name="description"]/@content' );
        if ( $meta_desc->length > 0 ) {
            $data['description'] = trim( $meta_desc->item( 0 )->nodeValue );
        }
    }

    // Get Open Graph image
    $og_image = $xpath->query( '//meta[@property="og:image"]/@content' );
    if ( $og_image->length > 0 ) {
        $data['image'] = acf_url_preview_resolve_url( $og_image->item( 0 )->nodeValue, $url );
    }

    // Fallback to Twitter image
    if ( empty( $data['image'] ) ) {
        $twitter_image = $xpath->query( '//meta[@name="twitter:image"]/@content' );
        if ( $twitter_image->length > 0 ) {
            $data['image'] = acf_url_preview_resolve_url( $twitter_image->item( 0 )->nodeValue, $url );
        }
    }

    // Fallback to first large image in content
    if ( empty( $data['image'] ) ) {
        $images = $xpath->query( '//img[@src]' );
        foreach ( $images as $img ) {
            $src = $img->getAttribute( 'src' );
            $width = $img->getAttribute( 'width' );

            // Skip small images, icons, and data URIs
            if ( strpos( $src, 'data:' ) === 0 ) {
                continue;
            }

            // Skip if width attribute exists and is less than 600
            if ( ! empty( $width ) && intval( $width ) < 600 ) {
                continue;
            }

            // Skip common icon/logo patterns
            $src_lower = strtolower( $src );
            if ( preg_match( '/(icon|logo|avatar|sprite|badge|button|\.svg)/i', $src_lower ) ) {
                continue;
            }

            // Found a potentially suitable image
            $resolved_src = acf_url_preview_resolve_url( $src, $url );

            // Verify image dimensions if possible (only for first 3 candidates)
            static $checked = 0;
            if ( $checked < 3 ) {
                $checked++;
                $dimensions = acf_url_preview_get_image_dimensions( $resolved_src );
                if ( $dimensions && $dimensions['width'] >= 600 ) {
                    $data['image'] = $resolved_src;
                    break;
                } elseif ( $dimensions && $dimensions['width'] < 600 ) {
                    continue;
                }
            }

            // If we couldn't verify dimensions, use this image as fallback
            if ( empty( $data['image'] ) ) {
                $data['image'] = $resolved_src;
            }

            break;
        }
    }

    // Truncate description if too long
    if ( strlen( $data['description'] ) > 300 ) {
        $data['description'] = substr( $data['description'], 0, 297 ) . '...';
    }

    // Clean up
    libxml_clear_errors();

    return $data;
}

/**
 * Resolve relative URLs to absolute
 *
 * @param string $relative_url The relative URL
 * @param string $base_url The base URL
 * @return string Absolute URL
 */
function acf_url_preview_resolve_url( $relative_url, $base_url ) {
    $relative_url = trim( $relative_url );

    // Already absolute
    if ( preg_match( '/^https?:\/\//i', $relative_url ) ) {
        return $relative_url;
    }

    // Protocol-relative
    if ( strpos( $relative_url, '//' ) === 0 ) {
        $scheme = wp_parse_url( $base_url, PHP_URL_SCHEME ) ?: 'https';
        return $scheme . ':' . $relative_url;
    }

    // Parse base URL
    $parsed = wp_parse_url( $base_url );
    $scheme = isset( $parsed['scheme'] ) ? $parsed['scheme'] : 'https';
    $host = isset( $parsed['host'] ) ? $parsed['host'] : '';

    if ( empty( $host ) ) {
        return $relative_url;
    }

    $base = $scheme . '://' . $host;

    // Absolute path
    if ( strpos( $relative_url, '/' ) === 0 ) {
        return $base . $relative_url;
    }

    // Relative path
    $path = isset( $parsed['path'] ) ? $parsed['path'] : '/';
    $path = dirname( $path );

    return $base . $path . '/' . $relative_url;
}

/**
 * Get image dimensions from URL (with caching)
 *
 * @param string $url Image URL
 * @return array|false Array with width and height, or false on failure
 */
function acf_url_preview_get_image_dimensions( $url ) {
    // Quick check using getimagesize with stream context
    $context = stream_context_create( array(
        'http' => array(
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0 (compatible; WordPress)',
        ),
    ) );

    // Only fetch first 32KB to get dimensions
    $handle = @fopen( $url, 'rb', false, $context );
    if ( ! $handle ) {
        return false;
    }

    $data = fread( $handle, 32768 );
    fclose( $handle );

    // Create temp file
    $temp = wp_tempnam( 'img' );
    file_put_contents( $temp, $data );

    $size = @getimagesize( $temp );
    @unlink( $temp );

    if ( $size && isset( $size[0] ) && isset( $size[1] ) ) {
        return array(
            'width' => $size[0],
            'height' => $size[1],
        );
    }

    return false;
}

/**
 * AJAX handler for importing external image to media library
 */
function acf_url_preview_import_image_handler() {
    // Verify nonce
    if ( ! check_ajax_referer( 'acf_url_preview_import', 'nonce', false ) ) {
        wp_send_json_error( __( 'Security check failed', 'acf-blocks' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'upload_files' ) ) {
        wp_send_json_error( __( 'Permission denied', 'acf-blocks' ) );
    }

    $image_url = isset( $_POST['image_url'] ) ? esc_url_raw( $_POST['image_url'] ) : '';
    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

    if ( empty( $image_url ) ) {
        wp_send_json_error( __( 'Invalid image URL', 'acf-blocks' ) );
    }

    // Check if this image was already imported
    $existing = get_posts( array(
        'post_type' => 'attachment',
        'meta_key' => '_acf_url_preview_source',
        'meta_value' => $image_url,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ) );

    if ( ! empty( $existing ) ) {
        wp_send_json_success( array(
            'attachment_id' => $existing[0],
            'message' => __( 'Image already imported', 'acf-blocks' ),
        ) );
    }

    // Download the image
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $temp_file = download_url( $image_url, 30 );

    if ( is_wp_error( $temp_file ) ) {
        wp_send_json_error( $temp_file->get_error_message() );
    }

    // Get file info
    $file_info = wp_check_filetype( basename( wp_parse_url( $image_url, PHP_URL_PATH ) ) );
    $mime_type = $file_info['type'];

    // If mime type couldn't be determined from URL, try to detect from file
    if ( empty( $mime_type ) ) {
        $mime_type = mime_content_type( $temp_file );
    }

    // Ensure it's an image
    if ( ! $mime_type || strpos( $mime_type, 'image/' ) !== 0 ) {
        @unlink( $temp_file );
        wp_send_json_error( __( 'URL does not point to a valid image', 'acf-blocks' ) );
    }

    // Generate filename
    $extension = str_replace( 'image/', '', $mime_type );
    if ( $extension === 'jpeg' ) {
        $extension = 'jpg';
    }
    $filename = 'url-preview-' . time() . '-' . wp_generate_password( 6, false ) . '.' . $extension;

    $file = array(
        'name' => $filename,
        'type' => $mime_type,
        'tmp_name' => $temp_file,
        'error' => 0,
        'size' => filesize( $temp_file ),
    );

    // Upload to media library
    $attachment_id = media_handle_sideload( $file, $post_id );

    if ( is_wp_error( $attachment_id ) ) {
        @unlink( $temp_file );
        wp_send_json_error( $attachment_id->get_error_message() );
    }

    // Store the source URL as meta
    update_post_meta( $attachment_id, '_acf_url_preview_source', $image_url );

    wp_send_json_success( array(
        'attachment_id' => $attachment_id,
        'url' => wp_get_attachment_url( $attachment_id ),
        'message' => __( 'Image imported successfully', 'acf-blocks' ),
    ) );
}
add_action( 'wp_ajax_acf_url_preview_import_image', 'acf_url_preview_import_image_handler' );
