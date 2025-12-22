<?php
/**
 * Video Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$video_type      = get_field( 'acf_video_type' );
$video_url       = get_field( 'acf_video_url' );
$video_file      = get_field( 'acf_video_file' );
$video_poster    = get_field( 'acf_video_poster' );
$video_title     = get_field( 'acf_video_title' );
$video_caption   = get_field( 'acf_video_caption' );
$aspect_ratio    = get_field( 'acf_video_aspect_ratio' );
$autoplay        = get_field( 'acf_video_autoplay' );
$loop            = get_field( 'acf_video_loop' );
$muted           = get_field( 'acf_video_muted' );
$controls        = get_field( 'acf_video_controls' );

$custom_class = get_field( 'acf_video_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_video_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$aspect_ratio_class = $aspect_ratio ? ' acf-aspect-' . esc_attr( $aspect_ratio ) : ' acf-aspect-16-9';

// Function to extract video ID from YouTube URL
function get_youtube_id( $url ) {
    preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches );
    return isset( $matches[1] ) ? $matches[1] : false;
}

// Function to extract video ID from Vimeo URL
function get_vimeo_id( $url ) {
    preg_match( '/(?:vimeo\.com\/)(\d+)/i', $url, $matches );
    return isset( $matches[1] ) ? $matches[1] : false;
}
?>

<div class="acf-video-block<?php echo $aspect_ratio_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $video_title ) : ?>
        <div class="acf-video-title">
            <h3><?php echo esc_html( $video_title ); ?></h3>
        </div>
    <?php endif; ?>

    <div class="acf-video-wrapper">
        <?php if ( $video_type === 'youtube' && $video_url ) : ?>
            <?php
            $youtube_id = get_youtube_id( $video_url );
            if ( $youtube_id ) :
                $embed_params = array();
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1&playlist=' . $youtube_id;
                if ( $muted ) $embed_params[] = 'mute=1';
                if ( ! $controls ) $embed_params[] = 'controls=0';
                $params_string = ! empty( $embed_params ) ? '?' . implode( '&', $embed_params ) : '';
                ?>
                <iframe
                    src="https://www.youtube.com/embed/<?php echo esc_attr( $youtube_id . $params_string ); ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            <?php endif; ?>

        <?php elseif ( $video_type === 'vimeo' && $video_url ) : ?>
            <?php
            $vimeo_id = get_vimeo_id( $video_url );
            if ( $vimeo_id ) :
                $embed_params = array();
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1';
                if ( $muted ) $embed_params[] = 'muted=1';
                $params_string = ! empty( $embed_params ) ? '?' . implode( '&', $embed_params ) : '';
                ?>
                <iframe
                    src="https://player.vimeo.com/video/<?php echo esc_attr( $vimeo_id . $params_string ); ?>"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen>
                </iframe>
            <?php endif; ?>

        <?php elseif ( $video_type === 'self-hosted' && $video_file ) : ?>
            <video
                <?php echo $controls !== false ? 'controls' : ''; ?>
                <?php echo $autoplay ? 'autoplay' : ''; ?>
                <?php echo $loop ? 'loop' : ''; ?>
                <?php echo $muted ? 'muted' : ''; ?>
                <?php echo $video_poster ? 'poster="' . esc_url( $video_poster['url'] ) . '"' : ''; ?>
                preload="metadata">
                <source src="<?php echo esc_url( $video_file['url'] ); ?>" type="<?php echo esc_attr( $video_file['mime_type'] ); ?>">
                Your browser does not support the video tag.
            </video>

        <?php else : ?>
            <?php if ( $is_preview ) : ?>
                <p><em>Please configure the video settings.</em></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ( $video_caption ) : ?>
        <div class="acf-video-caption">
            <?php echo esc_html( $video_caption ); ?>
        </div>
    <?php endif; ?>
</div>
