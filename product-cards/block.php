<?php
/**
 * Register ACF block: Product Cards
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'product_cards',
    'title'             => __('Product Cards'),
    'description'       => __('A customizable product card block.'),
    'render_template'   => 'blocks/product-cards/product-cards.php',
    'category'          => 'formatting',
    'icon'              => 'grid-view',
    'keywords'          => ['product', 'card', 'custom'],
    'supports'          => ['align' => true,]
]);