<?php
/**
 * Tabs Block Template.
 *
 * Uses minimal inline JavaScript for tab switching.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$tabs_items = get_field( 'acf_tabs_items' );
$tab_style  = get_field( 'acf_tabs_style' );

$custom_class = get_field( 'acf_tabs_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = get_field( 'acf_tabs_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$style_class = $tab_style ? ' tabs-' . esc_attr( $tab_style ) : ' tabs-default';
$unique_id   = 'tabs-' . ( $block['id'] ?? uniqid() );
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="tabs-block<?php echo $style_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $tabs_items && is_array( $tabs_items ) && count( $tabs_items ) > 0 ) : ?>
        <div class="tabs-nav" role="tablist">
            <?php foreach ( $tabs_items as $index => $tab ) :
                $is_active    = ( 0 === $index );
                $tab_id       = esc_attr( $unique_id . '-tab-' . $index );
                $panel_id     = esc_attr( $unique_id . '-panel-' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <button class="tab-button<?php echo $active_class; ?>"
                        id="<?php echo $tab_id; ?>"
                        type="button"
                        role="tab"
                        aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo $panel_id; ?>"
                        data-tab-index="<?php echo esc_attr( $index ); ?>"
                        onclick="acfBlocksSwitchTab(this, '<?php echo esc_js( $unique_id ); ?>')">
                    <?php if ( ! empty( $tab['acf_tab_icon'] ) ) : ?>
                        <span class="tab-icon">
                            <?php
                            $icon_markup = function_exists( 'acf_blocks_get_icon_markup' )
                                ? acf_blocks_get_icon_markup( $tab['acf_tab_icon'] )
                                : ( function_exists( 'md_get_icon_markup' )
                                    ? md_get_icon_markup( $tab['acf_tab_icon'] )
                                    : esc_html( $tab['acf_tab_icon'] ) );
                            echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </span>
                    <?php endif; ?>
                    <span class="tab-title"><?php echo esc_html( $tab['acf_tab_title'] ); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="tabs-content">
            <?php foreach ( $tabs_items as $index => $tab ) :
                $is_active    = ( 0 === $index );
                $tab_id       = esc_attr( $unique_id . '-tab-' . $index );
                $panel_id     = esc_attr( $unique_id . '-panel-' . $index );
                $active_class = $is_active ? ' active' : '';
                ?>
                <div class="tab-panel<?php echo $active_class; ?>"
                     id="<?php echo $panel_id; ?>"
                     role="tabpanel"
                     aria-labelledby="<?php echo $tab_id; ?>"
                     <?php echo $is_active ? '' : 'hidden'; ?>>
                    <?php echo wpautop( do_shortcode( wp_kses_post( $tab['acf_tab_content'] ) ) ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php if ( $is_preview ) : ?>
            <p><em><?php esc_html_e( 'No tabs added. Please add some tab items.', 'acf-blocks' ); ?></em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
// Output the tab switching script once per page
if ( ! defined( 'ACF_BLOCKS_TABS_SCRIPT_LOADED' ) ) :
    define( 'ACF_BLOCKS_TABS_SCRIPT_LOADED', true );
    ?>
    <script>
    function acfBlocksSwitchTab(btn, containerId) {
        var container = document.getElementById(containerId);
        if (!container) return;

        var tabIndex = btn.getAttribute('data-tab-index');

        // Update buttons
        var buttons = container.querySelectorAll('.tab-button');
        buttons.forEach(function(b) {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        // Update panels
        var panels = container.querySelectorAll('.tab-panel');
        panels.forEach(function(p, i) {
            if (i == tabIndex) {
                p.classList.add('active');
                p.removeAttribute('hidden');
            } else {
                p.classList.remove('active');
                p.setAttribute('hidden', '');
            }
        });
    }
    </script>
<?php endif; ?>
