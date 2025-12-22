<?php

/**
 * Load ACF blocks from block.json, auto-load per-block extra.php, with legacy PHP fallback.
 *
 * Folder structure (example):
 * /yourtheme/blocks/
 *   accordion-block/
 *     block.json
 *     accordion-block.php
 *     extra.php            <-- optional, autoloaded
 *     accordion.css
 *   legacy-block/
 *     block.php            <-- legacy fallback
 */

if (! function_exists('load_acf_blocks_from_json')) {
    function load_acf_blocks_from_json()
    {
        // Require ACF to parse the "acf" keys in block.json (renderTemplate, blockVersion, etc)
        if (! class_exists('ACF') && ! function_exists('acf')) {
            return;
        }

        // Allow overriding the blocks directory via filter if needed.
        $blocks_dir = apply_filters(
            'md/acf_blocks_dir',
            trailingslashit(get_stylesheet_directory()) . 'blocks/'
        );

        if (! is_dir($blocks_dir)) {
            return;
        }

        // Get immediate subdirectories
        $block_folders = glob($blocks_dir . '*', GLOB_ONLYDIR);
        if (! $block_folders) {
            return;
        }

        foreach ($block_folders as $block_folder) {
            $block_folder  = trailingslashit($block_folder);
            $block_json    = $block_folder . 'block.json';
            $legacy_php    = $block_folder . 'block.php';
            $extra_php     = $block_folder . 'extra.php';

            if (file_exists($block_json) && is_readable($block_json)) {
                // Register via block.json metadata (WordPress + ACF handle the rest).
                $result = register_block_type($block_folder);

                // Optional: basic error logging if registration fails.
                if (is_wp_error($result)) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log(sprintf(
                            '[ACF Blocks] Failed to register block in "%s": %s',
                            $block_folder,
                            $result->get_error_message()
                        ));
                    }
                    continue; // Skip loading extra.php if registration failed.
                }

                md_register_acf_block_field_groups($block_folder);

                // Autoload per-block extra.php if present — great for hooks, helpers, inline CSS systems, etc.
                if (file_exists($extra_php) && is_readable($extra_php)) {
                    require_once $extra_php;
                }
            } elseif (file_exists($legacy_php) && is_readable($legacy_php)) {
                // Fallback: include legacy PHP registration file.
                require_once $legacy_php;

                md_register_acf_block_field_groups($block_folder);

                // Also autoload extra.php for legacy blocks if available.
                if (file_exists($extra_php) && is_readable($extra_php)) {
                    require_once $extra_php;
                }
            } else {
                // Nothing to load in this folder; optionally log in debug.
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log(sprintf(
                        '[ACF Blocks] Skipped "%s" (no block.json or legacy block.php found).',
                        $block_folder
                    ));
                }
            }
        }
    }
}

// Use acf/init so ACF is fully loaded before reading "acf" keys from block.json.
add_action('acf/init', 'load_acf_blocks_from_json', 5);

if (! function_exists('md_get_icon_markup')) {
    /**
     * Return sanitized icon markup based on the provided value.
     *
     * Allows emoji/text output while also supporting CSS class strings
     * (e.g. "fa-solid fa-star" or "md-icon-sample").
     *
     * @param string $icon Raw icon value from ACF.
     * @return string Markup safe for direct output.
     */
    function md_get_icon_markup($icon)
    {
        $icon = trim((string) $icon);

        if ('' === $icon) {
            return '';
        }

        $contains_emoji = preg_match('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $icon);
        $looks_like_class = preg_match('/^[A-Za-z0-9_\-\s:]+$/u', $icon)
            && (false !== strpos($icon, '-') || false !== strpos($icon, ' '));

        if ($looks_like_class && ! $contains_emoji) {
            return sprintf('<i class="%s" aria-hidden="true"></i>', esc_attr($icon));
        }

        return esc_html($icon);
    }
}

if (! function_exists('md_register_acf_block_field_groups')) {
    /**
     * Load and register local field groups stored as JSON inside a block folder.
     *
     * Supports both single group JSON objects and arrays of groups exported by ACF.
     *
     * @param string $block_folder Absolute path to the block directory.
     * @return void
     */
    function md_register_acf_block_field_groups($block_folder)
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        $json_files = glob(trailingslashit($block_folder) . '*.json');

        if (empty($json_files)) {
            return;
        }

        foreach ($json_files as $json_file) {
            if (substr($json_file, -10) === 'block.json') {
                continue;
            }

            $raw = file_get_contents($json_file);

            if (false === $raw) {
                continue;
            }

            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
                continue;
            }

            // Normalise to an array of groups.
            if (isset($data['key'], $data['fields'])) {
                $data = array($data);
            }

            if (! is_array($data)) {
                continue;
            }

            foreach ($data as $group) {
                if (isset($group['key'], $group['fields'])) {
                    acf_add_local_field_group($group);
                }
            }
        }
    }
}

