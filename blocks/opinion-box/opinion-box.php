<?php
$avatar = get_field('ob_avatar');
$citation = get_field('ob_citation');
?>

<div class="opinion-block">
    <div class="opinion-content">
        <InnerBlocks templateLock="false" />
    </div>
    
    <div class="opinion-meta">
        <?php if($avatar): ?>
            <div class="opinion-avatar">
                <?php echo wp_get_attachment_image($avatar['ID'], 'thumbnail', false, [
                    'class' => 'avatar-image',
                    'loading' => 'lazy'
                ]); ?>
            </div>
        <?php endif; ?>

        <div class="opinion-author">
            <?php if($name = get_field('ob_name')): ?>
                <div class="author-name"><?php echo esc_html($name); ?></div>
            <?php endif; ?>

            <?php if($designation = get_field('ob_designation')): ?>
                <div class="author-designation"><?php echo esc_html($designation); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($citation): ?>
        <div class="opinion-citation">
            <?php echo esc_html($citation); ?>
        </div>
    <?php endif; ?>
</div>