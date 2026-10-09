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

// Resolve file field: compat layer may return numeric attachment ID instead of array.
if ( $video_file && is_numeric( $video_file ) ) {
	$attachment_id = intval( $video_file );
	$file_url      = wp_get_attachment_url( $attachment_id );
	$file_mime     = get_post_mime_type( $attachment_id );
	if ( $file_url ) {
		$video_file = array(
			'ID'        => $attachment_id,
			'url'       => $file_url,
			'mime_type' => $file_mime ?: 'video/mp4',
		);
	} else {
		$video_file = null;
	}
}

// Resolve poster image: compat layer may return numeric attachment ID instead of array.
if ( $video_poster && is_numeric( $video_poster ) ) {
	$resolved_poster = acf_blocks_resolve_image( $video_poster, '', 'full' );
	if ( $resolved_poster['src'] ) {
		$video_poster = array( 'url' => $resolved_poster['src'] );
	} else {
		$video_poster = null;
	}
}
$video_title     = acf_blocks_get_field( 'acf_video_title', $block );
$video_caption   = acf_blocks_get_field( 'acf_video_caption', $block );
$aspect_ratio    = acf_blocks_get_field( 'acf_video_aspect_ratio', $block );
$autoplay        = acf_blocks_get_field( 'acf_video_autoplay', $block );
$loop            = acf_blocks_get_field( 'acf_video_loop', $block );
$muted           = acf_blocks_get_field( 'acf_video_muted', $block );
$controls        = acf_blocks_get_field( 'acf_video_controls', $block );
$controls        = null === $controls ? true : $controls;

