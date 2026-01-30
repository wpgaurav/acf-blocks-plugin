<?php
/**
 * Team Member Block Template.
 *
 * Uses core blocks via InnerBlocks for name, title, and bio content.
 * Falls back to legacy ACF fields for backward compatibility.
 * ACF fields are retained for photo, contact info, social links, and styling.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$photo        = get_field( 'acf_team_member_photo' );
$photo_url    = get_field( 'acf_team_member_photo_url' );
$email        = get_field( 'acf_team_member_email' );
$phone        = get_field( 'acf_team_member_phone' );
$social_links = get_field( 'acf_team_member_social_links' );

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = 'Team member';
if ( $photo_url ) {
    $img_src = $photo_url;
} elseif ( $photo ) {
    $img_src = $photo['url'];
    $img_alt = $photo['alt'] ?: $img_alt;
}

$custom_class = get_field( 'acf_team_member_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_team_member_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

// Check for legacy ACF field content (backward compatibility)
$legacy_name  = get_field( 'acf_team_member_name' );
$legacy_title = get_field( 'acf_team_member_title' );
$legacy_bio   = get_field( 'acf_team_member_bio' );
$has_legacy_content = $legacy_name || $legacy_title || $legacy_bio;

$inner_blocks_template = [
    [ 'core/heading', [ 'level' => 3, 'placeholder' => 'Team member name...' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Job title or role...', 'className' => 'acf-team-member-title' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Short bio...' ] ]
];
?>

<div class="acf-team-member-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $img_src ) : ?>
        <div class="acf-team-member-photo">
            <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async" />
        </div>
    <?php endif; ?>

    <div class="acf-team-member-content">
        <?php if ( $has_legacy_content && empty( trim( $content ) ) ) : ?>
            <?php // Legacy rendering for blocks created before InnerBlocks migration ?>
            <?php if ( $legacy_name ) : ?>
                <h3 class="acf-team-member-name"><?php echo esc_html( $legacy_name ); ?></h3>
            <?php endif; ?>

            <?php if ( $legacy_title ) : ?>
                <div class="acf-team-member-title"><?php echo esc_html( $legacy_title ); ?></div>
            <?php endif; ?>

            <?php if ( $legacy_bio ) : ?>
                <div class="acf-team-member-bio">
                    <?php echo wp_kses_post( $legacy_bio ); ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
        <?php endif; ?>

        <?php if ( $email || $phone ) : ?>
            <div class="acf-team-member-contact">
                <?php if ( $email ) : ?>
                    <div class="acf-contact-item">
                        <span class="acf-contact-icon">&#9993;</span>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ( $phone ) : ?>
                    <div class="acf-contact-item">
                        <span class="acf-contact-icon">&#128222;</span>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ( $social_links && is_array( $social_links ) && count( $social_links ) > 0 ) : ?>
            <div class="acf-team-member-social">
                <?php foreach ( $social_links as $link ) : ?>
                    <?php if ( $link['acf_social_platform'] && $link['acf_social_url'] ) : ?>
                        <a href="<?php echo esc_url( $link['acf_social_url'] ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="acf-social-link <?php echo esc_attr( strtolower( $link['acf_social_platform'] ) ); ?>"
                           aria-label="<?php echo esc_attr( $link['acf_social_platform'] ); ?>">
                            <?php echo esc_html( $link['acf_social_platform'] ); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
