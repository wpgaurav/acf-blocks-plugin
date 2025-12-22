<?php
/**
 * ACF Blocks Core Functions
 *
 * Handles block registration, field group loading, and asset management.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load ACF blocks from block.json files.
 *
 * Scans the blocks directory and registers each block that has a block.json file.
 * Also auto-loads field groups from JSON files and optional extra.php files.
 */
function acf_blocks_load_blocks() {
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $blocks_dir = ACF_BLOCKS_PLUGIN_DIR . 'blocks/';

    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    $block_folders = glob( $blocks_dir . '*', GLOB_ONLYDIR );

    if ( ! $block_folders ) {
        return;
    }

    foreach ( $block_folders as $block_folder ) {
        $block_folder = trailingslashit( $block_folder );
        $block_json   = $block_folder . 'block.json';
        $extra_php    = $block_folder . 'extra.php';

        if ( file_exists( $block_json ) && is_readable( $block_json ) ) {
            // Register via block.json metadata
            $result = register_block_type( $block_folder );

            if ( is_wp_error( $result ) ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( sprintf(
                        '[ACF Blocks] Failed to register block in "%s": %s',
                        $block_folder,
                        $result->get_error_message()
                    ) );
                }
                continue;
            }

            // Register ACF field groups from JSON files
            acf_blocks_register_field_groups( $block_folder );

            // Load extra.php if present
            if ( file_exists( $extra_php ) && is_readable( $extra_php ) ) {
                require_once $extra_php;
            }
        }
    }
}
add_action( 'acf/init', 'acf_blocks_load_blocks', 5 );

/**
 * Register ACF field groups from JSON files in a block folder.
 *
 * Supports both single field group objects and arrays of field groups.
 *
 * @param string $block_folder Absolute path to the block directory.
 */
function acf_blocks_register_field_groups( $block_folder ) {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $json_files = glob( trailingslashit( $block_folder ) . '*.json' );

    if ( empty( $json_files ) ) {
        return;
    }

    foreach ( $json_files as $json_file ) {
        // Skip block.json files
        if ( substr( $json_file, -10 ) === 'block.json' ) {
            continue;
        }

        $raw = file_get_contents( $json_file );

        if ( false === $raw ) {
            continue;
        }

        $data = json_decode( $raw, true );

        if ( json_last_error() !== JSON_ERROR_NONE || empty( $data ) ) {
            continue;
        }

        // Normalize to an array of groups
        if ( isset( $data['key'], $data['fields'] ) ) {
            $data = array( $data );
        }

        if ( ! is_array( $data ) ) {
            continue;
        }

        foreach ( $data as $group ) {
            if ( isset( $group['key'], $group['fields'] ) ) {
                acf_add_local_field_group( $group );
            }
        }
    }
}

/**
 * Get icon markup from an icon field value.
 *
 * Handles both emoji/text output and CSS class-based icons.
 *
 * @param string $icon Raw icon value.
 * @return string Sanitized HTML markup.
 */
function acf_blocks_get_icon_markup( $icon ) {
    $icon = trim( (string) $icon );

    if ( '' === $icon ) {
        return '';
    }

    $contains_emoji   = preg_match( '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $icon );
    $looks_like_class = preg_match( '/^[A-Za-z0-9_\-\s:]+$/u', $icon )
        && ( false !== strpos( $icon, '-' ) || false !== strpos( $icon, ' ' ) );

    if ( $looks_like_class && ! $contains_emoji ) {
        return sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $icon ) );
    }

    return esc_html( $icon );
}

// Make the function available globally for backward compatibility
if ( ! function_exists( 'md_get_icon_markup' ) ) {
    function md_get_icon_markup( $icon ) {
        return acf_blocks_get_icon_markup( $icon );
    }
}

/**
 * Minify CSS string.
 *
 * @param string $css The CSS to minify.
 * @return string Minified CSS.
 */
