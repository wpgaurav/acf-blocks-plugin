<?php
/**
 * Coupon Code Block Template.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 * @param int    $post_id    The post ID this block is saved to.
 */

$offer_details          = get_field( 'cb_offer_details' );
$coupon_code            = get_field( 'cb_code' );
$copy_coupon_text       = get_field( 'cb_copy_text' ) ?: __( 'Copy Coupon', 'acf-blocks' );
$activate_discount_text = get_field( 'cb_activate_text' ) ?: __( 'Activate Discount', 'acf-blocks' );
$activate_discount_url  = get_field( 'cb_activate_url' );

$block_id = 'coupon-' . ( $block['id'] ?? uniqid() );
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
                <button type="button" class="acf-coupon-copy-btn" onclick="acfBlocksCopyCoupon(this, '<?php echo esc_js( $coupon_code ); ?>')">
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

<?php
// Output the copy script once per page
if ( ! defined( 'ACF_BLOCKS_COUPON_SCRIPT_LOADED' ) ) :
    define( 'ACF_BLOCKS_COUPON_SCRIPT_LOADED', true );
    ?>
    <script>
    function acfBlocksCopyCoupon(btn, code) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                var textSpan = btn.querySelector('.btn-text');
                var originalText = textSpan.textContent;
                textSpan.textContent = '<?php echo esc_js( __( 'Copied!', 'acf-blocks' ) ); ?>';
                setTimeout(function() {
                    textSpan.textContent = originalText;
                }, 2000);
            });
        } else {
            // Fallback for older browsers
            var tempInput = document.createElement('input');
            tempInput.value = code;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            var textSpan = btn.querySelector('.btn-text');
            var originalText = textSpan.textContent;
            textSpan.textContent = '<?php echo esc_js( __( 'Copied!', 'acf-blocks' ) ); ?>';
            setTimeout(function() {
                textSpan.textContent = originalText;
            }, 2000);
        }
    }
    </script>
<?php endif; ?>
