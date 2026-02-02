<?php
$avatar = acf_blocks_get_field('ob_avatar', $block);
$avatar_url = acf_blocks_get_field('ob_avatar_url', $block);
$citation = acf_blocks_get_field('ob_citation', $block);
$name = acf_blocks_get_field('ob_name', $block);

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = $name ?: 'Author';
if ( $avatar_url ) {
    $img_src = $avatar_url;
} elseif ( $avatar ) {
    $img_src = $avatar['url'];
    $img_alt = $avatar['alt'] ?: $img_alt;
}
?>

<div class="acf-opinion-box">
    <div class="acf-opinion-box-content">
        <InnerBlocks templateLock="false" />
    </div>

    <div class="acf-opinion-box-meta">
        <?php if($img_src): ?>
            <div class="acf-opinion-box-avatar">
                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="acf-opinion-box-avatar-image" loading="lazy" />
            </div>
        <?php endif; ?>

        <div class="acf-opinion-box-author">
            <?php if($name): ?>
                <div class="acf-opinion-box-author-name"><?php echo esc_html($name); ?></div>
            <?php endif; ?>

            <?php if($designation = acf_blocks_get_field('ob_designation', $block)): ?>
                <div class="acf-opinion-box-author-designation"><?php echo esc_html($designation); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($citation): ?>
        <div class="acf-opinion-box-citation">
            <?php echo esc_html($citation); ?>
        </div>
    <?php endif; ?>
</div>