<?php
/**
 * URL Preview Card Block Template
 *
 * Displays a product-like card with fetched Open Graph data from a URL.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty for ACF blocks).
 * @param bool $is_preview True during AJAX preview.
 * @param int|string $post_id The post ID this block is saved to.
 */

// Get field values
$source_url = acf_blocks_get_field( 'source_url', $block );
$title = acf_blocks_get_field( 'preview_title', $block );
$description = acf_blocks_get_field( 'preview_description', $block );
$image_source = acf_blocks_get_field( 'image_source', $block ) ?: 'external';
$external_image = acf_blocks_get_field( 'external_image_url', $block );
$local_image = acf_blocks_get_field( 'local_image', $block );
$image_alt = acf_blocks_get_field( 'image_alt', $block );
$custom_fields = acf_blocks_get_repeater( 'custom_fields', [ 'field_label', 'field_value', 'field_icon' ], $block );
$show_button = acf_blocks_get_field( 'show_button', $block );
$button_text = acf_blocks_get_field( 'button_text', $block ) ?: __( 'View Details', 'acf-blocks' );
$button_url = acf_blocks_get_field( 'button_url', $block ) ?: $source_url;
$button_new_tab = acf_blocks_get_field( 'button_new_tab', $block );
$button_nofollow = acf_blocks_get_field( 'button_nofollow', $block );
$show_secondary_button = acf_blocks_get_field( 'show_secondary_button', $block );
$secondary_button_text = acf_blocks_get_field( 'secondary_button_text', $block );
$secondary_button_url = acf_blocks_get_field( 'secondary_button_url', $block );
$card_layout = acf_blocks_get_field( 'card_layout', $block ) ?: 'vertical';
$card_style = acf_blocks_get_field( 'card_style', $block ) ?: 'default';
$image_position = acf_blocks_get_field( 'image_position', $block ) ?: 'left';
$local_image_size = acf_blocks_get_field( 'local_image_size', $block ) ?: 'medium_large';
$custom_class = acf_blocks_get_field( 'custom_class', $block );
$custom_inline = acf_blocks_get_field( 'custom_inline', $block );

// Determine image URL
$image_url = '';
if ( 'local' === $image_source && $local_image ) {
    // For horizontal layout, default to thumbnail size for minimal display
    $size_to_use = ( 'horizontal' === $card_layout ) ? 'thumbnail' : $local_image_size;

    if ( 'full' === $size_to_use ) {
        $image_url = $local_image['url'];
    } elseif ( isset( $local_image['sizes'][ $size_to_use ] ) ) {
        $image_url = $local_image['sizes'][ $size_to_use ];
    } else {
        // Fallback to full URL if size not available
        $image_url = $local_image['url'];
    }

    if ( empty( $image_alt ) && ! empty( $local_image['alt'] ) ) {
        $image_alt = $local_image['alt'];
    }
} elseif ( 'external' === $image_source && $external_image ) {
    // For external images, always use the full URL
    $image_url = $external_image;
}

// Build block classes
$block_id = $block['id'] ?? wp_unique_id( 'url-preview-' );
$classes = array( 'acf-url-preview' );
$classes[] = 'acf-url-preview--' . $card_layout;
if ( 'horizontal' === $card_layout ) {
    $classes[] = 'acf-url-preview--image-' . $image_position;
}
if ( 'default' !== $card_style ) {
    $classes[] = 'acf-url-preview--' . $card_style;
}
if ( $custom_class ) {
    $classes[] = esc_attr( $custom_class );
}
$class_string = implode( ' ', $classes );

// Build inline styles for card style variations
$style_variations = array(
    'compact' => '--acf-url-preview-radius: 4px; --acf-url-preview-shadow: 0 1px 3px rgba(0,0,0,0.08);',
    'minimal' => '--acf-url-preview-border: transparent; --acf-url-preview-shadow: none; --acf-url-preview-bg: transparent;',
    'featured' => '--acf-url-preview-radius: 12px; --acf-url-preview-shadow: 0 8px 24px rgba(0,0,0,0.12); --acf-url-preview-border: transparent;',
    'dark' => '--acf-url-preview-bg: #1d2327; --acf-url-preview-text: #ffffff; --acf-url-preview-text-secondary: #c3c4c7; --acf-url-preview-border: #3c434a;',
);

