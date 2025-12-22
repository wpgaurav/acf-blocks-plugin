<?php
// Accordion Block
acf_register_block_type([
    'name'              => 'accordion',
    'title'             => __('Accordion'),
    'description'       => __('A customizable accordion block with FAQ schema support.'),
    'render_template'   => 'blocks/accordion-block/accordion-block.php',
    'category'          => 'common',
    'icon'              => 'list-view',
    'keywords'          => ['accordion', 'faq', 'toggle'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);