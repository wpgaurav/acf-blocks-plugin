<?php
/**
 * Post Display Block Template.
 *
 * @var array $block The block settings and attributes.
 */

// Get ACF fields
$selected_posts = acf_blocks_get_field('pd_selected_posts', $block);
$layout = acf_blocks_get_field('pd_layout', $block) ?: 'text_links';
$columns = acf_blocks_get_field('pd_columns', $block) ?: 2;
$show_excerpt = acf_blocks_get_field('pd_show_excerpt', $block) ?: false;
$show_date = acf_blocks_get_field('pd_show_date', $block) ?: false;
$show_author = acf_blocks_get_field('pd_show_author', $block) ?: false;
$title_tag = acf_blocks_get_field('pd_title_tag', $block) ?: 'h3';
$custom_class = acf_blocks_get_field('pd_custom_class', $block) ?: '';
$show_read_more = acf_blocks_get_field('pd_show_read_more', $block);
$read_more_text = acf_blocks_get_field('pd_read_more_text', $block) ?: 'Read More';

// Block unique ID
$block_id = 'post-display-' . $block['id'];
$className = $block['className'] ?? '';

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

if ($className) {
    $container_classes[] = $className;
}

$container_class = implode(' ', $container_classes);

// Detect style variation
$style_variation = '';
if (strpos($className, 'is-style-dark') !== false) {
    $style_variation = 'dark';
} elseif (strpos($className, 'is-style-card') !== false) {
    $style_variation = 'card';
} elseif (strpos($className, 'is-style-minimal') !== false) {
    $style_variation = 'minimal';
} elseif (strpos($className, 'is-style-bordered') !== false) {
    $style_variation = 'bordered';
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($container_class); ?>">
    <?php if ($style_variation === 'dark'): ?>
    <?php
    ob_start();
    ?>
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item {
            background-color: #1a1a2e;
            border-color: #374151;
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-title a {
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-title a:hover {
            color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-meta {
            color: #9ca3af;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-excerpt {
            color: #d1d5db;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button {
            background-color: #ffd700;
            color: #1a1a2e;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button:hover {
            background-color: #ffed4a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display.acf-post-display-layout-text_links .acf-post-display-link {
            color: #ffd700;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ($style_variation === 'card'): ?>
    <?php
    ob_start();
    ?>
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-content {
            padding: 1.5rem;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button {
            border-radius: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ($style_variation === 'minimal'): ?>
    <?php
    ob_start();
    ?>
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item {
            background: transparent;
            border: none;
            border-radius: 0;
            box-shadow: none;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item:hover {
            box-shadow: none;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-content {
            padding: 0;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-thumb {
            border-bottom: none;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button {
            background: transparent;
            color: #0073aa;
            padding: 0;
            text-decoration: underline;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button:hover {
            background: transparent;
            color: #005a87;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ($style_variation === 'bordered'): ?>
    <?php
    ob_start();
    ?>
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item {
            border: 3px solid #1a1a1a;
            border-radius: 0;
            background: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-item:hover {
            box-shadow: 4px 4px 0 #1a1a1a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-title a {
            color: #1a1a1a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button {
            background: #1a1a1a;
            border-radius: 0;
        }
        #<?php echo esc_attr($block_id); ?>.acf-post-display .acf-post-display-read-more-button:hover {
            background: #333333;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php endif; ?>
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
                            <?php echo get_the_post_thumbnail($post->ID, 'thumbnail', ['class' => 'acf-post-display-thumb', 'loading' => 'lazy', 'decoding' => 'async']); ?>
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
                                <?php echo get_the_post_thumbnail($post->ID, 'medium', ['class' => 'acf-post-display-thumb', 'loading' => 'lazy', 'decoding' => 'async']); ?>
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