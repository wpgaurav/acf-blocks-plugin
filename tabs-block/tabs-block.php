<?php
/**
 * Tabs Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

// Global array for footer scripts.
global $tabs_unique_ids;
if ( ! isset( $tabs_unique_ids ) ) {
    $tabs_unique_ids = array();
    if ( ! function_exists( 'output_tabs_footer_scripts' ) ) {
        function output_tabs_footer_scripts() {
            global $tabs_unique_ids;
            if ( ! empty( $tabs_unique_ids ) ) {
                foreach ( $tabs_unique_ids as $id ) {
                    echo '<script>MD.tabs("' . esc_js( $id ) . '");</script>' . "\n";
                }
            }
        }
    }
    add_action( 'wp_footer', 'output_tabs_footer_scripts', 999 );
}

$unique_id = 'tabs_' . uniqid();
$tabs_unique_ids[] = $unique_id;

$tabs_items = get_field( 'acf_tabs_items' );
$tab_style  = get_field( 'acf_tabs_style' );

$custom_class = get_field( 'acf_tabs_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_tabs_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$style_class = $tab_style ? ' tabs-' . esc_attr( $tab_style ) : ' tabs-default';
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="tabs-block<?php echo $style_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $tabs_items && is_array( $tabs_items ) && count( $tabs_items ) > 0 ) : ?>
        <!-- Tab Navigation -->
        <div class="tabs-nav" role="tablist">
            <?php foreach ( $tabs_items as $index => $tab ) : ?>
                <?php
                $is_active = ( $index === 0 );
                $tab_id    = esc_attr( $unique_id . '_tab_' . $index );
                $panel_id  = esc_attr( $unique_id . '_panel_' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <button class="tab-button<?php echo $active_class; ?>"
                        id="<?php echo $tab_id; ?>"
                        role="tab"
                        aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo $panel_id; ?>"
                        data-tab="<?php echo esc_attr( $index ); ?>">
                    <?php if ( $tab['acf_tab_icon'] ) : ?>
                        <span class="tab-icon">
                            <?php
                            $icon_markup = function_exists( 'md_get_icon_markup' )
                                ? md_get_icon_markup( $tab['acf_tab_icon'] )
                                : esc_html( $tab['acf_tab_icon'] );
                            echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </span>
                    <?php endif; ?>
                    <span class="tab-title"><?php echo esc_html( $tab['acf_tab_title'] ); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Tab Panels -->
        <div class="tabs-content">
            <?php foreach ( $tabs_items as $index => $tab ) : ?>
                <?php
                $is_active = ( $index === 0 );
                $tab_id    = esc_attr( $unique_id . '_tab_' . $index );
                $panel_id  = esc_attr( $unique_id . '_panel_' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <div class="tab-panel<?php echo $active_class; ?>"
                     id="<?php echo $panel_id; ?>"
                     role="tabpanel"
                     aria-labelledby="<?php echo $tab_id; ?>"
                     <?php echo $is_active ? '' : 'hidden'; ?>>
                    <?php echo wpautop( do_shortcode( $tab['acf_tab_content'] ) ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php if ( $is_preview ) : ?>
            <p><em>No tabs added. Please add some tab items.</em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
