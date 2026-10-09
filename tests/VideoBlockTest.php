<?php

use PHPUnit\Framework\TestCase;

final class VideoBlockTest extends TestCase {
    private function render( array $data = array(), bool $preview = false ): string {
        $block = array(
            'id'   => 'video-test',
            'data' => array_merge( array(
                'acf_video_type'     => 'youtube',
                'acf_video_url'      => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                'acf_video_title'    => 'Test video',
                'acf_video_controls' => 1,
            ), $data ),
        );
        $is_preview = $preview;
        ob_start();
        include dirname( __DIR__ ) . '/blocks/video-block/video-block.php';
        return (string) ob_get_clean();
    }

    public function test_facade_requests_hd_widescreen_thumbnail(): void {
        $html = $this->render();
        $this->assertStringContainsString( '/maxresdefault.jpg', $html );
        $this->assertStringContainsString( 'width="1280" height="720"', $html );
        $this->assertStringContainsString( 'data-thumbnail-fallback="https://i.ytimg.com/vi/aqz-KE-bpKQ/hqdefault.jpg"', $html );
        $this->assertStringContainsString( 'aspect-ratio: 16 / 9;', $html );
        $this->assertStringContainsString( 'class="acf-video-facade"', $html );
        $this->assertStringNotContainsString( '<iframe', $html );
    }

    /** @dataProvider iframeModes */
    public function test_youtube_iframes_identify_the_embedding_site( bool $preview, int $autoplay ): void {
        $html = $this->render( array( 'acf_video_autoplay' => $autoplay ), $preview );
        $this->assertStringContainsString( '<iframe', $html );
        $this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
        $this->assertStringContainsString( 'title="Test video"', $html );
        $this->assertStringContainsString( 'loading="' . ( $preview ? 'eager' : 'lazy' ) . '"', $html );
        $this->assertStringContainsString( 'aspect-ratio: 16 / 9;', $html );
        if ( $preview ) {
            $this->assertStringContainsString( '/youtube-preview.html?ver=', $html );
            $this->assertStringContainsString( rawurlencode( 'https://www.youtube.com/embed/aqz-KE-bpKQ?' ), $html );
        } else {
            $this->assertStringContainsString( 'src="https://www.youtube.com/embed/aqz-KE-bpKQ?autoplay=1"', $html );
        }
    }

    public function iframeModes(): array {
        return array( 'editor' => array( true, 0 ), 'autoplay' => array( false, 1 ) );
    }

    public function test_invalid_ratio_keeps_widescreen_class_and_geometry_in_sync(): void {
        $html = $this->render( array( 'acf_video_aspect_ratio' => 'invalid' ) );
        $this->assertStringContainsString( 'acf-aspect-16-9', $html );
        $this->assertStringContainsString( 'aspect-ratio: 16 / 9;', $html );
    }

    public function test_explicit_ratio_remains_supported(): void {
        $html = $this->render( array( 'acf_video_aspect_ratio' => '4-3' ) );
        $this->assertStringContainsString( 'acf-aspect-4-3', $html );
        $this->assertStringContainsString( 'aspect-ratio: 4 / 3;', $html );
    }

    /** @dataProvider youtubeUrls */
    public function test_supported_youtube_urls_render_a_player( string $url ): void {
        $html = $this->render( array( 'acf_video_url' => $url ) );
        $this->assertStringContainsString( 'data-video-id="aqz-KE-bpKQ"', $html );
    }

    public function youtubeUrls(): array {
        return array_map( function( $url ) { return array( $url ); }, array(
            'https://youtu.be/aqz-KE-bpKQ?t=90s',
            'https://www.youtube.com/shorts/aqz-KE-bpKQ',
            'https://www.youtube.com/live/aqz-KE-bpKQ',
            'https://www.youtube-nocookie.com/embed/aqz-KE-bpKQ',
            'https://m.youtube.com/watch?v=aqz-KE-bpKQ',
        ) );
    }

    public function test_foreign_hosts_and_invalid_ids_show_an_editor_diagnostic(): void {
        foreach ( array( 'https://example.test/youtube.com/watch?v=aqz-KE-bpKQ', 'https://www.youtube.com/watch?v=aqz-KE-bpKQextra' ) as $url ) {
            $html = $this->render( array( 'acf_video_url' => $url ), true );
            $this->assertStringNotContainsString( '<iframe', $html );
            $this->assertStringContainsString( 'Enter a valid YouTube video URL.', $html );
        }
    }

    public function test_vimeo_embed_urls_preserve_unlisted_hash_and_controls(): void {
        foreach ( array( 'https://player.vimeo.com/video/123456789?h=abc123def4', 'https://vimeo.com/123456789/abc123def4', 'https://vimeo.com/channels/staffpicks/123456789?h=abc123def4', 'https://vimeo.com/groups/film/videos/123456789?h=abc123def4' ) as $url ) {
            $html = $this->render( array( 'acf_video_type' => 'vimeo', 'acf_video_url' => $url, 'acf_video_controls' => 0 ), true );
            $this->assertStringContainsString( 'https://player.vimeo.com/video/123456789?h=abc123def4&amp;controls=0', $html );
        }
    }

