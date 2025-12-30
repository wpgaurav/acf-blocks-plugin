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
$title         = get_field( 'checklist_title' ) ?: '';
$items         = get_field( 'checklist_items' ) ?: array();
$interactive   = get_field( 'checklist_interactive' );
$show_progress = get_field( 'checklist_show_progress' );
$strikethrough = get_field( 'checklist_strikethrough' );
$accent_color  = get_field( 'checklist_accent_color' ) ?: '#16a34a';
$bg_color      = get_field( 'checklist_bg_color' ) ?: '#f9fafb';

// Build wrapper classes
$wrapper_classes = array( 'acf-checklist' );

if ( $align ) {
    $wrapper_classes[] = 'align' . $align;
}

if ( $className ) {
    $wrapper_classes[] = $className;
}

if ( $interactive ) {
    $wrapper_classes[] = 'acf-checklist--interactive';
}

if ( $strikethrough ) {
    $wrapper_classes[] = 'acf-checklist--strikethrough';
}

// Detect style variation
$is_card_style = $className && strpos( $className, 'is-style-card' ) !== false;
$is_minimal_style = $className && strpos( $className, 'is-style-minimal' ) !== false;

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

// Generate unique ID
$block_id = 'checklist-' . uniqid();

$anchor_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
?>

<div <?php echo $anchor_attr; ?> class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-checklist-id="<?php echo esc_attr( $block_id ); ?>">
    <?php
    ob_start();
    ?>
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] {
            --checklist-accent: <?php echo esc_attr( $accent_color ); ?>;
            --checklist-bg: <?php echo esc_attr( $bg_color ); ?>;
        }
        <?php if ( $is_card_style ) : ?>
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] { background-color: var(--checklist-bg); padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__item { background-color: #fff; padding: 0.875rem 1rem; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 0.5rem; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__item:last-child { margin-bottom: 0; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__item--checked { background-color: #f0fdf4; border-color: #bbf7d0; }
        @media (max-width: 600px) { [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] { padding: 1rem; } [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__item { padding: 0.75rem; } }
        <?php endif; ?>
        <?php if ( $is_minimal_style ) : ?>
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] { padding: 0; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__item { padding: 0.5rem 0; border-bottom: none; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__progress-bar { height: 4px; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__checkmark,
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__icon--empty { width: 18px; height: 18px; border-radius: 50%; }
        [data-checklist-id="<?php echo esc_attr( $block_id ); ?>"] .acf-checklist__checkmark svg { width: 12px; height: 12px; }
        <?php endif; ?>
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>

    <?php if ( ! empty( $title ) ) : ?>
        <h3 class="acf-checklist__title"><?php echo esc_html( $title ); ?></h3>
    <?php endif; ?>

    <?php if ( $show_progress && $total_items > 0 ) : ?>
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
                    <?php if ( $interactive ) : ?>
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

<?php if ( $interactive && ! $is_preview ) : ?>
<script>
(function() {
    var block = document.querySelector('[data-checklist-id="<?php echo esc_js( $block_id ); ?>"]');
    if (!block) return;

    var storageKey = 'acf-checklist-<?php echo esc_js( $block_id ); ?>';
    var checkboxes = block.querySelectorAll('.acf-checklist__checkbox');
    var progressFill = block.querySelector('.acf-checklist__progress-fill');
    var progressText = block.querySelector('.acf-checklist__progress-text');

    // Load saved state
    var saved = localStorage.getItem(storageKey);
    if (saved) {
        try {
            var state = JSON.parse(saved);
            checkboxes.forEach(function(cb, i) {
                if (typeof state[i] !== 'undefined') {
                    cb.checked = state[i];
                    updateItemState(cb);
                }
            });
            updateProgress();
        } catch(e) {}
    }

    // Add change listeners
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            updateItemState(cb);
            saveState();
            updateProgress();
        });
    });

    function updateItemState(checkbox) {
        var item = checkbox.closest('.acf-checklist__item');
        if (checkbox.checked) {
            item.classList.add('acf-checklist__item--checked');
        } else {
            item.classList.remove('acf-checklist__item--checked');
        }
    }

    function saveState() {
        var state = [];
        checkboxes.forEach(function(cb) {
            state.push(cb.checked);
        });
        localStorage.setItem(storageKey, JSON.stringify(state));
    }

    function updateProgress() {
        if (!progressFill) return;
        var total = checkboxes.length;
        var checked = 0;
        checkboxes.forEach(function(cb) {
            if (cb.checked) checked++;
        });
        var percent = total > 0 ? Math.round((checked / total) * 100) : 0;
        progressFill.style.width = percent + '%';
        if (progressText) {
            progressText.textContent = checked + '/' + total + ' completed';
        }
    }
})();
</script>
<?php endif; ?>
