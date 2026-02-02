<?php
/**
 * Video Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$video_type      = acf_blocks_get_field( 'acf_video_type', $block );
$video_url       = acf_blocks_get_field( 'acf_video_url', $block );
$video_file      = acf_blocks_get_field( 'acf_video_file', $block );
$video_poster    = acf_blocks_get_field( 'acf_video_poster', $block );
$video_title     = acf_blocks_get_field( 'acf_video_title', $block );
$video_caption   = acf_blocks_get_field( 'acf_video_caption', $block );
$aspect_ratio    = acf_blocks_get_field( 'acf_video_aspect_ratio', $block );
$autoplay        = acf_blocks_get_field( 'acf_video_autoplay', $block );
$loop            = acf_blocks_get_field( 'acf_video_loop', $block );
$muted           = acf_blocks_get_field( 'acf_video_muted', $block );
$controls        = acf_blocks_get_field( 'acf_video_controls', $block );

$custom_class = acf_blocks_get_field( 'acf_video_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_video_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$aspect_ratio_class = $aspect_ratio ? ' acf-aspect-' . esc_attr( $aspect_ratio ) : ' acf-aspect-16-9';

// Function to extract video ID from YouTube URL
if ( ! function_exists( 'acf_get_youtube_id' ) ) {
    function acf_get_youtube_id( $url ) {
        preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches );
        return isset( $matches[1] ) ? $matches[1] : false;
    }
}

// Function to extract video ID from Vimeo URL
if ( ! function_exists( 'acf_get_vimeo_id' ) ) {
    function acf_get_vimeo_id( $url ) {
        preg_match( '/(?:vimeo\.com\/)(\d+)/i', $url, $matches );
        return isset( $matches[1] ) ? $matches[1] : false;
    }
}

// Generate unique ID for this block
$block_id = isset( $block['id'] ) ? $block['id'] : wp_unique_id( 'video-' );
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="acf-video-block<?php echo $aspect_ratio_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $video_title ) : ?>
        <div class="acf-video-title">
            <h3><?php echo esc_html( $video_title ); ?></h3>
        </div>
    <?php endif; ?>

    <div class="acf-video-wrapper">
        <?php if ( $video_type === 'youtube' && $video_url ) : ?>
            <?php
            $youtube_id = acf_get_youtube_id( $video_url );
            if ( $youtube_id ) :
                $embed_params = array();
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1&playlist=' . $youtube_id;
                if ( $muted ) $embed_params[] = 'mute=1';
                if ( ! $controls ) $embed_params[] = 'controls=0';
                $params_string = ! empty( $embed_params ) ? '&' . implode( '&', $embed_params ) : '';

                // Use facade pattern for performance - show thumbnail, load iframe on click
                $thumbnail_url = 'https://i.ytimg.com/vi/' . esc_attr( $youtube_id ) . '/hqdefault.jpg';
                ?>
                <?php if ( $is_preview || $autoplay ) : ?>
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo esc_attr( $youtube_id ); ?>?<?php echo ltrim( $params_string, '&' ); ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                <?php else : ?>
                    <div class="acf-video-facade"
                         data-video-id="<?php echo esc_attr( $youtube_id ); ?>"
                         data-video-type="youtube"
                         data-params="<?php echo esc_attr( $params_string ); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr( $video_title ?: __( 'Play video', 'acf-blocks' ) ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                             alt="<?php echo esc_attr( $video_title ?: __( 'Video thumbnail', 'acf-blocks' ) ); ?>"
                             loading="lazy"
                             decoding="async" />
                        <div class="acf-video-play-button">
                            <svg viewBox="0 0 68 48" width="68" height="48">
                                <path class="acf-video-play-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"/>
                                <path d="M 45,24 27,14 27,34" fill="#fff"/>
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ( $video_type === 'vimeo' && $video_url ) : ?>
            <?php
            $vimeo_id = acf_get_vimeo_id( $video_url );
            if ( $vimeo_id ) :
                $embed_params = array();
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1';
                if ( $muted ) $embed_params[] = 'muted=1';
                $params_string = ! empty( $embed_params ) ? '&' . implode( '&', $embed_params ) : '';
                ?>
                <?php if ( $is_preview || $autoplay ) : ?>
                    <iframe
                        src="https://player.vimeo.com/video/<?php echo esc_attr( $vimeo_id ); ?>?<?php echo ltrim( $params_string, '&' ); ?>"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                <?php else : ?>
                    <div class="acf-video-facade"
                         data-video-id="<?php echo esc_attr( $vimeo_id ); ?>"
                         data-video-type="vimeo"
                         data-params="<?php echo esc_attr( $params_string ); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr( $video_title ?: __( 'Play video', 'acf-blocks' ) ); ?>">
                        <div class="acf-video-vimeo-thumb" data-vimeo-id="<?php echo esc_attr( $vimeo_id ); ?>"></div>
                        <div class="acf-video-play-button acf-video-play-vimeo">
                            <svg viewBox="0 0 68 48" width="68" height="48">
                                <circle cx="34" cy="24" r="23" fill="#00adef"/>
                                <path d="M 45,24 27,14 27,34" fill="#fff"/>
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ( $video_type === 'self-hosted' && $video_file ) : ?>
            <video
                <?php echo $controls !== false ? 'controls' : ''; ?>
                <?php echo $autoplay ? 'autoplay' : ''; ?>
                <?php echo $loop ? 'loop' : ''; ?>
                <?php echo $muted ? 'muted' : ''; ?>
                <?php echo $video_poster ? 'poster="' . esc_url( $video_poster['url'] ) . '"' : ''; ?>
                preload="metadata"
                playsinline>
                <source src="<?php echo esc_url( $video_file['url'] ); ?>" type="<?php echo esc_attr( $video_file['mime_type'] ); ?>">
                <?php esc_html_e( 'Your browser does not support the video tag.', 'acf-blocks' ); ?>
            </video>

        <?php else : ?>
            <?php if ( $is_preview ) : ?>
                <p><em><?php esc_html_e( 'Please configure the video settings.', 'acf-blocks' ); ?></em></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ( $video_caption ) : ?>
        <div class="acf-video-caption">
            <?php echo esc_html( $video_caption ); ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Add facade script once per page
static $acf_video_facade_script_added = false;
if ( ! $acf_video_facade_script_added && ! $is_preview && ! $autoplay ) :
    $acf_video_facade_script_added = true;
?>
<script>
(function() {
    function initVideoFacades() {
        document.querySelectorAll('.acf-video-facade').forEach(function(facade) {
            if (facade.dataset.initialized) return;
            facade.dataset.initialized = 'true';

            function loadVideo() {
                var videoId = facade.dataset.videoId;
                var videoType = facade.dataset.videoType;
                var params = facade.dataset.params || '';
                var iframe = document.createElement('iframe');

                if (videoType === 'youtube') {
                    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1' + params;
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                } else if (videoType === 'vimeo') {
                    iframe.src = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1' + params;
                    iframe.allow = 'autoplay; fullscreen; picture-in-picture';
                }

                iframe.frameBorder = '0';
                iframe.allowFullscreen = true;
                facade.parentNode.replaceChild(iframe, facade);
            }

            facade.addEventListener('click', loadVideo);
            facade.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    loadVideo();
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVideoFacades);
    } else {
        initVideoFacades();
    }
})();
</script>
<?php endif; ?>
