<?php
// Testimonial Block
acf_register_block_type([
    'name'              => 'testimonial',
    'title'             => __('Testimonial'),
    'description'       => __('A customizable testimonial block with quote, author, and rating.'),
    'render_template'   => 'blocks/testimonial-block/testimonial-block.php',
    'category'          => 'common',
    'icon'              => 'format-quote',
    'keywords'          => ['testimonial', 'review', 'quote', 'customer'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
