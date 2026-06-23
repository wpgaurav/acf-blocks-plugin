<?php
/**
 * ACF Blocks — Block Recovery
 *
 * Repairs the "This block contains unexpected or invalid content" /
 * "Attempt Recovery" error that affects ACF InnerBlocks (jsx) blocks whose
 * inner content was saved as undelimited raw HTML instead of properly
 * delimited inner blocks.
 *
 * How the corruption happens
 * --------------------------
 * ACF blocks that declare `supports.jsx` (Callout, CTA, Hero, Section, …)
 * render an <InnerBlocks /> area. WordPress expects that area to contain
 * nested block markup, e.g.:
 *
 *     <!-- wp:acf/callout {...} -->
 *     <!-- wp:paragraph --><p>Text</p><!-- /wp:paragraph -->
 *     <!-- /wp:acf/callout -->
 *
 * When content is inserted programmatically (REST publishing, imports, AI
 * tools) the inner block delimiters are sometimes omitted, leaving raw HTML
 * directly inside the block body:
 *
 *     <!-- wp:acf/callout {...} -->
 *     <p>Text</p>
 *     <!-- /wp:acf/callout -->
 *
 * The block parser stores that `<p>` in the block's own innerHTML with an
 * empty innerBlocks array. The editor sees an InnerBlocks block whose body is
 * not valid block markup, marks it invalid, and offers "Attempt Recovery".
 * Because the block is dynamic, recovery rebuilds it from the ACF template's
 * default InnerBlocks template — wiping the author's content.
 *
 * The fix
 * -------
 * This module re-wraps that orphaned HTML into proper core blocks
 * (paragraph, heading, list, quote, …) so the markup validates and no
 * recovery prompt is ever shown.
 *
 * Two delivery paths, both idempotent:
 *   1. Live self-heal — filters the REST `content.raw` served to the block
 *      editor (context=edit) so existing posts open cleanly with zero action.
 *      Saving the post then persists the repaired markup.
 *   2. Permanent bulk repair — `wp acf-blocks repair-content` rewrites the
 *      stored content of every affected post in the database.
 *
 * @package ACF_Blocks
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the set of ACF block names that use InnerBlocks (supports.jsx).
 *
 * Derived from each block.json so the list stays correct as blocks are
 * added or removed. Result is cached for the request.
 *
 * @return string[] Block names, e.g. array( 'acf/callout', 'acf/cta' ).
 */
function acf_blocks_recovery_innerblock_block_names() {
    static $names = null;

    if ( null !== $names ) {
        return $names;
    }

    $names = array();

    if ( ! function_exists( 'acf_blocks_get_block_metadata_cache' ) ) {
        return $names;
    }

    foreach ( acf_blocks_get_block_metadata_cache() as $block_info ) {
        $metadata = $block_info['metadata'];

        if ( empty( $metadata['name'] ) ) {
            continue;
        }

        $supports_jsx = ! empty( $metadata['supports']['jsx'] );
        $acf_jsx       = ! empty( $metadata['acf']['jsx'] );

        if ( $supports_jsx || $acf_jsx ) {
            $names[] = $metadata['name'];
        }
    }

    // Also consult the live block registry. ACF can enable InnerBlocks (jsx)
    // support at registration time even when block.json does not declare it,
    // so the registry is the authoritative runtime source. Union the two so
    // no InnerBlocks block is missed. Treating a block as an InnerBlocks
    // container is harmless if it never carries inner content — the repair
    // only acts when orphaned HTML is actually present.
    if ( class_exists( 'WP_Block_Type_Registry' ) ) {
        $registry = WP_Block_Type_Registry::get_instance();
        foreach ( $registry->get_all_registered() as $block_name => $block_type ) {
            if ( 0 !== strpos( $block_name, 'acf/' ) ) {
                continue;
            }
            $supports = (array) ( isset( $block_type->supports ) ? $block_type->supports : array() );
            if ( ! empty( $supports['jsx'] ) && ! in_array( $block_name, $names, true ) ) {
                $names[] = $block_name;
            }
        }
    }

    /**
     * Filter the list of ACF block names treated as InnerBlocks containers
     * for recovery purposes.
     *
     * @param string[] $names Block names that use InnerBlocks.
     */
    $names = apply_filters( 'acf_blocks_recovery_innerblock_names', $names );

    return $names;
}

