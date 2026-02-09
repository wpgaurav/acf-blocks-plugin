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

    // Track existing IDs to avoid duplicates
    $existing_ids = array();

    // Find all headings without IDs
    $pattern = '/<(h[1-6])([^>]*)>(.*?)<\/\1>/is';

    $content = preg_replace_callback( $pattern, function( $matches ) use ( &$existing_ids ) {
        $tag        = $matches[1];
        $attributes = $matches[2];
        $text       = $matches[3];

        // Check if heading already has an ID
        if ( preg_match( '/\bid=["\']([^"\']+)["\']/i', $attributes, $id_match ) ) {
            $existing_ids[ $id_match[1] ] = true;
            return $matches[0]; // Return unchanged
        }

        // Generate ID from heading text
        $heading_text = wp_strip_all_tags( $text );
        $id = sanitize_title( $heading_text );

        // Handle empty IDs
        if ( empty( $id ) ) {
            $id = 'heading-' . wp_rand( 1000, 9999 );
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

    return $content;
}
add_filter( 'the_content', 'acf_toc_add_heading_ids', 10 );

/**
 * Add scroll-margin-top to headings when TOC is present.
 *
 * This ensures headings are not hidden behind fixed headers
 * when navigating via TOC links.
 */
function acf_toc_add_scroll_margin_style() {
    global $post;

    if ( ! is_singular() || ! $post || ! has_block( 'acf/toc', $post->post_content ) ) {
        return;
    }

    // Get the sticky offset if TOC is set to sticky
    // Default to a reasonable value for most themes
    ?>
    <style id="acf-toc-scroll-margin">
        h1[id], h2[id], h3[id], h4[id], h5[id], h6[id] {
            scroll-margin-top: 80px;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'acf_toc_add_scroll_margin_style', 99 );
