<?php
/**
 * Register ACF block: Section Block
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'acf_section',
    'title'             => __('Section Block'),
    'description'       => __('A customizable container block that wraps inner blocks.'),
    'render_template'   => 'blocks/section-block/section-block.php',
    'category'          => 'layout',
    'icon'              => 'editor-insertmore',
    'keywords'          => ['section', 'container', 'wrapper'],
    'supports'          => [
        'align'     => true,
        'jsx'       => true,
        'mode'      => true,
        'multiple'  => true,
    ],
    // 'enqueue_script'    => get_stylesheet_directory_uri() . '/blocks/section-block/editor.js',
]);