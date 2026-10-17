<?php
/**
 * Coupon Code Block Template.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id    The post ID this block is saved to.
 */

$offer_details          = acf_blocks_get_field( 'cb_offer_details', $block );
$coupon_code            = acf_blocks_get_field( 'cb_code', $block );
$copy_coupon_text       = acf_blocks_get_field( 'cb_copy_text', $block ) ?: __( 'Copy Coupon', 'acf-blocks' );
$activate_discount_text = acf_blocks_get_field( 'cb_activate_text', $block ) ?: __( 'Activate Discount', 'acf-blocks' );
$activate_discount_url  = acf_blocks_get_field( 'cb_activate_url', $block );

$block_id = 'coupon-' . ( $block['id'] ?? wp_unique_id() );
?>

<div class="acf-coupon-code-block" id="<?php echo esc_attr( $block_id ); ?>">
    <div class="acf-coupon-code-inner">
        <?php if ( $offer_details ) : ?>
            <div class="acf-coupon-offer-details">
                <?php echo esc_html( $offer_details ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $coupon_code ) : ?>
            <div class="acf-coupon-code-container">
                <span class="acf-coupon-code-text"><?php echo esc_html( $coupon_code ); ?></span>
                <button type="button" class="acf-coupon-copy-btn" data-code="<?php echo esc_attr( $coupon_code ); ?>" data-copied-label="<?php echo esc_attr( __( 'Copied!', 'acf-blocks' ) ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                        <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                    </svg>
                    <span class="btn-text"><?php echo esc_html( $copy_coupon_text ); ?></span>
                </button>
            </div>
        <?php endif; ?>

        <div class="acf-coupon-activate">
            <?php if ( $activate_discount_url ) : ?>
                <a href="<?php echo esc_url( $activate_discount_url ); ?>" class="acf-activate-discount-btn" target="_blank" rel="noopener">
                    <?php echo esc_html( $activate_discount_text ); ?>
                </a>
            <?php else : ?>
                <span class="acf-activate-discount-btn"><?php echo esc_html( $activate_discount_text ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

