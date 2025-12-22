<?php
// Team Member Block
acf_register_block_type([
    'name'              => 'team-member',
    'title'             => __('Team Member'),
    'description'       => __('A team member block with photo, name, title, bio, and social links.'),
    'render_template'   => 'blocks/team-member-block/team-member-block.php',
    'category'          => 'common',
    'icon'              => 'groups',
    'keywords'          => ['team', 'member', 'staff', 'person', 'profile'],
    'supports'          => [
        'align'  => true,
        'mode'   => true,
        'jsx' => true,
    ],
]);
