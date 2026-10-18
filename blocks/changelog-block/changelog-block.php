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
$date_format = 'F j, Y';
$entries_schema = acf_blocks_get_field_schema( 'changelog_entries', $block );
foreach ( $entries_schema['sub_fields'] ?? array() as $field_schema ) {
    if ( 'changelog_date' === ( $field_schema['name'] ?? '' ) && ! empty( $field_schema['return_format'] ) ) {
        $date_format = $field_schema['return_format'];
        break;
    }
}
?>
<div<?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
    <?php
    $data = $block['data'] ?? [];
    foreach ($entries as $entry_index => $entry) :
        $version = esc_html($entry['changelog_version'] ?? '');
        $date_value = (string) ( $entry['changelog_date'] ?? '' );
        if ( preg_match( '/^[0-9]{8}$/D', $date_value ) ) {
            // ACF stores date pickers as Ymd. Preserve date-only values without
            // shifting them across calendar days through the site's timezone.
            $date_only = DateTimeImmutable::createFromFormat( '!Ymd', $date_value, new DateTimeZone( 'UTC' ) );
            if ( $date_only && $date_only->format( 'Ymd' ) === $date_value ) {
                $date_value = function_exists( 'wp_date' )
                    ? wp_date( $date_format, $date_only->getTimestamp(), new DateTimeZone( 'UTC' ) )
                    : $date_only->format( $date_format );
            }
        }
        $date = esc_html( $date_value );
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