    public function test_null_controls_default_on_and_explicit_off_stays_off(): void {
        $this->assertStringNotContainsString( 'controls=0', $this->render( array( 'acf_video_controls' => null ), true ) );
        $this->assertStringContainsString( 'controls%3D0', $this->render( array( 'acf_video_controls' => 0 ), true ) );
        $this->assertStringContainsString( 'controls%3D0', $this->render( array( 'acf_video_controls' => false ), true ) );
    }

    public function test_share_timestamps_are_preserved(): void {
        foreach ( array( '90s' => 90, '1m30s' => 90, '1h2m3s' => 3723, '90' => 90 ) as $input => $seconds ) {
            $html = $this->render( array( 'acf_video_url' => 'https://youtu.be/aqz-KE-bpKQ?t=' . $input ) );
            $this->assertStringContainsString( 'start=' . $seconds, $html );
        }
    }

    public function test_saved_bundle_must_match_current_version_settings_and_readable_file(): void {
        $path = tempnam( sys_get_temp_dir(), 'acf-bundle-test-' );
        try {
            $hash = acf_blocks_editor_settings_hash( array( 'acf/hero' ) );
            $bundle = array( 'url' => 'https://example.test/editor.css', 'path' => $path, 'plugin_version' => ACF_BLOCKS_VERSION, 'settings_hash' => $hash );
            $this->assertTrue( acf_blocks_editor_bundle_is_current( $bundle, $hash ) );
            $this->assertFalse( acf_blocks_editor_bundle_is_current( $bundle, acf_blocks_editor_settings_hash( array() ) ) );
            $bundle['plugin_version'] = 'old';
            $this->assertFalse( acf_blocks_editor_bundle_is_current( $bundle, $hash ) );
            $bundle['plugin_version'] = ACF_BLOCKS_VERSION;
            unlink( $path );
            $this->assertFalse( acf_blocks_editor_bundle_is_current( $bundle, $hash ) );
            $this->assertSame( acf_blocks_editor_settings_hash( array( 'acf/hero', 'acf/video' ) ), acf_blocks_editor_settings_hash( array( 'acf/video', 'acf/hero', 'acf/video' ) ) );
        } finally {
            if ( is_file( $path ) ) unlink( $path );
        }
    }

    public function test_self_hosted_video_keeps_lazy_and_editor_sources(): void {
        $data = array( 'acf_video_type' => 'self-hosted', 'acf_video_url' => 'https://example.test/video.mp4' );
        $public = $this->render( $data );
        $preview = $this->render( $data, true );
        $this->assertStringContainsString( 'data-lazy-src="https://example.test/video.mp4"', $public );
        $this->assertStringContainsString( '<source src="https://example.test/video.mp4" type="video/mp4">', $preview );
    }

    public function test_email_form_controls_cannot_style_acf_inspector_containers(): void {
        $root = dirname( __DIR__ );
        foreach ( array( '/blocks/email-form/email-form.css', '/assets/css/editor-blocks.css' ) as $file ) {
            $css = (string) file_get_contents( $root . $file );
            preg_match_all( '/([^{}]+)\{[^{}]*\}/', $css, $rules );
            $controls = 0;
            foreach ( $rules[1] as $selectors ) {
                foreach ( explode( ',', $selectors ) as $selector ) {
                    if ( preg_match( '/\.acf-(input|submit|form-group)(?=[:\s{]|$)/', $selector ) ) {
                        $controls++;
                        $this->assertMatchesRegularExpression( '/\.acf-email-form(?:--inline)?\s/', $selector, $file . ': ' . $selector );
                    }
                }
            }
            $this->assertGreaterThan( 0, $controls );
        }
    }

    public function test_custom_form_ids_keep_their_labels_connected(): void {
        $block = array( 'id' => 'email-test', 'data' => array(
            'form_type' => 'form_action', 'display_name_field' => 1,
            'name_field_attributes' => array( 'id' => 'signup-name' ),
            'email_field_attributes' => array( 'id' => 'signup-email' ),
        ) );
        $is_preview = true;
        ob_start();
        include dirname( __DIR__ ) . '/blocks/email-form/email-form.php';
        $html = (string) ob_get_clean();
        foreach ( array( 'signup-name', 'signup-email' ) as $id ) {
            $this->assertStringContainsString( '<label for="' . $id . '">', $html );
            $this->assertStringContainsString( 'id="' . $id . '"', $html );
        }
        $this->assertSame( 2, substr_count( $html, 'hidden="hidden"' ) );
    }
}