/**
 * Convert a fragment of orphaned freeform HTML into an array of block arrays.
 *
 * Recognises the common top-level elements found in callout/CTA/hero bodies
 * (paragraphs, headings, lists, blockquotes) and maps each to its core block.
 * Anything unrecognised is preserved verbatim inside a core/html block, which
 * always validates — so no content is ever lost.
 *
 * @param string $html Raw HTML fragment.
 * @return array[] Parsed block arrays suitable for serialize_blocks().
 */
function acf_blocks_recovery_html_to_blocks( $html ) {
    $html = trim( $html );

    if ( '' === $html ) {
        return array();
    }

    if ( ! class_exists( 'DOMDocument' ) ) {
        // No DOM extension: keep content safe in a single HTML block.
        return array( acf_blocks_recovery_make_block( 'core/html', array(), "\n" . $html . "\n" ) );
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML(
        '<?xml encoding="UTF-8"><div id="acf-blocks-recovery-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $root = $dom->getElementById( 'acf-blocks-recovery-root' );

    if ( ! $root ) {
        return array( acf_blocks_recovery_make_block( 'core/html', array(), "\n" . $html . "\n" ) );
    }

    $blocks  = array();
    $pending = '';

    // Flush buffered loose text/inline nodes into a paragraph block.
    $flush = function () use ( &$pending, &$blocks ) {
        $text    = trim( $pending );
        $pending = '';
        if ( '' !== $text ) {
            $blocks[] = acf_blocks_recovery_make_block( 'core/paragraph', array(), "\n<p>" . $text . "</p>\n" );
        }
    };

    foreach ( iterator_to_array( $root->childNodes ) as $node ) {
        if ( XML_TEXT_NODE === $node->nodeType ) {
            $pending .= $node->textContent;
            continue;
        }

        if ( XML_ELEMENT_NODE !== $node->nodeType ) {
            continue;
        }

        $tag   = strtolower( $node->nodeName );
        $outer = $dom->saveHTML( $node );

        switch ( $tag ) {
            case 'p':
                $flush();
                $blocks[] = acf_blocks_recovery_make_block( 'core/paragraph', array(), "\n" . $outer . "\n" );
                break;

            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                $flush();
                $level = (int) substr( $tag, 1 );
                $attrs = ( 2 === $level ) ? array() : array( 'level' => $level );
                $inner = $outer;
                if ( false === strpos( $inner, 'wp-block-heading' ) ) {
                    $inner = preg_replace( '/^<h([1-6])/', '<h$1 class="wp-block-heading"', $inner, 1 );
                }
                $blocks[] = acf_blocks_recovery_make_block( 'core/heading', $attrs, "\n" . $inner . "\n" );
                break;

            case 'ul':
            case 'ol':
                $flush();
                $attrs    = ( 'ol' === $tag ) ? array( 'ordered' => true ) : array();
                $blocks[] = acf_blocks_recovery_make_block( 'core/list', $attrs, "\n" . $outer . "\n" );
                break;

            case 'blockquote':
                $flush();
                $blocks[] = acf_blocks_recovery_make_block( 'core/quote', array(), "\n" . $outer . "\n" );
                break;

            default:
                // figure/img/table/div/etc. — preserve verbatim, never lose data.
                $flush();
                $blocks[] = acf_blocks_recovery_make_block( 'core/html', array(), "\n" . $outer . "\n" );
                break;
        }
    }

    $flush();

    return $blocks;
}

/**
 * Build a minimal parsed-block array for a leaf (no inner blocks) block.
 *
 * @param string $name  Block name (e.g. 'core/paragraph').
 * @param array  $attrs Block attributes.
 * @param string $html  Inner HTML (with surrounding newlines).
 * @return array Parsed block array compatible with serialize_block().
 */
function acf_blocks_recovery_make_block( $name, $attrs, $html ) {
    return array(
        'blockName'    => $name,
        'attrs'        => $attrs,
        'innerBlocks'  => array(),
        'innerHTML'    => $html,
        'innerContent' => array( $html ),
    );
}

/**
 * Normalize a single ACF InnerBlocks block, re-wrapping orphaned HTML.
 *
 * Walks the block's innerContent in order. String chunks are buffered as raw
 * HTML; null placeholders pull the matching existing inner block. When a
 * non-empty raw buffer is found it is converted to real blocks and spliced in
 * at the correct position. If nothing required conversion the original block
 * is returned untouched (so the operation is a no-op on healthy blocks).
 *
 * @param array $block Parsed ACF block array.
 * @return array The repaired (or original) block array.
 */
function acf_blocks_recovery_normalize_block( $block ) {
    if ( empty( $block['innerContent'] ) ) {
        return $block;
    }

    $existing   = isset( $block['innerBlocks'] ) ? $block['innerBlocks'] : array();
    $index      = 0;
    $new_blocks = array();
    $buffer     = '';
    $converted  = false;

    $flush = function () use ( &$buffer, &$new_blocks, &$converted ) {
        if ( '' === trim( $buffer ) ) {
            $buffer = '';
            return;
        }
        foreach ( acf_blocks_recovery_html_to_blocks( $buffer ) as $made ) {
            $new_blocks[] = $made;
        }
        $converted = true;
        $buffer    = '';
    };

    foreach ( $block['innerContent'] as $chunk ) {
        if ( is_string( $chunk ) ) {
            $buffer .= $chunk;
            continue;
        }

        // null placeholder -> the next existing inner block, in order.
        $flush();
        if ( isset( $existing[ $index ] ) ) {
            $new_blocks[] = $existing[ $index ];
        }
        $index++;
    }

    $flush();

    if ( ! $converted ) {
        return $block;
    }

    // Rebuild innerContent with newline glue and a null per inner block.
    $content = array( "\n" );
    foreach ( $new_blocks as $unused ) {
        $content[] = null;
        $content[] = "\n";
    }

    $block['innerBlocks']  = $new_blocks;
    $block['innerContent'] = $content;
    $block['innerHTML']    = "\n\n";

    return $block;
}

/**
 * Recursively repair a parsed block tree.
 *
 * @param array[] $blocks  Parsed blocks.
 * @param array   $names   ACF InnerBlocks block names lookup (name => true).
 * @param bool    $changed Set to true if any block was repaired.
 * @return array[] The repaired block tree.
 */
function acf_blocks_recovery_process_blocks( $blocks, $names, &$changed ) {
    $out = array();

    foreach ( $blocks as $block ) {
        $name = isset( $block['blockName'] ) ? $block['blockName'] : null;

        if ( $name && isset( $names[ $name ] ) ) {
            $before = $block;
            $block  = acf_blocks_recovery_normalize_block( $block );
            if ( $block !== $before ) {
                $changed = true;
            }
        }

        if ( ! empty( $block['innerBlocks'] ) ) {
            $block['innerBlocks'] = acf_blocks_recovery_process_blocks( $block['innerBlocks'], $names, $changed );
        }

        $out[] = $block;
    }

    return $out;
}

/**
 * Repair orphaned InnerBlocks HTML in a block of content.
 *
 * Safe to call on any content: it early-returns unchanged when there are no
 * ACF blocks, and is fully idempotent (running it twice yields identical
 * output).
 *
 * @param string $content Post content (block markup).
 * @param bool   $changed Optional. Set to true when a repair was applied.
 * @return string The repaired content (or the original, unchanged).
 */
function acf_blocks_recovery_repair_content( $content, &$changed = false ) {
    $changed = false;

    if ( ! is_string( $content ) || '' === $content ) {
        return $content;
    }

    // Fast path: nothing to do if no ACF block markup is present.
    if ( false === strpos( $content, 'wp:acf/' ) ) {
        return $content;
    }

    if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
        return $content;
    }

    $names = acf_blocks_recovery_innerblock_block_names();

    if ( empty( $names ) ) {
        return $content;
    }

    $lookup   = array_fill_keys( $names, true );
    $repaired = acf_blocks_recovery_process_blocks( parse_blocks( $content ), $lookup, $changed );

    if ( ! $changed ) {
        return $content;
    }

    return serialize_blocks( $repaired );
}

/*
 * --------------------------------------------------------------------------
 * Live self-heal: repair content served to the block editor.
 * --------------------------------------------------------------------------
 */

/**
 * Register REST filters that repair editor content on read.
 *
 * Hooks every post type that is editable via the block editor so the content
 * the editor loads is always valid — preventing the recovery prompt without
 * touching the database until the post is next saved.
 */
function acf_blocks_recovery_register_rest_filters() {
    $post_types = get_post_types( array( 'show_in_rest' => true ), 'names' );

    foreach ( $post_types as $post_type ) {
        add_filter( "rest_prepare_{$post_type}", 'acf_blocks_recovery_filter_rest_response', 10, 3 );
    }
}
add_action( 'rest_api_init', 'acf_blocks_recovery_register_rest_filters' );

/**
 * Repair the raw content of an edit-context REST response.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Post          $post     The post.
 * @param WP_REST_Request  $request  The request.
 * @return WP_REST_Response The (possibly repaired) response.
 */
function acf_blocks_recovery_filter_rest_response( $response, $post, $request ) {
    if ( 'edit' !== $request->get_param( 'context' ) ) {
        return $response;
    }

    $data = $response->get_data();

    if ( empty( $data['content']['raw'] ) || ! is_string( $data['content']['raw'] ) ) {
        return $response;
    }

    $repaired = acf_blocks_recovery_repair_content( $data['content']['raw'], $changed );

    if ( $changed ) {
        $data['content']['raw'] = $repaired;
        $response->set_data( $data );
    }

    return $response;
}

/*
 * --------------------------------------------------------------------------
 * WP-CLI: permanently repair stored content.
 * --------------------------------------------------------------------------
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    /**
     * Repair ACF InnerBlocks "Attempt Recovery" content across the database.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Report what would change without writing to the database.
     *
     * [--post=<id>]
     * : Repair a single post by ID instead of scanning every post.
     *
     * [--post_type=<types>]
     * : Comma-separated post types to scan. Defaults to all block-editor types.
     *
     * [--post_status=<statuses>]
     * : Comma-separated post statuses to scan. Default: publish,draft,pending,private,future.
     *
     * ## EXAMPLES
     *
     *     wp acf-blocks repair-content --dry-run
     *     wp acf-blocks repair-content
     *     wp acf-blocks repair-content --post=1094375
     *
     * @when after_wp_load
     *
     * @param array $args       Positional args (unused).
     * @param array $assoc_args Named args.
     */
    function acf_blocks_recovery_cli_repair( $args, $assoc_args ) {
        $dry_run = isset( $assoc_args['dry-run'] );

        if ( isset( $assoc_args['post'] ) ) {
            $post_ids = array( (int) $assoc_args['post'] );
        } else {
            $post_types = isset( $assoc_args['post_type'] )
                ? array_map( 'trim', explode( ',', $assoc_args['post_type'] ) )
                : get_post_types( array( 'show_in_rest' => true ), 'names' );

            $statuses = isset( $assoc_args['post_status'] )
                ? array_map( 'trim', explode( ',', $assoc_args['post_status'] ) )
                : array( 'publish', 'draft', 'pending', 'private', 'future' );

            $post_ids = get_posts( array(
                'post_type'      => $post_types,
                'post_status'    => $statuses,
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
                's'              => '', // no search
            ) );
        }

        $scanned  = 0;
        $repaired = 0;

        foreach ( $post_ids as $post_id ) {
            $content = get_post_field( 'post_content', $post_id );

            if ( '' === $content ) {
                continue;
            }

            $scanned++;
            $new = acf_blocks_recovery_repair_content( $content, $changed );

            if ( ! $changed ) {
                continue;
            }

            $repaired++;

            if ( $dry_run ) {
                WP_CLI::log( sprintf( '[dry-run] Would repair post #%d (%s)', $post_id, get_the_title( $post_id ) ) );
                continue;
            }

            $result = wp_update_post( array(
                'ID'           => $post_id,
                'post_content' => wp_slash( $new ),
            ), true );

            if ( is_wp_error( $result ) ) {
                WP_CLI::warning( sprintf( 'Post #%d failed: %s', $post_id, $result->get_error_message() ) );
                $repaired--;
            } else {
                WP_CLI::log( sprintf( 'Repaired post #%d (%s)', $post_id, get_the_title( $post_id ) ) );
            }
        }

        $verb = $dry_run ? 'would be repaired' : 'repaired';
        WP_CLI::success( sprintf( '%d of %d post(s) scanned %s.', $repaired, $scanned, $verb ) );
    }

    WP_CLI::add_command( 'acf-blocks repair-content', 'acf_blocks_recovery_cli_repair' );
}
