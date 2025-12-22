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
 * Conditionally enqueue block styles only when blocks are used.
 *
 * On the frontend, only loads CSS for blocks present in the content.
 * In the block editor, loads all styles for preview purposes.
 */
function acf_blocks_enqueue_styles_conditionally() {
    global $post;

    // Block styles mapping: block name => CSS file path relative to blocks/
    $block_styles = array(
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
 * Enqueue a single block's stylesheet.
 *
 * @param string $block_name Block name (e.g., 'acf/accordion').
 * @param string $css_file   Path to CSS file relative to blocks directory.
 */
function acf_blocks_enqueue_block_style( $block_name, $css_file ) {
    $handle        = 'acf-blocks-' . str_replace( '/', '-', $block_name );
    $css_url       = ACF_BLOCKS_PLUGIN_URL . 'blocks/' . $css_file;
    $css_file_path = ACF_BLOCKS_PLUGIN_DIR . 'blocks/' . $css_file;

    if ( file_exists( $css_file_path ) ) {
        wp_enqueue_style(
            $handle,
            $css_url,
            array(),
            filemtime( $css_file_path )
        );
    }
}

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
    $block_styles = array(
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
    acf_blocks_enqueue_all_styles( $block_styles );
});

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
