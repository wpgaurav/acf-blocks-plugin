<?php
// Stats Block
acf_register_block_type([
    'name'              => 'stats',
    'title'             => __('Stats/Counter'),
    'description'       => __('A stats block with animated counters to showcase numbers and achievements.'),
    'render_template'   => 'blocks/stats-block/stats-block.php',
    'category'          => 'common',
    'icon'              => 'chart-bar',
    'keywords'          => ['stats', 'counter', 'numbers', 'metrics', 'achievements'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
