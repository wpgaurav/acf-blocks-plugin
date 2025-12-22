<?php
/**
 * Register ACF block: Callout
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'callout',
    'title'             => __('Callout'),
    'description'       => __('Display a styled callout box with title, text, and button.'),
    'render_template'   => 'blocks/callout/template.php',
    'category'          => 'formatting',
    'icon'              => 'megaphone',
    'keywords'          => ['callout', 'cta', 'button'],
    'supports'          => [
        'align' => ['wide', 'full'],
    ],
    'example'           => [
        'attributes' => [
            'mode' => 'preview',
            'data' => [
                'callout_title' => 'Callout',
                'callout_text'  => 'This is an example callout block',
                'is_preview'    => true,
            ],
        ],
    ],
]);