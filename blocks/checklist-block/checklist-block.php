<?php
/**
 * Checklist Block Template
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering content against.
 * @param array $context The context provided to the block by the post or its parent block.
 */

defined( 'ABSPATH' ) || exit;

// Block attributes
$align     = $block['align'] ?? '';
$anchor    = $block['anchor'] ?? '';
$className = $block['className'] ?? '';

// Get block values
$title         = acf_blocks_get_field( 'checklist_title', $block ) ?: '';
$items         = acf_blocks_get_repeater( 'checklist_items', [ 'checklist_item_text', 'checklist_item_checked' => 'bool' ], $block );
$interactive   = acf_blocks_get_field( 'checklist_interactive', $block );
$show_progress = acf_blocks_get_field( 'checklist_show_progress', $block );
$strikethrough = acf_blocks_get_field( 'checklist_strikethrough', $block );
$checklist_style = acf_blocks_checklist_style(
    acf_blocks_get_field( 'checklist_accent_color', $block ),
    acf_blocks_get_field( 'checklist_bg_color', $block )
);

// Build wrapper classes
$wrapper_classes = array( 'acf-checklist' );

if ( $align ) {
    $wrapper_classes[] = 'align' . $align;
}

if ( $className ) {
    $wrapper_classes[] = $className;
}

if ( acf_blocks_is_on( $interactive ) ) {
    $wrapper_classes[] = 'acf-checklist--interactive';
}

if ( acf_blocks_is_on( $strikethrough ) ) {
    $wrapper_classes[] = 'acf-checklist--strikethrough';
}

// Calculate progress
$total_items   = count( $items );
$checked_items = 0;
if ( ! empty( $items ) ) {
    foreach ( $items as $item ) {
        if ( ! empty( $item['checklist_item_checked'] ) ) {
            $checked_items++;
        }
    }
}
$progress_percent = $total_items > 0 ? round( ( $checked_items / $total_items ) * 100 ) : 0;

// DOM IDs stay unique per render; persistence uses stored block identity, with
// a content fingerprint for legacy blocks that have no persisted ID.
$block_id = 'checklist-' . wp_unique_id();
$persistence_identity = $block['id'] ?? $anchor;
if ( ! $persistence_identity ) {
    $persistence_identity = wp_json_encode( array( $title, $items ) );
}
// Post context also separates plain-permalink pages that share the same path.
$persistence_key = hash( 'sha256', wp_json_encode( array( $post_id ?? 0, $persistence_identity ) ) );

$anchor_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>

<div <?php echo $anchor_attr; ?> class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-checklist-id="<?php echo esc_attr( $block_id ); ?>" data-checklist-key="<?php echo esc_attr( $persistence_key ); ?>" style="<?php echo esc_attr( $checklist_style ); ?>">

    <?php if ( ! empty( $title ) ) : ?>
        <h3 class="acf-checklist__title"><?php echo esc_html( $title ); ?></h3>
    <?php endif; ?>

    <?php if ( acf_blocks_is_on( $show_progress ) && $total_items > 0 ) : ?>
        <div class="acf-checklist__progress">
            <div class="acf-checklist__progress-bar">
                <div class="acf-checklist__progress-fill" style="width: <?php echo esc_attr( $progress_percent ); ?>%;"></div>
            </div>
            <span class="acf-checklist__progress-text"><?php echo esc_html( $checked_items ); ?>/<?php echo esc_html( $total_items ); ?> completed</span>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $items ) ) : ?>
        <ul class="acf-checklist__list">
            <?php foreach ( $items as $index => $item ) :
                $is_checked = ! empty( $item['checklist_item_checked'] );
                $item_text  = $item['checklist_item_text'] ?? '';
                $item_id    = $block_id . '-item-' . $index;
                ?>
                <li class="acf-checklist__item<?php echo $is_checked ? ' acf-checklist__item--checked' : ''; ?>">
                    <?php if ( acf_blocks_is_on( $interactive ) ) : ?>
                        <input type="checkbox" id="<?php echo esc_attr( $item_id ); ?>" class="acf-checklist__checkbox" <?php checked( $is_checked ); ?>>
                        <label for="<?php echo esc_attr( $item_id ); ?>" class="acf-checklist__label">
                            <span class="acf-checklist__checkmark">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="acf-checklist__text"><?php echo esc_html( $item_text ); ?></span>
                        </label>
                    <?php else : ?>
                        <span class="acf-checklist__marker">
                            <?php if ( $is_checked ) : ?>
                                <svg class="acf-checklist__icon acf-checklist__icon--check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            <?php else : ?>
                                <span class="acf-checklist__icon acf-checklist__icon--empty"></span>
                            <?php endif; ?>
                        </span>
                        <span class="acf-checklist__text"><?php echo esc_html( $item_text ); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p class="acf-checklist__empty"><?php esc_html_e( 'No checklist items added.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</div>
