<?php
/**
 * Register ACF block: Compare Block
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'compare',
    'title'             => __('Compare Block'),
    'description'       => __('A customizable compare card block.'),
    'render_template'   => 'blocks/compare-block/compare-block.php',
    'category'          => 'formatting',
    'icon'              => 'grid-view',
    'keywords'          => ['compare', 'vs', 'custom'],
    'supports'          => ['align' => true,]
]);