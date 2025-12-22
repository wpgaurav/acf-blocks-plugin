<?php
$avatar = get_field('ob_avatar');
$citation = get_field('ob_citation');
?>

<div class="acf-opinion-box">
    <div class="acf-opinion-box-content">
        <InnerBlocks templateLock="false" />
    </div>

    <div class="acf-opinion-box-meta">
        <?php if($avatar): ?>
            <div class="acf-opinion-box-avatar">
                <?php echo wp_get_attachment_image($avatar['ID'], 'thumbnail', false, [
                    'class' => 'acf-opinion-box-avatar-image',
                    'loading' => 'lazy'
                ]); ?>
            </div>
        <?php endif; ?>

        <div class="acf-opinion-box-author">
            <?php if($name = get_field('ob_name')): ?>
                <div class="acf-opinion-box-author-name"><?php echo esc_html($name); ?></div>
            <?php endif; ?>

            <?php if($designation = get_field('ob_designation')): ?>
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