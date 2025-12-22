<?php
// Tabs Block
acf_register_block_type([
    'name'              => 'tabs',
    'title'             => __('Tabs'),
    'description'       => __('A tabbed content block for organizing information into switchable sections.'),
    'render_template'   => 'blocks/tabs-block/tabs-block.php',
    'category'          => 'common',
    'icon'              => 'index-card',
    'keywords'          => ['tabs', 'tabbed', 'content', 'sections'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