$custom_class = acf_blocks_get_field( 'acf_video_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_video_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

/*
 * The player is absolutely positioned, so the wrapper is the only thing giving
 * this block height. If the stylesheet does not apply — a stale editor bundle,
 * a caching layer, or an editor iframe that never receives it — the block
 * collapses to 0px and disappears entirely, which is what made it render on the
 * front end but not in the editor. Other blocks merely look unstyled.
 *
 * Carrying the ratio inline makes the box intrinsic to the markup, so the block
 * always occupies space even with no CSS at all.
 */
$aspect_ratios = array(
    '16-9' => '16 / 9',
    '4-3'  => '4 / 3',
    '21-9' => '21 / 9',
    '1-1'  => '1 / 1',
);
$aspect_ratio     = is_string( $aspect_ratio ) && isset( $aspect_ratios[ $aspect_ratio ] ) ? $aspect_ratio : '16-9';
$aspect_ratio_class = ' acf-aspect-' . $aspect_ratio;
$ratio_value      = $aspect_ratios[ $aspect_ratio ];
$wrapper_style_at = ' style="aspect-ratio: ' . esc_attr( $ratio_value ) . ';"';

require_once __DIR__ . '/extra.php';

// Generate unique ID for this block
$block_id = isset( $block['id'] ) ? $block['id'] : wp_unique_id( 'video-' );
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="acf-video-block<?php echo $aspect_ratio_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $video_title ) : ?>
        <div class="acf-video-title">
            <h3><?php echo esc_html( $video_title ); ?></h3>
        </div>
    <?php endif; ?>

    <div class="acf-video-wrapper"<?php echo $wrapper_style_at; ?>>
        <?php if ( $video_type === 'youtube' && $video_url ) : ?>
            <?php
            $youtube_id = acf_get_youtube_id( $video_url );
            if ( $youtube_id ) :
                $embed_params = array();
                $start_time = acf_video_get_start_time( $video_url );
                if ( $start_time ) $embed_params[] = 'start=' . $start_time;
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1&playlist=' . $youtube_id;
                if ( $muted ) $embed_params[] = 'mute=1';
                if ( ! $controls ) $embed_params[] = 'controls=0';
                $params_string = ! empty( $embed_params ) ? '&' . implode( '&', $embed_params ) : '';

                // Use facade pattern for performance - show thumbnail, load iframe on click
                $thumbnail_url = 'https://i.ytimg.com/vi/' . $youtube_id . '/maxresdefault.jpg';
                $thumbnail_fallback = 'https://i.ytimg.com/vi/' . $youtube_id . '/hqdefault.jpg';
                $embed_url = 'https://www.youtube.com/embed/' . $youtube_id . '?' . ltrim( $params_string, '&' );
                // The editor canvas uses a blob URL, which cannot send an HTTP
                // Referer even with an explicit policy. A real URL on this site
                // gives the nested YouTube player the required client identity.
                $player_url = $is_preview
                    ? ACF_BLOCKS_PLUGIN_URL . 'blocks/video-block/youtube-preview.html?ver=' . rawurlencode( ACF_BLOCKS_VERSION ) . '&video=' . rawurlencode( $embed_url ) . '&title=' . rawurlencode( $video_title ?: __( 'YouTube video player', 'acf-blocks' ) )
                    : $embed_url;
                ?>
                <?php if ( $is_preview || $autoplay ) : ?>
                    <iframe
                        src="<?php echo esc_url( $player_url ); ?>"
                        title="<?php echo esc_attr( $video_title ?: __( 'YouTube video player', 'acf-blocks' ) ); ?>"
                        width="1280" height="720"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen="allowfullscreen"
                        loading="<?php echo $is_preview ? 'eager' : 'lazy'; ?>">
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
                             data-thumbnail-fallback="<?php echo esc_url( $thumbnail_fallback ); ?>"
                             width="1280" height="720"
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
            <?php elseif ( $is_preview ) : ?>
                <p><?php esc_html_e( 'Enter a valid YouTube video URL.', 'acf-blocks' ); ?></p>
            <?php endif; ?>

        <?php elseif ( $video_type === 'vimeo' && $video_url ) : ?>
            <?php
            $vimeo_data = acf_video_get_vimeo_data( $video_url );
            $vimeo_id = $vimeo_data ? $vimeo_data['id'] : false;
            if ( $vimeo_id ) :
                $embed_params = array();
                if ( $vimeo_data['hash'] ) $embed_params[] = 'h=' . rawurlencode( $vimeo_data['hash'] );
                if ( $autoplay ) $embed_params[] = 'autoplay=1';
                if ( $loop ) $embed_params[] = 'loop=1';
                if ( $muted ) $embed_params[] = 'muted=1';
                if ( ! $controls ) $embed_params[] = 'controls=0';
                $params_string = ! empty( $embed_params ) ? '&' . implode( '&', $embed_params ) : '';
                ?>
                <?php if ( $is_preview || $autoplay ) : ?>
                    <iframe
                        src="https://player.vimeo.com/video/<?php echo esc_attr( $vimeo_id ); ?>?<?php echo esc_attr( ltrim( $params_string, '&' ) ); ?>"
                        title="<?php echo esc_attr( $video_title ?: __( 'Vimeo video player', 'acf-blocks' ) ); ?>"
                        width="1280" height="720"
                        frameborder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen="allowfullscreen"
                        loading="<?php echo $is_preview ? 'eager' : 'lazy'; ?>">
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
            <?php elseif ( $is_preview ) : ?>
                <p><?php esc_html_e( 'Enter a valid Vimeo video URL.', 'acf-blocks' ); ?></p>
            <?php endif; ?>

        <?php
        elseif ( $video_type === 'self-hosted' ) :
            // URL field overrides the media-library file upload.
            $self_src  = '';
            $self_type = '';
            if ( $video_url ) {
                $self_src  = $video_url;
                // Derive MIME type from URL extension.
                $ext = strtolower( pathinfo( wp_parse_url( $video_url, PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
                $mime_map  = array( 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg', 'mov' => 'video/mp4' );
                $self_type = isset( $mime_map[ $ext ] ) ? $mime_map[ $ext ] : 'video/mp4';
            } elseif ( $video_file ) {
                $self_src  = $video_file['url'];
                $self_type = $video_file['mime_type'];
            }
            if ( $self_src ) :
                $preload = ( $video_poster && ! $autoplay ) ? 'none' : 'metadata';
        ?>
            <video
                <?php echo $controls ? 'controls="controls"' : ''; ?>
                <?php echo $autoplay ? 'autoplay="autoplay"' : ''; ?>
                <?php echo $loop ? 'loop="loop"' : ''; ?>
                <?php echo $muted ? 'muted="muted"' : ''; ?>
                <?php echo $video_poster ? 'poster="' . esc_url( $video_poster['url'] ) . '"' : ''; ?>
                preload="<?php echo esc_attr( $preload ); ?>"
                playsinline="playsinline"
                <?php if ( ! $autoplay && ! $is_preview ) : ?>data-lazy-src="<?php echo esc_url( $self_src ); ?>" data-lazy-type="<?php echo esc_attr( $self_type ); ?>"<?php else : ?>src="<?php echo esc_url( $self_src ); ?>"<?php endif; ?>>
                <?php if ( $autoplay || $is_preview ) : ?>
                <source src="<?php echo esc_url( $self_src ); ?>" type="<?php echo esc_attr( $self_type ); ?>">
                <?php endif; ?>
                <?php esc_html_e( 'Your browser does not support the video tag.', 'acf-blocks' ); ?>
            </video>
        <?php endif; ?>

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
