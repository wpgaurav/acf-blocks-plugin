<?php
/**
 * Changelog Block Template.
 */
$entries = get_field('changelog_entries');
if (empty($entries)) {
    if (!empty($is_preview)) {
        echo '<p><em>Add changelog entries to display version history.</em></p>';
    }
    return;
}
?>
<div class="acf-changelog">
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
