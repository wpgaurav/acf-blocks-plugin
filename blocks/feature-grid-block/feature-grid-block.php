<?php
/**
 * Feature Grid Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$heading         = get_field( 'acf_feature_grid_heading' );
$subheading      = get_field( 'acf_feature_grid_subheading' );
$features        = get_field( 'acf_feature_grid_items' );
$columns         = get_field( 'acf_feature_grid_columns' );
$layout_style    = get_field( 'acf_feature_grid_layout' );

$custom_class = get_field( 'acf_feature_grid_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_feature_grid_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$columns_class = $columns ? ' columns-' . esc_attr( $columns ) : ' columns-3';
$layout_class = $layout_style ? ' layout-' . esc_attr( $layout_style ) : ' layout-default';
?>

<div class="acf-feature-grid-block<?php echo $columns_class . $layout_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $heading || $subheading ) : ?>
        <div class="acf-feature-grid-header">
            <?php if ( $heading ) : ?>
                <h2 class="acf-feature-grid-heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>

            <?php if ( $subheading ) : ?>
                <p class="acf-feature-grid-subheading"><?php echo esc_html( $subheading ); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ( $features && is_array( $features ) && count( $features ) > 0 ) : ?>
        <div class="acf-feature-grid-items">
            <?php foreach ( $features as $feature ) : ?>
                <div class="acf-feature-item">
                    <?php if ( $feature['acf_feature_icon'] || $feature['acf_feature_image'] ) : ?>
                        <div class="acf-feature-icon-wrapper">
                            <?php if ( $feature['acf_feature_image'] ) : ?>
                                <div class="acf-feature-image">
                                    <img src="<?php echo esc_url( $feature['acf_feature_image']['url'] ); ?>"
                                         alt="<?php echo esc_attr( $feature['acf_feature_image']['alt'] ); ?>" />
                                </div>
                            <?php elseif ( $feature['acf_feature_icon'] ) : ?>
                                <div class="acf-feature-icon">
                                    <?php
                                    $icon_markup = function_exists( 'md_get_icon_markup' )
                                        ? md_get_icon_markup( $feature['acf_feature_icon'] )
                                        : esc_html( $feature['acf_feature_icon'] );
                                    echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="acf-feature-content">
                        <?php if ( $feature['acf_feature_title'] ) : ?>
                            <h3 class="acf-feature-title"><?php echo esc_html( $feature['acf_feature_title'] ); ?></h3>
                        <?php endif; ?>

                        <?php if ( $feature['acf_feature_description'] ) : ?>
                            <div class="acf-feature-description">
                                <?php echo wpautop( esc_html( $feature['acf_feature_description'] ) ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $feature['acf_feature_link'] ) : ?>
                            <a href="<?php echo esc_url( $feature['acf_feature_link']['url'] ); ?>"
                               class="acf-feature-link"
                               <?php echo $feature['acf_feature_link']['target'] ? 'target="' . esc_attr( $feature['acf_feature_link']['target'] ) . '"' : ''; ?>>
                                <?php echo esc_html( $feature['acf_feature_link']['title'] ? $feature['acf_feature_link']['title'] : 'Learn More' ); ?>
                                <span class="acf-link-arrow">→</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php if ( $is_preview ) : ?>
            <p><em>No features added. Please add some feature items.</em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
