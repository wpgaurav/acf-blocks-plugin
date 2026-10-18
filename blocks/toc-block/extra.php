<?php
/**
 * Build TOCs and heading anchors from one inventory of final rendered content.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Tokenize attributes so id-like text inside another value stays ordinary text. */
function acf_toc_html_attributes( $attributes ) {
    if ( isset( $attributes[0] ) && '<' === $attributes[0] && preg_match( '/^<[a-z][a-z0-9:-]*\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>/i', $attributes, $opening ) ) {
        $attributes = $opening[1];
    }
    preg_match_all( '/(?:^|\s+)([^\s=\/>]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+)))?/', $attributes, $matches, PREG_SET_ORDER );
    $parsed = array();
    foreach ( $matches as $match ) {
        $name = strtolower( $match[1] );
        if ( array_key_exists( $name, $parsed ) ) {
            continue; // Browsers honor the first occurrence of an attribute.
        }
        $value = '';
        foreach ( array( 2, 3, 4 ) as $index ) {
            if ( isset( $match[ $index ] ) && '' !== $match[ $index ] ) {
                $value = html_entity_decode( $match[ $index ], ENT_QUOTES, 'UTF-8' );
                break;
            }
        }
        $parsed[ $name ] = $value;
    }
    return $parsed;
}

/** Read a quoted, unquoted or boolean HTML attribute. */
function acf_toc_html_attribute( $attributes, $name ) {
    $parsed = acf_toc_html_attributes( $attributes );
    return $parsed[ strtolower( $name ) ] ?? '';
}

/**
 * Inventory headings while retaining byte offsets, so rewriting IDs never
 * reserializes unrelated HTML/SVG or mistakes heading examples for real tags.
 *
 * @return array Heading records and IDs reserved by all rendered elements.
 */
