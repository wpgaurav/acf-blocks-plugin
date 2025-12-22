<?php
/**
 * Register ACF block: Coupon Code
 */

if (!function_exists('acf_register_block_type')) {
    return;
}

acf_register_block_type([
    'name'              => 'cb-coupon-code',
    'title'             => __('Coupon Code'),
    'description'       => __('A coupon code block with offer details, copyable coupon code, and discount activation button.'),
    'render_template'   => 'blocks/coupon-code/coupon-code.php',
    'category'          => 'common',
    'icon'              => 'tickets',
    'keywords'          => ['coupon', 'discount', 'offer'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'anchor' => true,
    ],
]);