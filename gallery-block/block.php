<?php
// Gallery Block
acf_register_block_type([
    'name'              => 'gallery',
    'title'             => __('Gallery'),
    'description'       => __('A responsive gallery block with multiple layout options.'),
    'render_template'   => 'blocks/gallery-block/gallery-block.php',
    'category'          => 'media',
    'icon'              => 'format-gallery',
    'keywords'          => ['gallery', 'images', 'photos', 'grid'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
