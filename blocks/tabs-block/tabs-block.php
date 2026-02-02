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

$tabs_items = acf_blocks_get_repeater( 'acf_tabs_items', [ 'acf_tab_title', 'acf_tab_icon', 'acf_tab_content' ], $block );
$tab_style  = acf_blocks_get_field( 'acf_tabs_style', $block );

$custom_class = acf_blocks_get_field( 'acf_tabs_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = acf_blocks_get_field( 'acf_tabs_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$style_class = $tab_style ? ' acf-tabs-' . esc_attr( $tab_style ) : ' acf-tabs-default';
$unique_id   = 'acf-tabs-' . ( $block['id'] ?? uniqid() );

// Detect style variation from className
$className = isset( $block['className'] ) ? $block['className'] : '';
$style_variation = '';
if ( strpos( $className, 'is-style-card' ) !== false ) {
	$style_variation = 'card';
} elseif ( strpos( $className, 'is-style-dark' ) !== false ) {
	$style_variation = 'dark';
} elseif ( strpos( $className, 'is-style-minimal' ) !== false ) {
	$style_variation = 'minimal';
} elseif ( strpos( $className, 'is-style-vertical' ) !== false ) {
	$style_variation = 'vertical';
}
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-tabs-block<?php echo $style_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    // Output inline styles for style variations
    if ( $style_variation === 'card' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-tabs-nav {
            border-bottom: none;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-tab-button {
            background: #f5f5f5;
            border-radius: 8px;
            border-bottom: none;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-tab-button.active {
            background: #007bff;
            color: #fff;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
        ?><?php
    elseif ( $style_variation === 'dark' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 1.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-tabs-nav {
            border-bottom-color: #3d3d5c;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-tab-button {
            color: #a0a0a0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-tab-button.active {
            color: #fff;
            border-bottom-color: #ffd700;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-tabs-content,
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-tab-panel {
            color: #e0e0e0;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
        ?><?php
    elseif ( $style_variation === 'minimal' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-tabs-nav {
            border-bottom: none;
            gap: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-tab-button {
            padding: 0.5rem 0;
            font-weight: 400;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-tab-button.active {
            font-weight: 600;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
        ?><?php
    elseif ( $style_variation === 'vertical' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical {
            display: flex;
            gap: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tabs-nav {
            flex-direction: column;
            border-bottom: none;
            border-right: 1px solid currentColor;
            padding-right: 1rem;
            margin-bottom: 0;
            min-width: 150px;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tab-button {
            border-bottom: none;
            border-right: 2px solid transparent;
            margin-right: -1px;
            text-align: left;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tab-button.active {
            border-right-color: currentColor;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tabs-content {
            flex: 1;
            padding: 0;
        }
        @media (max-width: 480px) {
            #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical {
                flex-direction: column;
            }
            #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tabs-nav {
                border-right: none;
                border-bottom: 1px solid currentColor;
                padding-right: 0;
                padding-bottom: 0.5rem;
                min-width: auto;
            }
            #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tab-button {
                border-right: none;
                border-bottom: 2px solid transparent;
            }
            #<?php echo esc_attr( $unique_id ); ?>.is-style-vertical .acf-tab-button.active {
                border-bottom-color: currentColor;
            }
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
        ?><?php
    endif;
    ?>
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
                        data-tab-index="<?php echo esc_attr( $index ); ?>"
                        onclick="acfBlocksSwitchTab(this, '<?php echo esc_js( $unique_id ); ?>')">
                    <?php if ( ! empty( $tab['acf_tab_icon'] ) ) : ?>
                        <span class="acf-tab-icon">
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
        var buttons = container.querySelectorAll('.acf-tab-button');
        buttons.forEach(function(b) {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        // Update panels
        var panels = container.querySelectorAll('.acf-tab-panel');
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
