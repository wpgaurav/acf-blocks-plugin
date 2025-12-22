<?php
/**
 * Register ACF block: Post Display
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'post-display',
    'title'             => __('Post Display'),
    'description'       => __('Display selected posts in various layouts.'),
    'render_template'   => 'blocks/post-display/post-display.php',
    'category'          => 'common',
    'icon'              => 'admin-post',
    'keywords'          => ['post', 'article', 'display', 'grid'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'anchor' => true,
    ],
]);