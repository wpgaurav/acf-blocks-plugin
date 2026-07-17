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

// Build wrapper classes
$wrapper_classes = ['acf-changelog'];
if ($className) {
    $wrapper_classes[] = $className;
}

$anchor_attr = $anchor ? ' id="' . esc_attr($anchor) . '"' : '';
?>
<div<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
    <?php
    $data = $block['data'] ?? [];
    foreach ($entries as $entry_index => $entry) :
        $version = esc_html($entry['changelog_version'] ?? '');
        $date = esc_html($entry['changelog_date'] ?? '');
        $items = $entry['changelog_items'] ?? [];

        // Nested repeater may be a count (flat format) — parse sub-items from block data.
        if ( ! empty( $data ) && ( ! is_array( $items ) || empty( $items ) ) ) {
            $items = acf_blocks_get_nested_repeater(
                'changelog_entries_' . $entry_index . '_changelog_items',
                [ 'changelog_type', 'changelog_text' ],
                $data
            );
        }
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