function acf_blocks_minify_css( $css ) {
    // Remove comments
    $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
    // Remove whitespace
    $css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
    // Remove spaces around selectors and properties
    $css = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $css );
    // Remove trailing semicolons before closing braces
    $css = str_replace( ';}', '}', $css );
    // Remove spaces after colons
    $css = preg_replace( '/:\s+/', ':', $css );
    // Remove extra spaces
    $css = preg_replace( '/\s{2,}/', ' ', $css );

    return trim( $css );
}

/**
 * Get the cache directory for minified assets.
 *
 * @return string Cache directory path.
 */
function acf_blocks_get_cache_dir() {
    $upload_dir = wp_upload_dir();
    $cache_dir  = $upload_dir['basedir'] . '/acf-blocks-cache/';

    if ( ! file_exists( $cache_dir ) ) {
        wp_mkdir_p( $cache_dir );
    }

    return $cache_dir;
}

/**
 * Get the cache URL for minified assets.
 *
 * @return string Cache directory URL.
 */
function acf_blocks_get_cache_url() {
    $upload_dir = wp_upload_dir();
    return $upload_dir['baseurl'] . '/acf-blocks-cache/';
}

/**
 * Get or create minified CSS file.
 *
 * @param string $css_file_path Full path to the source CSS file.
 * @param string $css_file      Relative path to CSS file.
 * @return array Array with 'url' and 'version' keys.
 */
function acf_blocks_get_minified_css( $css_file_path, $css_file ) {
    // In debug mode, skip minification
    if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
        return array(
            'url'     => ACF_BLOCKS_PLUGIN_URL . 'blocks/' . $css_file,
            'version' => filemtime( $css_file_path ),
        );
    }

    $source_mtime = filemtime( $css_file_path );
    $cache_dir    = acf_blocks_get_cache_dir();
    $cache_url    = acf_blocks_get_cache_url();

    // Create a unique filename based on the source path
    $hash       = md5( $css_file );
    $cache_file = $cache_dir . $hash . '.min.css';
    $cache_key  = $cache_dir . $hash . '.time';

    // Check if cached version exists and is up to date
    if ( file_exists( $cache_file ) && file_exists( $cache_key ) ) {
        $cached_time = (int) file_get_contents( $cache_key );
        if ( $cached_time === $source_mtime ) {
            return array(
                'url'     => $cache_url . $hash . '.min.css',
                'version' => $source_mtime,
            );
        }
    }

    // Read and minify the CSS
    $css = file_get_contents( $css_file_path );
    if ( false === $css ) {
        return array(
            'url'     => ACF_BLOCKS_PLUGIN_URL . 'blocks/' . $css_file,
            'version' => $source_mtime,
        );
    }

    $minified = acf_blocks_minify_css( $css );

    // Save minified CSS to cache
    file_put_contents( $cache_file, $minified );
    file_put_contents( $cache_key, $source_mtime );

    return array(
        'url'     => $cache_url . $hash . '.min.css',
        'version' => $source_mtime,
    );
}

/**
 * Enqueue a single block's stylesheet (minified on frontend).
 *
 * @param string $block_name Block name (e.g., 'acf/accordion').
 * @param string $css_file   Path to CSS file relative to blocks directory.
 */
function acf_blocks_enqueue_block_style( $block_name, $css_file ) {
    $handle        = 'acf-blocks-' . str_replace( '/', '-', $block_name );
    $css_file_path = ACF_BLOCKS_PLUGIN_DIR . 'blocks/' . $css_file;

    if ( ! file_exists( $css_file_path ) ) {
        return;
    }

    // Use minified version on frontend, regular on admin
    if ( ! is_admin() ) {
        $minified = acf_blocks_get_minified_css( $css_file_path, $css_file );
        wp_enqueue_style( $handle, $minified['url'], array(), $minified['version'] );
    } else {
        wp_enqueue_style(
            $handle,
            ACF_BLOCKS_PLUGIN_URL . 'blocks/' . $css_file,
            array(),
            filemtime( $css_file_path )
        );
    }
}

/**
 * Block styles mapping.
 *
 * @return array Block name => CSS file path mappings.
 */
