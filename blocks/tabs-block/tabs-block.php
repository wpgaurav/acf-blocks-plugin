<?php
/**
 * Tabs Block Template.
 *
 * Behaviour lives in tabs.js, registered as the block's viewScript, so the
 * markup carries no inline handlers.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$tabs_items = acf_blocks_get_repeater( 'acf_tabs_items', [ 'acf_tab_title', 'acf_tab_icon', 'acf_tab_content' ], $block );
$tab_style  = acf_blocks_get_field( 'acf_tabs_style', $block );

$custom_class = acf_blocks_get_field( 'acf_tabs_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = acf_blocks_get_field( 'acf_tabs_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$style_class = $tab_style ? ' acf-tabs-' . esc_attr( $tab_style ) : ' acf-tabs-default';
$unique_id   = 'acf-tabs-' . ( $block['id'] ?? wp_unique_id() );

?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-tabs-block<?php echo $style_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $tabs_items && is_array( $tabs_items ) && count( $tabs_items ) > 0 ) : ?>
        <div class="acf-tabs-nav" role="tablist">
            <?php foreach ( $tabs_items as $index => $tab ) :
                $is_active    = ( 0 === $index );
                $tab_id       = esc_attr( $unique_id . '-tab-' . $index );
                $panel_id     = esc_attr( $unique_id . '-panel-' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <button class="acf-tab-button<?php echo $active_class; ?>"
                        id="<?php echo $tab_id; ?>"
                        type="button"
                        role="tab"
                        aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo $panel_id; ?>"
                        tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
                        data-tab-index="<?php echo esc_attr( $index ); ?>">
                    <?php if ( ! empty( $tab['acf_tab_icon'] ) ) : ?>
                        <span class="acf-tab-icon">
                            <?php
                            $icon_markup = acf_blocks_get_icon_markup( $tab['acf_tab_icon'] );
                            echo wp_kses_post( $icon_markup );
                            ?>
                        </span>
                    <?php endif; ?>
                    <span class="acf-tab-title"><?php echo esc_html( $tab['acf_tab_title'] ); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="acf-tabs-content">
            <?php foreach ( $tabs_items as $index => $tab ) :
                $is_active    = ( 0 === $index );
                $tab_id       = esc_attr( $unique_id . '-tab-' . $index );
                $panel_id     = esc_attr( $unique_id . '-panel-' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <div class="acf-tab-panel<?php echo $active_class; ?>"
                     id="<?php echo $panel_id; ?>"
                     role="tabpanel"
                     aria-labelledby="<?php echo $tab_id; ?>"
                     <?php echo $is_active ? '' : 'hidden'; ?>>
                    <?php echo wp_kses_post( wpautop( do_shortcode( $tab['acf_tab_content'] ) ) ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php if ( $is_preview ) : ?>
            <p><em><?php esc_html_e( 'No tabs added. Please add some tab items.', 'acf-blocks' ); ?></em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
