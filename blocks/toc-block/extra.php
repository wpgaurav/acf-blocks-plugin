<?php
/**
 * Table of Contents Block - Extra functionality
 *
 * Adds ID attributes to headings that don't have them,
 * enabling the TOC to link to any heading in the content.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add ID attributes to headings in the content.
 *
 * This filter runs on `the_content` and adds ID attributes to any heading
 * that doesn't already have one. This ensures TOC links work properly.
 *
 * @param string $content The post content.
 * @return string Modified content with heading IDs.
 */
if ( ! function_exists( 'acf_toc_add_heading_ids' ) ) {
function acf_toc_add_heading_ids( $content ) {
    // Only process on singular pages with content
    if ( is_admin() || empty( $content ) ) {
        return $content;
    }

    // Check original post content for TOC block presence
    // (block comments are already resolved to HTML at this priority)
    global $post;
    if ( ! $post || ! has_block( 'acf/toc', $post->post_content ) ) {
        return $content;
    }

    // Temporarily remove rendered TOC navigation so its own title is not
    // counted as a content heading.
    $protected = array();
    $content = preg_replace_callback(
        '/<nav\b[^>]*class=["\'][^"\']*\bacf-toc\b[^"\']*["\'][^>]*>.*?<\/nav>/is',
        function( $match ) use ( &$protected ) {
            $key = '%%ACF_TOC_' . count( $protected ) . '%%';
            $protected[ $key ] = $match[0];
            return $key;
        },
        $content
    );

    // Track existing IDs to avoid duplicates.
    $existing_ids = array();
    $heading_index = 0;

    // Find all headings without IDs
    $pattern = '/<(h[1-6])([^>]*)>(.*?)<\/\1>/is';

    $content = preg_replace_callback( $pattern, function( $matches ) use ( &$existing_ids, &$heading_index ) {
        $tag        = $matches[1];
        $attributes = $matches[2];
        $text       = $matches[3];
        $heading_index++;

        $id = '';
        if ( preg_match( '/\bid=["\']([^"\']+)["\']/i', $attributes, $id_match ) ) {
            $id = $id_match[1];
            $attributes = preg_replace( '/\s*\bid=["\'][^"\']+["\']/i', '', $attributes, 1 );
        }

        if ( '' === $id ) {
            $id = sanitize_title( wp_strip_all_tags( $text ) );
        }

        if ( empty( $id ) ) {
            $id = 'heading-' . $heading_index;
        }

        // Handle duplicates
        $original_id = $id;
        $counter = 2;
        while ( isset( $existing_ids[ $id ] ) ) {
            $id = $original_id . '-' . $counter;
            $counter++;
        }
        $existing_ids[ $id ] = true;

        // Add ID to the heading
        if ( empty( $attributes ) ) {
            $new_opening = '<' . $tag . ' id="' . esc_attr( $id ) . '">';
        } else {
            $new_opening = '<' . $tag . ' id="' . esc_attr( $id ) . '"' . $attributes . '>';
        }

        return $new_opening . $text . '</' . $tag . '>';
    }, $content );

    return strtr( $content, $protected );
}
add_filter( 'the_content', 'acf_toc_add_heading_ids', 10 );
}
