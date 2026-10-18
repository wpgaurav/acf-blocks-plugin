<?php
/** Standalone, offline integration fixture using the actual render templates. */
require dirname( __DIR__ ) . '/bootstrap.php';
function is_admin() { return false; }
function sanitize_title( $text ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) ), '-' ); }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function get_permalink( $id ) { return 'https://example.test/post-' . $id; }
function wp_json_encode( $data, $options = 0 ) { return json_encode( $data, $options ); }
require dirname( __DIR__, 2 ) . '/blocks/toc-block/extra.php';
$standalone = ( $argv[1] ?? '' ) === 'standalone';
$post_id = 17;
$is_preview = false;
$block = array( 'id' => 'contents', 'data' => array( 'toc_heading_levels' => array( 'h2', 'h3' ), 'toc_include_acf_block_headings' => false, 'toc_highlight_active' => true, 'toc_schema' => true ) );
if ( $standalone ) {
    $block['data']['toc_list_type'] = 'ol';
    $block['data']['toc_list_class'] = 'sidebar-list';
    $block['data']['toc_link_class'] = 'sidebar-link';
}
ob_start();
require dirname( __DIR__, 2 ) . '/blocks/toc-block/toc-block.php';
$nav = ob_get_clean();
$block['id'] = 'including-acf';
$block['data']['toc_include_acf_block_headings'] = true;
if ( $standalone ) {
    $block['data']['toc_list_type'] = 'plain';
}
ob_start();
require dirname( __DIR__, 2 ) . '/blocks/toc-block/toc-block.php';
$nav .= ob_get_clean();
$heading_html = '<div id="intro"></div><h2>Intro</h2><div class="acf-block"><h2>Intro</h2></div><h2 id="intro-2">Custom anchor</h2><div class="wp-block-html"><h2>HTML heading</h2></div><section class="wp-block-pattern"><h3>Synced heading</h3></section>';
$rendered = $standalone ? $heading_html : acf_toc_add_heading_ids( $nav . $heading_html );
$svg = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="600" height="400" fill="#164e63"/><circle cx="300" cy="200" r="100" fill="#a5f3fc"/></svg>' );
$block = array( 'data' => array( 'acf_gallery_images' => array( array( 'url' => $svg, 'alt' => 'Blue circle' ) ), 'acf_gallery_enable_lightbox' => true ) );
ob_start();
require dirname( __DIR__, 2 ) . '/blocks/gallery-block/gallery-block.php';
$gallery = ob_get_clean();
$block['data']['acf_gallery_enable_lightbox'] = false;
ob_start();
require dirname( __DIR__, 2 ) . '/blocks/gallery-block/gallery-block.php';
$disabled_gallery = ob_get_clean();
echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>TOC and gallery offline integration</title><style>body{font:16px system-ui;margin:2rem auto;max-width:1000px}main{display:grid;gap:1rem}nav{margin-bottom:1rem}h2,h3{margin-top:2rem}';
echo file_get_contents( dirname( __DIR__, 2 ) . '/blocks/toc-block/toc-block.css' );
echo file_get_contents( dirname( __DIR__, 2 ) . '/blocks/gallery-block/gallery.css' );
echo '</style><h2 id="theme-heading">Theme title outside content</h2>' . ( $standalone ? '<aside>' . $nav . '</aside>' : '' ) . '<main><article class="entry-content">' . $rendered . '<section id="enabled-gallery">' . $gallery . '</section><section id="second-gallery">' . $gallery . '</section><section id="disabled-gallery">' . $disabled_gallery . '</section></article></main><script>';
echo file_get_contents( dirname( __DIR__, 2 ) . '/blocks/toc-block/toc.js' );
echo '</script><script>' . file_get_contents( dirname( __DIR__, 2 ) . '/blocks/gallery-block/gallery.js' ) . '</script></html>';