$inline_styles = '';
if ( isset( $style_variations[ $card_style ] ) ) {
    $inline_styles .= $style_variations[ $card_style ];
}
if ( $custom_inline ) {
    $inline_styles .= ' ' . $custom_inline;
}
$inline_styles = trim( $inline_styles );

// Icon SVGs - minimal inline for performance
$icons = array(
    'price' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
    'calendar' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    'star' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    'check' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
    'info' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    'clock' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    'percent' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>',
    'gift' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
    'truck' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
);

// Show placeholder in editor if no content
if ( $is_preview && empty( $title ) && empty( $image_url ) ) : ?>
    <div class="acf-url-preview acf-url-preview--placeholder">
        <div class="acf-url-preview__placeholder-content">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            <p><?php esc_html_e( 'Enter a URL and click "Fetch Data" to populate this card.', 'acf-blocks' ); ?></p>
        </div>
    </div>
    <?php
    return;
endif;

// Check if we have enough content to display
if ( empty( $title ) && empty( $image_url ) && empty( $description ) ) {
    return;
}
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_string ); ?>"<?php echo $inline_styles ? ' style="' . esc_attr( $inline_styles ) . '"' : ''; ?>>

    <?php if ( $image_url ) : ?>
    <div class="acf-url-preview__image">
        <img
            src="<?php echo esc_url( $image_url ); ?>"
            alt="<?php echo esc_attr( $image_alt ?: $title ); ?>"
            loading="lazy"
            decoding="async"
        />
    </div>
    <?php endif; ?>

    <div class="acf-url-preview__content">
        <?php if ( $title ) : ?>
        <h3 class="acf-url-preview__title"><?php echo esc_html( $title ); ?></h3>
        <?php endif; ?>

        <?php if ( $description ) : ?>
        <p class="acf-url-preview__description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $custom_fields ) && is_array( $custom_fields ) ) : ?>
        <ul class="acf-url-preview__fields">
            <?php foreach ( $custom_fields as $field ) :
                if ( empty( $field['field_label'] ) && empty( $field['field_value'] ) ) continue;
                $icon_key = $field['field_icon'] ?? 'none';
                $icon_svg = ( 'none' !== $icon_key && isset( $icons[ $icon_key ] ) ) ? $icons[ $icon_key ] : '';
            ?>
            <li class="acf-url-preview__field">
                <?php if ( $icon_svg ) : ?>
                <span class="acf-url-preview__field-icon"><?php echo $icon_svg; ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $field['field_label'] ) ) : ?>
                <span class="acf-url-preview__field-label"><?php echo esc_html( $field['field_label'] ); ?>:</span>
                <?php endif; ?>
                <span class="acf-url-preview__field-value"><?php echo esc_html( $field['field_value'] ); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php
        $has_primary = $show_button && $button_url;
        $has_secondary = $show_secondary_button && $secondary_button_text && $secondary_button_url;

        if ( $has_primary || $has_secondary ) :
            $rel_attrs = array();
            if ( $button_new_tab ) {
                $rel_attrs[] = 'noopener';
                $rel_attrs[] = 'noreferrer';
            }
            if ( $button_nofollow ) {
                $rel_attrs[] = 'nofollow';
            }
            $rel_string = ! empty( $rel_attrs ) ? implode( ' ', $rel_attrs ) : '';
        ?>
        <div class="acf-url-preview__buttons">
            <?php if ( $has_primary ) : ?>
            <a
                href="<?php echo esc_url( $button_url ); ?>"
                class="acf-url-preview__button"
                <?php echo $button_new_tab ? 'target="_blank"' : ''; ?>
                <?php echo $rel_string ? 'rel="' . esc_attr( $rel_string ) . '"' : ''; ?>
            >
                <?php echo esc_html( $button_text ); ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <?php endif; ?>

            <?php if ( $has_secondary ) : ?>
            <a
                href="<?php echo esc_url( $secondary_button_url ); ?>"
                class="acf-url-preview__button acf-url-preview__button--secondary"
                <?php echo $button_new_tab ? 'target="_blank"' : ''; ?>
                <?php echo $rel_string ? 'rel="' . esc_attr( $rel_string ) . '"' : ''; ?>
            >
                <?php echo esc_html( $secondary_button_text ); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
