<?php
// Video Block
acf_register_block_type([
    'name'              => 'video',
    'title'             => __('Video'),
    'description'       => __('A responsive video block supporting YouTube, Vimeo, and self-hosted videos.'),
    'render_template'   => 'blocks/video-block/video-block.php',
    'category'          => 'media',
    'icon'              => 'video-alt3',
    'keywords'          => ['video', 'youtube', 'vimeo', 'media', 'embed'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
