<?php
/**
 * Team Member Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$photo        = get_field( 'acf_team_member_photo' );
$name         = get_field( 'acf_team_member_name' );
$title        = get_field( 'acf_team_member_title' );
$bio          = get_field( 'acf_team_member_bio' );
$email        = get_field( 'acf_team_member_email' );
$phone        = get_field( 'acf_team_member_phone' );
$social_links = get_field( 'acf_team_member_social_links' );

$custom_class = get_field( 'acf_team_member_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_team_member_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="acf-team-member-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $photo ) : ?>
        <div class="acf-team-member-photo">
            <img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ? $photo['alt'] : $name ); ?>" />
        </div>
    <?php endif; ?>

    <div class="acf-team-member-content">
        <?php if ( $name ) : ?>
            <h3 class="acf-team-member-name"><?php echo esc_html( $name ); ?></h3>
        <?php endif; ?>

        <?php if ( $title ) : ?>
            <div class="acf-team-member-title"><?php echo esc_html( $title ); ?></div>
        <?php endif; ?>

        <?php if ( $bio ) : ?>
            <div class="acf-team-member-bio">
                <?php echo wpautop( esc_html( $bio ) ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $email || $phone ) : ?>
            <div class="acf-team-member-contact">
                <?php if ( $email ) : ?>
                    <div class="acf-contact-item">
                        <span class="acf-contact-icon">✉</span>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ( $phone ) : ?>
                    <div class="acf-contact-item">
                        <span class="acf-contact-icon">📞</span>
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
