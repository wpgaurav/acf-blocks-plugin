<?php
/**
 * Changelog Block Template.
 */
$entries = acf_blocks_get_repeater('changelog_entries', [ 'changelog_version', 'changelog_date', 'changelog_items' ], $block);
if (empty($entries)) {
    if (!empty($is_preview)) {
        echo '<p><em>Add changelog entries to display version history.</em></p>';
    }
    return;
}

// Get block attributes
$className = $block['className'] ?? '';
$anchor = $block['anchor'] ?? '';

// Detect style variation
$is_timeline = $className && strpos($className, 'is-style-timeline') !== false;
$is_compact = $className && strpos($className, 'is-style-compact') !== false;

// Build wrapper classes
$wrapper_classes = ['acf-changelog'];
if ($className) {
    $wrapper_classes[] = $className;
}

// Generate unique ID for scoped styles
$block_id = 'changelog-' . uniqid();
$anchor_attr = $anchor ? ' id="' . esc_attr($anchor) . '"' : '';
?>
<div<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" data-changelog-id="<?php echo esc_attr($block_id); ?>">
    <?php if ($is_timeline || $is_compact) :
        ob_start();
        ?>
        <?php if ($is_timeline) : ?>
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] { padding-left: 2rem; border-left: 2px solid #e5e5e5; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-entry { position: relative; padding-left: 1.5rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-entry::before { content: ''; position: absolute; left: -2.35rem; top: 0.5rem; width: 12px; height: 12px; background: #007bff; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #007bff; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-version { color: #007bff; }
        @media (max-width: 480px) { [data-changelog-id="<?php echo esc_attr($block_id); ?>"] { padding-left: 1.5rem; } }
        <?php endif; ?>
        <?php if ($is_compact) : ?>
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-entry { margin-bottom: 1rem; padding-bottom: 1rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-header { margin-bottom: 0.5rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-version { font-size: 1.1rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-list { gap: 0.25rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-item { font-size: 0.9rem; }
        [data-changelog-id="<?php echo esc_attr($block_id); ?>"] .acf-changelog-badge { padding: 0.1rem 0.4rem; font-size: 0.65rem; }
        <?php endif; ?>
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    endif; ?>
    <?php foreach ($entries as $entry) :
        $version = esc_html($entry['changelog_version'] ?? '');
        $date = esc_html($entry['changelog_date'] ?? '');
        $items = $entry['changelog_items'] ?? [];
    ?>
    <div class="acf-changelog-entry">
        <div class="acf-changelog-header">
            <?php if ($version) : ?><span class="acf-changelog-version"><?php echo $version; ?></span><?php endif; ?>
            <?php if ($date) : ?><span class="acf-changelog-date"><?php echo $date; ?></span><?php endif; ?>
        </div>
        <?php if (!empty($items)) : ?>
        <ul class="acf-changelog-list">
            <?php foreach ($items as $item) :
                $type = esc_attr($item['changelog_type'] ?? 'added');
                $text = esc_html($item['changelog_text'] ?? '');
            ?>
            <li class="acf-changelog-item" data-type="<?php echo $type; ?>">
                <span class="acf-changelog-badge"><?php echo ucfirst($type); ?></span>
                <span class="acf-changelog-text"><?php echo $text; ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
