<?php
/** Video URL parsing shared by rendering and tests. */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'acf_get_youtube_id' ) ) {
    function acf_get_youtube_id( $url ) {
        if ( ! is_string( $url ) ) {
            return false;
        }
        $parts = wp_parse_url( $url );
        if ( ! is_array( $parts ) || empty( $parts['host'] ) || ! in_array( strtolower( $parts['scheme'] ?? '' ), array( 'http', 'https' ), true ) ) {
            return false;
        }
        $host = strtolower( $parts['host'] );
        $path = $parts['path'] ?? '';
        $id   = false;
        if ( in_array( $host, array( 'youtu.be', 'www.youtu.be' ), true ) ) {
            $id = trim( $path, '/' );
        } elseif ( in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com' ), true ) ) {
            if ( preg_match( '~^/(?:embed|shorts|live|v|e)/([A-Za-z0-9_-]{11})/?$~', $path, $matches ) ) {
                $id = $matches[1];
            } elseif ( '/watch' === $path ) {
                parse_str( $parts['query'] ?? '', $query );
                $id = $query['v'] ?? false;
            }
        }
        return is_string( $id ) && preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ? $id : false;
    }
}

/** Return a Vimeo ID and its optional unlisted privacy hash. */
function acf_video_get_vimeo_data( $url ) {
    if ( ! is_string( $url ) ) {
        return false;
    }
    $parts = wp_parse_url( $url );
    if ( ! is_array( $parts ) || ! in_array( strtolower( $parts['host'] ?? '' ), array( 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ), true )
        || ! in_array( strtolower( $parts['scheme'] ?? '' ), array( 'http', 'https' ), true ) ) {
        return false;
    }
    if ( ! preg_match( '~^/(?:video/|channels/[^/]+/|groups/[^/]+/videos/|(?:album|showcase)/[0-9]+/video/)?([0-9]+)(?:/([A-Za-z0-9]+))?/?$~', $parts['path'] ?? '', $matches ) ) {
        return false;
    }
    parse_str( $parts['query'] ?? '', $query );
    $hash = $query['h'] ?? ( $matches[2] ?? '' );
    $hash = is_string( $hash ) && preg_match( '/^[A-Za-z0-9]{1,128}$/', $hash ) ? $hash : '';
    return array( 'id' => $matches[1], 'hash' => $hash );
}

/** Preserve YouTube share timestamps without forwarding arbitrary parameters. */
function acf_video_get_start_time( $url ) {
    $parts = wp_parse_url( $url );
    parse_str( $parts['query'] ?? '', $query );
    parse_str( $parts['fragment'] ?? '', $fragment );
    $value = $query['start'] ?? ( $query['t'] ?? ( $fragment['t'] ?? '' ) );
    if ( ! is_string( $value ) ) {
        return 0;
    }
    if ( preg_match( '/^[0-9]{1,9}$/', $value ) ) {
        return (int) $value;
    }
    if ( $value && preg_match( '/^(?:([0-9]{1,6})h)?(?:([0-9]{1,6})m)?(?:([0-9]{1,6})s)?$/i', $value, $matches ) ) {
        return (int) ( $matches[1] ?? 0 ) * 3600 + (int) ( $matches[2] ?? 0 ) * 60 + (int) ( $matches[3] ?? 0 );
    }
    return 0;
}

if ( ! function_exists( 'acf_get_vimeo_id' ) ) {
    function acf_get_vimeo_id( $url ) {
        $video = acf_video_get_vimeo_data( $url );
        return $video ? $video['id'] : false;
    }
}