if (! function_exists('md_enqueue_acf_block_styles_conditionally')) {
    /**
     * Conditionally enqueue block styles only when blocks are actually used on the page.
     *
     * This prevents loading CSS for all blocks on every page, improving performance.
     *
     * @return void
     */
    function md_enqueue_acf_block_styles_conditionally()
    {
        // Only run on frontend and block editor
        if (is_admin() && ! wp_doing_ajax()) {
            return;
        }

        global $post;

        // Get the current post content
        $content = '';
        if ($post instanceof WP_Post) {
            $content = $post->post_content;
        }

        // If we're in the block editor, load all styles (for preview purposes)
        if (is_admin() || (function_exists('get_current_screen') && get_current_screen() && get_current_screen()->is_block_editor())) {
            md_enqueue_all_block_styles();
            return;
        }

        // If no content, nothing to check
        if (empty($content)) {
            return;
        }

        // Define block name to CSS file mapping
        $block_styles = array(
            'acf/accordion'        => 'accordion-block/accordion.css',
            'acf/callout'          => 'callout/callout.css',
            'acf/compare'          => 'compare-block/compare-block.css',
            'acf/cb-coupon-code'   => 'coupon-code/coupon-code.css',
            'acf/cta'              => 'cta-block/cta.css',
            'acf/email-form'       => 'email-form/email-form.css',
            'acf/faq'              => 'faq-block/faq.css',
            'acf/feature-grid'     => 'feature-grid-block/feature-grid.css',
            'acf/gallery'          => 'gallery-block/gallery.css',
            'acf/hero'             => 'hero-block/hero.css',
            'acf/opinion-box'      => 'opinion-box/opinion-box.css',
            'acf/pl-block'         => 'pl-block/pl-block.css',
            'acf/post-display'     => 'post-display/post-display.css',
            'acf/product-box'      => 'product-box/product-box.css',
            'acf/product-cards'    => 'product-cards/product-cards.css',
            'acf/product-review'   => 'product-review/product-review.css',
            'acf/section-block'    => 'section-block/section-block.css',
            'acf/stats'            => 'stats-block/stats.css',
            'acf/tabs'             => 'tabs-block/tabs.css',
            'acf/team-member'      => 'team-member-block/team-member.css',
            'acf/testimonial'      => 'testimonial-block/testimonial.css',
            'acf/thread-builder'   => 'thread-builder/thread-builder.css',
            'acf/video'            => 'video-block/video.css',
            'acf/star-rating'      => 'star-rating-block/star-rating-block.css',
        );

        // Check which blocks are actually used and enqueue their styles
        foreach ($block_styles as $block_name => $css_file) {
            if (has_block($block_name, $content)) {
                $handle = str_replace('/', '-', $block_name) . '-style';
                $css_path = get_stylesheet_directory_uri() . '/blocks/' . $css_file;
                $css_file_path = get_stylesheet_directory() . '/blocks/' . $css_file;

                // Only enqueue if the file exists
                if (file_exists($css_file_path)) {
                    wp_enqueue_style($handle, $css_path, array(), filemtime($css_file_path));
                }
            }
        }
    }
}

if (! function_exists('md_enqueue_all_block_styles')) {
    /**
     * Enqueue all block styles (used in block editor for preview purposes).
     *
     * @return void
     */
    function md_enqueue_all_block_styles()
    {
        $block_styles = array(
            'acf-accordion'        => 'accordion-block/accordion.css',
            'acf-callout'          => 'callout/callout.css',
            'acf-compare'          => 'compare-block/compare-block.css',
            'acf-cb-coupon-code'   => 'coupon-code/coupon-code.css',
            'acf-cta'              => 'cta-block/cta.css',
            'acf-email-form'       => 'email-form/email-form.css',
            'acf-faq'              => 'faq-block/faq.css',
            'acf-feature-grid'     => 'feature-grid-block/feature-grid.css',
            'acf-gallery'          => 'gallery-block/gallery.css',
            'acf-hero'             => 'hero-block/hero.css',
            'acf-opinion-box'      => 'opinion-box/opinion-box.css',
            'acf-pl-block'         => 'pl-block/pl-block.css',
            'acf-post-display'     => 'post-display/post-display.css',
            'acf-product-box'      => 'product-box/product-box.css',
            'acf-product-cards'    => 'product-cards/product-cards.css',
            'acf-product-review'   => 'product-review/product-review.css',
            'acf-section-block'    => 'section-block/section-block.css',
            'acf-stats'            => 'stats-block/stats.css',
            'acf-tabs'             => 'tabs-block/tabs.css',
            'acf-team-member'      => 'team-member-block/team-member.css',
            'acf-testimonial'      => 'testimonial-block/testimonial.css',
            'acf-thread-builder'   => 'thread-builder/thread-builder.css',
            'acf-video'            => 'video-block/video.css',
            'acf-star-rating'      => 'star-rating-block/star-rating-block.css',
        );

        foreach ($block_styles as $handle => $css_file) {
            $css_path = get_stylesheet_directory_uri() . '/blocks/' . $css_file;
            $css_file_path = get_stylesheet_directory() . '/blocks/' . $css_file;

            if (file_exists($css_file_path)) {
                wp_enqueue_style($handle . '-style', $css_path, array(), filemtime($css_file_path));
            }
        }
    }
}

// Hook into wp_enqueue_scripts to conditionally load block styles on frontend
add_action('wp_enqueue_scripts', 'md_enqueue_acf_block_styles_conditionally');

// Hook into enqueue_block_editor_assets to load styles in the block editor
add_action('enqueue_block_editor_assets', 'md_enqueue_all_block_styles');
