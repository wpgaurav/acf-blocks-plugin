<?php
/**
 * Post Display Block Template.
 *
 * @var array $block The block settings and attributes.
 */

// Get ACF fields
$selected_posts = get_field('pd_selected_posts');
$layout = get_field('pd_layout') ?: 'text_links';
$columns = get_field('pd_columns') ?: 2;
$show_excerpt = get_field('pd_show_excerpt') ?: false;
$show_date = get_field('pd_show_date') ?: false;
$show_author = get_field('pd_show_author') ?: false;
$title_tag = get_field('pd_title_tag') ?: 'h3';
$custom_class = get_field('pd_custom_class') ?: '';
$show_read_more = get_field('pd_show_read_more');
$read_more_text = get_field('pd_read_more_text') ?: 'Read More';

// Block unique ID
$block_id = 'post-display-' . $block['id'];

// Begin output
if (!$selected_posts) {
    if (is_admin()) {
        echo '<p>Please select at least one post.</p>';
    }
    return;
}

// CSS classes based on layout
$container_classes = [
    'acf-post-display',
    'acf-post-display-layout-' . $layout,
];

if ($layout === 'grid') {
    $container_classes[] = 'acf-post-display-grid-columns-' . $columns;
}

if ($custom_class) {
    $container_classes[] = $custom_class;
}

$container_class = implode(' ', $container_classes);
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($container_class); ?>">
    <?php if ($layout === 'text_links'): ?>

        <ul class="acf-post-display-list">
            <?php foreach ($selected_posts as $post): ?>
                <li class="acf-post-display-item">
                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="acf-post-display-link">
                        <?php echo esc_html(get_the_title($post->ID)); ?>
                    </a>

                    <?php if ($show_date): ?>
                        <span class="acf-post-display-date">
                            <?php echo get_the_date('', $post->ID); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($show_read_more): ?>
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="acf-post-display-read-more-button acf-post-display-text-link-read-more">
                            <?php echo esc_html($read_more_text); ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
    <?php elseif ($layout === 'thumbnail'): ?>

        <div class="acf-post-display-thumbnail-layout">
            <?php foreach ($selected_posts as $post): ?>
                <div class="acf-post-display-item">
                    <?php if (has_post_thumbnail($post->ID)): ?>
                    <div class="acf-post-display-thumbnail">
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                            <?php echo get_the_post_thumbnail($post->ID, 'thumbnail', ['class' => 'acf-post-display-thumb']); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="acf-post-display-content">
                        <<?php echo esc_attr($title_tag); ?> class="acf-post-display-title">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo esc_html(get_the_title($post->ID)); ?>
                            </a>
                        </<?php echo esc_attr($title_tag); ?>>

                        <?php if ($show_date || $show_author): ?>
                            <div class="acf-post-display-meta">
                                <?php if ($show_date): ?>
                                    <span class="acf-post-display-date">
                                        <?php echo get_the_date('', $post->ID); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($show_author): ?>
                                    <span class="acf-post-display-author">
                                        by <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_excerpt): ?>
                            <div class="acf-post-display-excerpt">
                                <?php echo wp_kses_post(get_the_excerpt($post->ID)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_read_more): ?>
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="acf-post-display-read-more-button">
                                <?php echo esc_html($read_more_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    <?php elseif ($layout === 'grid'): ?>

        <div class="acf-post-display-grid-layout">
            <?php foreach ($selected_posts as $post): ?>
                <article class="acf-post-display-grid-item acf-post-display-item">
                    <?php if (has_post_thumbnail($post->ID)): ?>
                        <div class="acf-post-display-thumbnail">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo get_the_post_thumbnail($post->ID, 'medium', ['class' => 'acf-post-display-thumb']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="acf-post-display-content">
                        <<?php echo esc_attr($title_tag); ?> class="acf-post-display-title">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                <?php echo esc_html(get_the_title($post->ID)); ?>
                            </a>
                        </<?php echo esc_attr($title_tag); ?>>

                        <?php if ($show_date || $show_author): ?>
                            <div class="acf-post-display-meta">
                                <?php if ($show_date): ?>
                                    <span class="acf-post-display-date">
                                        <?php echo get_the_date('', $post->ID); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($show_author): ?>
                                    <span class="acf-post-display-author">
                                        by <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_excerpt): ?>
                            <div class="acf-post-display-excerpt">
                                <?php echo wp_kses_post(get_the_excerpt($post->ID)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_read_more): ?>
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="wp-element-button button button-arrow button-small">
                                <?php echo esc_html($read_more_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
    <?php endif; ?>
</div>