<?php
/**
 * Product Box Block — Extra functionality.
 *
 * Registers the custom image size used by the product box block.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register 550×550 cropped image size for product box images.
 */
function acf_product_box_register_image_size() {
    add_image_size( 'product-box-image', 550, 550, true );
}
add_action( 'after_setup_theme', 'acf_product_box_register_image_size' );

/**
 * Resolve the best image source for a product box.
 *
 * Priority:
 * 1. Direct external URL → use as-is
 * 2. Direct URL from same domain → find attachment, serve 550×550 if available
 * 3. ACF image array (with ID) → serve 550×550, fallback to medium
 *
 * @param array|false  $image     ACF image array (or false).
 * @param string       $image_url Direct image URL (or empty).
 * @param string       $alt       Fallback alt text.
 * @return array{src: string, alt: string}
 */
function acf_product_box_resolve_image( $image, $image_url, $alt = 'Product image' ) {
    $result = [ 'src' => '', 'alt' => $alt ];

    // Case 1: Direct URL provided
    if ( $image_url ) {
        $result['src'] = $image_url;

        // Check if it's a same-domain URL — try to get the attachment ID
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $url_host  = wp_parse_url( $image_url, PHP_URL_HOST );

        // Match domain or subdomain (e.g. cdn.example.com matches example.com)
        if ( $url_host && $site_host && ( $url_host === $site_host || str_ends_with( $url_host, '.' . $site_host ) ) ) {
            $attachment_id = attachment_url_to_postid( $image_url );
            if ( $attachment_id ) {
                $sized = wp_get_attachment_image_src( $attachment_id, 'product-box-image' );
                if ( $sized ) {
                    $result['src'] = $sized[0];
                } else {
                    $medium = wp_get_attachment_image_src( $attachment_id, 'medium' );
                    if ( $medium ) {
                        $result['src'] = $medium[0];
                    }
                }
                $img_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                if ( $img_alt ) {
                    $result['alt'] = $img_alt;
                }
            }
        }
        // External URL: use as-is (no size manipulation)
        return $result;
    }

    // Case 2: ACF image field (returns array with 'ID', 'url', 'alt', 'sizes')
    if ( $image && ! empty( $image['ID'] ) ) {
        $attachment_id = $image['ID'];
        $result['alt'] = ! empty( $image['alt'] ) ? $image['alt'] : $alt;

        // Try product-box-image size first, then medium
        $sized = wp_get_attachment_image_src( $attachment_id, 'product-box-image' );
        if ( $sized ) {
            $result['src'] = $sized[0];
        } else {
            $medium = wp_get_attachment_image_src( $attachment_id, 'medium' );
            $result['src'] = $medium ? $medium[0] : $image['url'];
        }
        return $result;
    }

    return $result;
}