function acf_toc_rendered_inventory( $html ) {
    $tag_attributes = '(?:[^>"\']|"[^"]*"|\'[^\']*\')*';
    $pattern = '~<!--.*?(?:-->|\z)|<(script|style|textarea|title|template)\b' . $tag_attributes . '>.*?</\1\s*>|</?([a-z][a-z0-9:-]*)\b' . $tag_attributes . '>~is';
    preg_match_all( $pattern, $html, $tokens, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
    $stack = array();
    $headings = array();
    $reserved = array();
    $nonheading_ids = array();
    $void = array( 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr' );

    foreach ( $tokens as $token ) {
        $markup = $token[0][0];
        $offset = $token[0][1];
        if ( 0 === strpos( $markup, '<!--' ) ) {
            continue;
        }
        if ( ! preg_match( '/^<(\/?)([a-z][a-z0-9:-]*)\b(' . $tag_attributes . ')>/is', $markup, $parts ) ) {
            continue;
        }
        $tag = strtolower( $parts[2] );
        if ( '/' === $parts[1] ) {
            for ( $index = count( $stack ) - 1; $index >= 0; $index-- ) {
                if ( $stack[ $index ]['tag'] !== $tag ) {
                    continue;
                }
                $open = $stack[ $index ];
                if ( isset( $open['heading'] ) ) {
                    $headings[ $open['heading'] ]['text'] = html_entity_decode(
                        wp_strip_all_tags( substr( $html, $open['end'], $offset - $open['end'] ) ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                }
                $stack = array_slice( $stack, 0, $index );
                break;
            }
            continue;
        }
        $attributes = $parts[3];
        $parsed_attributes = acf_toc_html_attributes( $attributes );
        $id = $parsed_attributes['id'] ?? '';
        $is_heading = (bool) preg_match( '/^h[1-6]$/', $tag );
        if ( '' !== $id ) {
            $reserved[ $id ] = true;
            if ( ! $is_heading ) {
                $nonheading_ids[ $id ] = true;
            }
        }
        $classes = preg_split( '/\s+/', acf_toc_html_attribute( $attributes, 'class' ) );
        $parent = empty( $stack ) ? array() : end( $stack );
        $entry = array(
            'tag' => $tag,
            'end' => $offset + strlen( $markup ),
            'acf' => ! empty( $parent['acf'] ) || in_array( 'acf-block', $classes, true ) || array_key_exists( 'data-acf-block', $parsed_attributes ),
        );
        if ( $is_heading ) {
            $entry['heading'] = count( $headings );
            $headings[] = array( 'tag' => $tag, 'level' => (int) substr( $tag, 1 ), 'id' => $id, 'text' => '', 'acf' => $entry['acf'] || '1' === acf_toc_html_attribute( $attributes, 'data-acf-toc-heading' ), 'offset' => $offset, 'opening' => $markup );
        }
        // Raw-text/template tokens include their closing tag and are atomic.
        if ( ! in_array( $tag, $void, true ) && ! preg_match( '~/\s*>$~', $markup ) && empty( $token[1][0] ) ) {
            $stack[] = $entry;
        }
    }
    return array( $headings, $reserved, $nonheading_ids );
}

/**
 * Mark actual ACF-rendered headings, including templates with multiple roots.
 * Wrapper ancestry remains a compatibility fallback for cached rendered HTML.
 */
function acf_toc_mark_acf_headings( $html, $block ) {
    if ( is_admin() || false === stripos( $html, '<h' ) || empty( $block['blockName'] ) || 0 !== strpos( $block['blockName'], 'acf/' ) ) {
        return $html;
    }
    list( $headings ) = acf_toc_rendered_inventory( $html );
    foreach ( array_reverse( $headings ) as $heading ) {
        if ( '1' === acf_toc_html_attribute( $heading['opening'], 'data-acf-toc-heading' ) ) {
            continue;
        }
        $opening = preg_replace( '/^(<h[1-6])\b/i', '$1 data-acf-toc-heading="1"', $heading['opening'], 1 );
        $html = substr_replace( $html, $opening, $heading['offset'], strlen( $heading['opening'] ) );
    }
    return $html;
}
add_filter( 'render_block', 'acf_toc_mark_acf_headings', 20, 2 );

/**
 * Assign collision-free anchors, then build every list from those exact nodes.
 * Runs after do_blocks, shortcodes and normal content formatting.
 */
function acf_toc_add_heading_ids( $content ) {
    if ( is_admin() || '' === $content || false === strpos( $content, 'acf-toc' ) || ! function_exists( 'acf_toc_build_list' ) ) {
        return $content;
    }
    $protected = array();
    $content = preg_replace_callback(
        '~(?:<!--.*?(?:-->|\z)|<(?P<raw>script|style|textarea|title|template)\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>.*?</(?P=raw)\s*>)(*SKIP)(*F)|<nav\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>.*?</nav\s*>~is',
        function( $match ) use ( &$protected ) {
            if ( ! in_array( 'acf-toc', preg_split( '/\s+/', acf_toc_html_attribute( $match[0], 'class' ) ), true ) ) {
                return $match[0];
            }
            $key = '<!--acf-toc-protected-' . count( $protected ) . '-->';
            $protected[ $key ] = $match[0];
            return $key;
        },
        $content
    );
    if ( empty( $protected ) ) {
        return $content;
    }
    list( $headings, $reserved, $used ) = acf_toc_rendered_inventory( $content );
    foreach ( $protected as $nav ) {
        list( , $nav_ids ) = acf_toc_rendered_inventory( $nav );
        $reserved += $nav_ids;
        $used += $nav_ids;
    }
    $replacements = array();
    foreach ( $headings as $index => &$heading ) {
        $existing = $heading['id'];
        $id = $existing;
        if ( '' === $id || isset( $used[ $id ] ) ) {
            $base = '' !== $existing ? $existing : sanitize_title( $heading['text'] );
            if ( '' === $base ) {
                $base = 'heading-' . ( $index + 1 );
            }
            $id = $base;
            $counter = 2;
            while ( isset( $reserved[ $id ] ) || isset( $used[ $id ] ) ) {
                $id = $base . '-' . $counter++;
            }
        }
        $used[ $id ] = true;
        $heading['id'] = $id;
        if ( $existing !== $id ) {
            $opening = $heading['opening'];
            $opening = preg_replace_callback( '/\s+([^\s=\/>]+)(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))?/', function( $attribute ) {
                return 'id' === strtolower( $attribute[1] ) ? '' : $attribute[0];
            }, $opening );
            $opening = preg_replace_callback( '/^(<h[1-6])\b/i', function( $match ) use ( $id ) {
                return $match[1] . ' id="' . esc_attr( $id ) . '"';
            }, $opening, 1 );
            $replacements[] = array( $heading['offset'], strlen( $heading['opening'] ), $opening );
        }
    }
    unset( $heading );
    foreach ( array_reverse( $replacements ) as $replacement ) {
        $content = substr_replace( $content, $replacement[2], $replacement[0], $replacement[1] );
    }
    foreach ( $protected as $key => $nav ) {
        $levels = explode( ',', acf_toc_html_attribute( $nav, 'data-heading-levels' ) ?: 'h2' );
        $include_acf = '1' === acf_toc_html_attribute( $nav, 'data-include-acf-headings' );
        $selected = array_values( array_filter( $headings, function( $heading ) use ( $levels, $include_acf ) {
            return in_array( $heading['tag'], $levels, true ) && ( $include_acf || ! $heading['acf'] );
        } ) );
        if ( empty( $selected ) ) {
            $protected[ $key ] = '';
            continue;
        }
        $nav = str_replace( ' data-toc-finalized="0"', ' data-toc-finalized="1"', $nav );
        $mode = acf_toc_html_attribute( $nav, 'data-list-type' ) ?: 'ul';
        $list = acf_toc_build_list( $selected, $mode, acf_toc_html_attribute( $nav, 'data-list-class' ), acf_toc_html_attribute( $nav, 'data-link-class' ), 'plain' === $mode );
        $nav = preg_replace_callback( '/<!--acf-toc-list:start-->.*?<!--acf-toc-list:end-->|<!--acf-toc-list-->/s', function() use ( $list ) {
            return '<!--acf-toc-list:start-->' . $list . '<!--acf-toc-list:end-->';
        }, $nav );
        $schema = '1' === acf_toc_html_attribute( $nav, 'data-toc-schema' )
            ? acf_toc_generate_schema( $selected, (int) acf_toc_html_attribute( $nav, 'data-toc-post-id' ) ) : '';
        $nav = preg_replace_callback( '/<!--acf-toc-schema:start-->.*?<!--acf-toc-schema:end-->/s', function() use ( $schema ) {
            return '<!--acf-toc-schema:start-->' . $schema . '<!--acf-toc-schema:end-->';
        }, $nav );
        $protected[ $key ] = $nav;
    }
    return strtr( $content, $protected );
}
add_filter( 'the_content', 'acf_toc_add_heading_ids', 99 );
