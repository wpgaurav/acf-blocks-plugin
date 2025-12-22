<?php
/**
 * Coupon Code Block Template.
 *
 * @var array  $block       The block settings and attributes.
 * @var string $content     The block inner HTML (empty).
 * @var bool   $is_preview  True during AJAX preview.
 * @var int    $post_id     The post ID this block is saved to.
 */

// Retrieve ACF field values with the new "cb_" names.
$offer_details          = get_field('cb_offer_details');
$coupon_code            = get_field('cb_code');
$copy_coupon_text       = get_field('cb_copy_text') ?: 'Copy Coupon';
$activate_discount_text = get_field('cb_activate_text') ?: 'Activate Discount';
$activate_discount_url  = get_field('cb_activate_url');
?>

<div class="coupon-code-block">
    <div class="coupon-code-inner">
        <?php if( $offer_details ): ?>
            <div class="coupon-offer-details">
                <?php echo $offer_details; ?>
            </div>
        <?php endif; ?>
        
        <?php if( $coupon_code ): ?>
            <div class="coupon-code-container">
                <span class="coupon-code-text" id="couponCode-<?php echo esc_attr($block['id']); ?>">
                    <?php echo esc_html($coupon_code); ?>
                </span>
                <button class="coupon-copy-btn" data-target="couponCode-<?php echo esc_attr($block['id']); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                    </svg>
                    <?php echo esc_html($copy_coupon_text); ?>
                </button>
            </div>
        <?php endif; ?>
        
        <div class="coupon-activate">
            <?php if( $activate_discount_url ): ?>
                <a href="<?php echo esc_url($activate_discount_url); ?>" class="activate-discount-btn">
                    <?php echo esc_html($activate_discount_text); ?>
                </a>
            <?php else: ?>
                <button class="activate-discount-btn">
                    <?php echo esc_html($activate_discount_text); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var copyButtons = document.querySelectorAll('.coupon-copy-btn');
    copyButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var couponTextElem = document.getElementById(targetId);
            if(couponTextElem) {
                var couponText = couponTextElem.innerText;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(couponText).then(function() {
                        alert('Coupon code copied!');
                    });
                } else {
                    var tempInput = document.createElement('input');
                    tempInput.value = couponText;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    alert('Coupon code copied!');
                }
            }
        });
    });
});
</script>