function acf_blocks_get_style_map() {
    return array(
        'acf/accordion'      => 'accordion-block/accordion.css',
        'acf/callout'        => 'callout/callout.css',
        'acf/compare'        => 'compare-block/compare-block.css',
        'acf/cb-coupon-code' => 'coupon-code/coupon-code.css',
        'acf/cta'            => 'cta-block/cta.css',
        'acf/email-form'     => 'email-form/email-form.css',
        'acf/faq'            => 'faq-block/faq.css',
        'acf/feature-grid'   => 'feature-grid-block/feature-grid.css',
        'acf/gallery'        => 'gallery-block/gallery.css',
        'acf/hero'           => 'hero-block/hero.css',
        'acf/opinion-box'    => 'opinion-box/opinion-box.css',
        'acf/pl-block'       => 'pl-block/pl-block.css',
        'acf/post-display'   => 'post-display/post-display.css',
        'acf/product-box'    => 'product-box/product-box.css',
        'acf/product-cards'  => 'product-cards/product-cards.css',
        'acf/product-review' => 'product-review/product-review.css',
        'acf/section-block'  => 'section-block/section-block.css',
        'acf/stats'          => 'stats-block/stats.css',
        'acf/tabs'           => 'tabs-block/tabs.css',
        'acf/team-member'    => 'team-member-block/team-member.css',
        'acf/testimonial'    => 'testimonial-block/testimonial.css',
        'acf/thread-builder' => 'thread-builder/thread-builder.css',
        'acf/video'          => 'video-block/video.css',
        'acf/star-rating'    => 'star-rating-block/star-rating-block.css',
    );
}

/**
 * Conditionally enqueue block styles only when blocks are used.
 *
 * On the frontend, only loads minified CSS for blocks present in the content.
 * In the block editor, loads all styles for preview purposes.
 */
function acf_blocks_enqueue_styles_conditionally() {
    global $post;

    $block_styles = acf_blocks_get_style_map();

    // In admin/block editor, load all styles
    if ( is_admin() ) {
        acf_blocks_enqueue_all_styles( $block_styles );
        return;
    }

    // Get post content
    $content = '';
    if ( $post instanceof WP_Post ) {
        $content = $post->post_content;
    }

    if ( empty( $content ) ) {
        return;
    }

    // Only enqueue styles for blocks that are actually used
    foreach ( $block_styles as $block_name => $css_file ) {
        if ( has_block( $block_name, $content ) ) {
            acf_blocks_enqueue_block_style( $block_name, $css_file );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'acf_blocks_enqueue_styles_conditionally' );

/**
 * Enqueue all block styles (used in block editor).
 *
 * @param array $block_styles Array of block name => CSS file mappings.
 */
function acf_blocks_enqueue_all_styles( $block_styles ) {
    foreach ( $block_styles as $block_name => $css_file ) {
        acf_blocks_enqueue_block_style( $block_name, $css_file );
    }
}
add_action( 'enqueue_block_editor_assets', function() {
    acf_blocks_enqueue_all_styles( acf_blocks_get_style_map() );
});

/**
 * Clear CSS cache when plugin is updated.
 */
function acf_blocks_clear_cache() {
    $cache_dir = acf_blocks_get_cache_dir();

    if ( ! is_dir( $cache_dir ) ) {
        return;
    }

    $files = glob( $cache_dir . '*' );
    if ( $files ) {
        foreach ( $files as $file ) {
            if ( is_file( $file ) ) {
                unlink( $file );
            }
        }
    }
}
register_deactivation_hook( ACF_BLOCKS_PLUGIN_FILE, 'acf_blocks_clear_cache' );

/**
 * Get the plugin's blocks directory path.
 *
 * @return string Blocks directory path.
 */
function acf_blocks_get_blocks_dir() {
    return ACF_BLOCKS_PLUGIN_DIR . 'blocks/';
}

/**
 * Get the plugin's blocks directory URL.
 *
 * @return string Blocks directory URL.
 */
function acf_blocks_get_blocks_url() {
    return ACF_BLOCKS_PLUGIN_URL . 'blocks/';
}
