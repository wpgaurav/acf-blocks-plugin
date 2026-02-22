<?php
/**
 * Compare Block Template.
 *
 * Side-by-side product/feature comparison with styled columns,
 * title badges, feature lists, and an optional CTA button.
 *
 * @var array  $block      The block settings and attributes.
 * @var string $content    The block inner HTML.
 * @var bool   $is_preview True during AJAX preview.
 * @var int    $post_id    The post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$columns     = acf_blocks_get_field( 'comp_columns', $block ) ?: 3;
$cta_text    = acf_blocks_get_field( 'comp_cta_text', $block );
$cta_url     = acf_blocks_get_field( 'comp_cta_url', $block );
$cta_rel     = acf_blocks_get_field( 'comp_cta_url_rel_tag', $block );
$cta_bg      = acf_blocks_get_field( 'comp_cta_bg', $block );

$comp_data = acf_blocks_get_repeater('comp_columns_data', [
    'comp_title',
    'comp_title_bg',
    'comp_title_color',
    'comp_text',
    'comp_list_content',
    'comp_column_style',
], $block);

// Also try old field name for backward compat
if ( empty( $comp_data ) ) {
    $comp_data = acf_blocks_get_repeater('comp_columns_data', [
        'comp_title',
        'comp_title_bg',
        'comp_title_color',
        'comp_text',
        'comp_list_class',
        'comp_column_style',
    ], $block);
}

$block_id    = 'cmp-' . ( $block['id'] ?? uniqid() );
$className   = $block['className'] ?? '';
$anchor      = $block['anchor'] ?? '';
$anchor_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

$wrapper_classes = [
    'acf-compare',
    'acf-compare--cols-' . intval( $columns ),
];
if ( ! empty( $className ) ) {
    $wrapper_classes[] = $className;
}
?>

<div<?php echo $anchor_attr; ?> class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-acf-block="compare" data-cmp-id="<?php echo esc_attr( $block_id ); ?>">
    <?php if ( ! empty( $comp_data ) ) : ?>
    <div class="acf-compare__grid">
        <?php foreach ( $comp_data as $col_index => $col ) :
            $title        = $col['comp_title'] ?? '';
            $title_bg     = $col['comp_title_bg'] ?? '';
            $title_color  = $col['comp_title_color'] ?? '';
            $text         = $col['comp_text'] ?? '';
            $list_content = $col['comp_list_content'] ?? '';
            $col_style    = $col['comp_column_style'] ?? '';

            // Backward compat: if list_content is empty, try nested repeater
            if ( empty( $list_content ) ) {
                $list_items = acf_blocks_get_nested_repeater(
                    'comp_columns_data_' . $col_index . '_comp_repeater_list',
                    [ 'comp_list_item' ],
                    $block['data'] ?? array()
                );
                if ( ! empty( $list_items ) ) {
                    $list_content = '<ul>';
                    foreach ( $list_items as $item ) {
                        $list_content .= '<li>' . esc_html( $item['comp_list_item'] ?? '' ) . '</li>';
                    }
                    $list_content .= '</ul>';
                }
            }
        ?>
            <div class="acf-compare__column"<?php echo $col_style ? ' style="' . esc_attr( $col_style ) . '"' : ''; ?>>
                <?php if ( $title ) : ?>
                    <div class="acf-compare__title"<?php
                        $title_styles = '';
                        if ( $title_bg ) $title_styles .= 'background:' . esc_attr( $title_bg ) . ';';
                        if ( $title_color ) $title_styles .= 'color:' . esc_attr( $title_color ) . ';';
                        echo $title_styles ? ' style="' . $title_styles . '"' : '';
                    ?>><?php echo esc_html( $title ); ?></div>
                <?php endif; ?>

                <?php if ( $text ) : ?>
                    <div class="acf-compare__subtitle"><?php echo esc_html( $text ); ?></div>
                <?php endif; ?>

                <?php if ( $list_content ) : ?>
                    <div class="acf-compare__features">
                        <?php echo wp_kses_post( $list_content ); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ( $cta_text && $cta_url ) : ?>
        <div class="acf-compare__cta">
            <a href="<?php echo esc_url( $cta_url ); ?>" class="acf-compare__btn"<?php
                echo $cta_rel ? ' rel="' . esc_attr( $cta_rel ) . '"' : '';
                echo $cta_bg ? ' style="background-color:' . esc_attr( $cta_bg ) . ';border-color:' . esc_attr( $cta_bg ) . ';"' : '';
            ?>><?php echo esc_html( $cta_text ); ?></a>
        </div>
    <?php endif; ?>
</div>