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
 * Register custom block category for ACF Blocks.
 *
 * @param array $categories Existing block categories.
 * @return array Modified block categories.
 */
function acf_blocks_register_category( $categories ) {
    return array_merge(
        array(
            array(
                'slug'  => 'acf-blocks',
                'title' => __( 'ACF Blocks', 'acf-blocks' ),
                'icon'  => 'layout',
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'acf_blocks_register_category', 10, 1 );

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

/**
 * Minify CSS string for inline output.
 *
 * Removes comments, whitespace, and unnecessary characters from CSS.
 *
 * @param string $css The CSS string to minify.
 * @return string Minified CSS.
 */
function acf_blocks_minify_css( $css ) {
    // Remove comments
    $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
    // Remove whitespace
    $css = preg_replace( '/\s+/', ' ', $css );
    // Remove space around selectors and braces
    $css = preg_replace( '/\s*([\{\};:,>~+])\s*/', '$1', $css );
    // Remove trailing semicolons before closing braces
    $css = str_replace( ';}', '}', $css );
    // Trim
    $css = trim( $css );
    return $css;
}

/**
 * Enqueue block editor assets for block transforms.
 *
 * Loads JavaScript that enables converting core blocks to ACF Blocks.
 */
function acf_blocks_enqueue_editor_assets() {
    $script_path = ACF_BLOCKS_PLUGIN_DIR . 'assets/js/block-transforms.js';
    $script_url  = ACF_BLOCKS_PLUGIN_URL . 'assets/js/block-transforms.js';

    if ( ! file_exists( $script_path ) ) {
        return;
    }

    wp_enqueue_script(
        'acf-blocks-transforms',
        $script_url,
        array( 'wp-blocks', 'wp-hooks', 'wp-element' ),
        ACF_BLOCKS_VERSION,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'acf_blocks_enqueue_editor_assets' );
