<?php
/**
 * Register ACF block: Product Block
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'pl_block',
    'title'             => __('Product List Block'),
    'description'       => __('A product block with rank, icon, name, description, pricing, coupons, and offer buttons.'),
    'render_template'   => 'blocks/pl-block/pl-block.php',
    'category'          => 'common',
    'icon'              => 'products',
    'keywords'          => ['product', 'offer', 'pricing', 'coupon'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'anchor' => true,
        'multiple' => true,
        'jsx' => true,
    ],
]);