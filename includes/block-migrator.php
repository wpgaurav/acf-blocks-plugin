<?php
/**
 * ACF Blocks — Block Migrator
 *
 * Migrates legacy / renamed ACF blocks to their current equivalents and
 * repairs block markup that the editor cannot parse, so old content stops
 * triggering "block unavailable" and "Attempt Recovery" errors.
 *
 * Covers three failure modes found in real content:
 *
 *   1. Orphaned InnerBlocks HTML — delegated to includes/block-recovery.php.
 *   2. Unparseable block comments — an ACF block whose attribute JSON contains
 *      a literal "-->" (e.g. inside FAQ answer text) which prematurely closes
 *      the HTML comment and corrupts the block. Repaired losslessly by brace-
 *      matching the JSON and HTML-encoding the stray "-->" to "--&gt;"
 *      (identical when rendered as HTML).
 *   3. Legacy / renamed block names — blocks saved under names this plugin no
 *      longer registers, remapped to the current block with its field schema:
 *
 *        acf/table-of-contents  -> acf/toc
 *        acf/table-of-content   -> acf/toc
 *        acf/productbox         -> acf/product-box
 *        acf/accordion-item     -> acf/accordion   (consecutive items merged)
 *        acf/accordion-group    -> acf/accordion   (wrapper + children merged)
 *        acf/acf-accordion      -> acf/accordion   (sub-field rename)
 *        acf/poll               -> (no equivalent — reported, left untouched)
 *
 * A visual Migrator is added to the ACF Blocks options page (Settings → ACF
 * Blocks License). Every applied change goes through wp_update_post(), which
 * stores a revision, so migrations are reversible.
 *
 * @package ACF_Blocks
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Map of legacy block names to a migration handler key.
 *
 * @return array<string,string>
 */
function acf_blocks_migrator_legacy_types() {
    return array(
        'acf/table-of-contents' => 'toc',
        'acf/table-of-content'  => 'toc',
        'acf/productbox'        => 'productbox',
        'acf/accordion-item'    => 'accordion_item',
        'acf/accordion-group'   => 'accordion_group',
        'acf/acf-accordion'     => 'acf_accordion',
        'acf/poll'              => 'unsupported',
    );
}

/*
 * --------------------------------------------------------------------------
 * Block builders
 * --------------------------------------------------------------------------
 */

/**
 * Build a self-closing ACF block (no InnerBlocks) parsed-block array.
 *
 * @param string $name Block name (e.g. 'acf/accordion').
 * @param array  $data ACF flat data array (name/value + _name/key pairs).
 * @return array Parsed block array for serialize_block().
 */
function acf_blocks_migrator_make_acf_block( $name, $data ) {
    return array(
        'blockName'    => $name,
        'attrs'        => array(
            'name' => $name,
            'data' => $data,
            'mode' => 'preview',
        ),
        'innerBlocks'  => array(),
        'innerHTML'    => '',
        'innerContent' => array(),
    );
}

/**
 * Build current accordion block data from an ordered list of Q/A rows.
 *
 * @param array[] $rows       Each row: array( 'title' => string, 'content' => string ).
 * @param bool    $faq_schema Whether to enable FAQ schema output.
 * @return array ACF flat data array.
 */
function acf_blocks_migrator_build_accordion_data( $rows, $faq_schema ) {
    $rows = array_values( $rows );
    $data = array(
        'acf_accord_enable_faq_schema'  => $faq_schema ? '1' : '0',
        '_acf_accord_enable_faq_schema' => 'field_acf_accord_enable_faq_schema',
        'acf_accord_groups'             => (string) count( $rows ),
        '_acf_accord_groups'            => 'field_acf_accord_groups',
    );

    foreach ( $rows as $idx => $row ) {
        $data[ "acf_accord_groups_{$idx}_acf_accord_group_title" ]    = (string) $row['title'];
        $data[ "_acf_accord_groups_{$idx}_acf_accord_group_title" ]   = 'field_acf_accord_group_title';
        $data[ "acf_accord_groups_{$idx}_acf_accord_group_content" ]  = (string) $row['content'];
        $data[ "_acf_accord_groups_{$idx}_acf_accord_group_content" ] = 'field_acf_accord_group_content';
    }

    return $data;
}

/*
 * --------------------------------------------------------------------------
 * Per-type transforms
 * --------------------------------------------------------------------------
 */

/**
 * acf/table-of-contents | acf/table-of-content -> acf/toc.
 *
 * The current TOC auto-generates from headings, so only a custom title is
 * carried over; everything else falls back to the new block's defaults.
 *
 * @param array $block Legacy parsed block.
 * @return array New acf/toc block.
 */
function acf_blocks_migrator_transform_toc( $block ) {
    $d     = (array) ( $block['attrs']['data'] ?? array() );
    $title = isset( $d['acf_toc_title'] ) ? (string) $d['acf_toc_title'] : '';
    $data  = array();

    if ( '' !== trim( $title ) ) {
        $data['toc_title']  = $title;
        $data['_toc_title'] = 'field_toc_title';
    }

    return acf_blocks_migrator_make_acf_block( 'acf/toc', $data );
}

/**
 * acf/productbox -> acf/product-box (field_pb_* -> pb_*, features repeater).
 *
 * @param array $block Legacy parsed block.
 * @return array New acf/product-box block.
 */
function acf_blocks_migrator_transform_productbox( $block ) {
    $d    = (array) ( $block['attrs']['data'] ?? array() );
    $data = array();

    $simple = array(
        'field_pb_title'       => 'pb_title',
        'field_pb_image'       => 'pb_image',
        'field_pb_url'         => 'pb_title_url',
        'field_pb_description' => 'pb_description',
    );
    foreach ( $simple as $old => $new ) {
        if ( isset( $d[ $old ] ) && '' !== (string) $d[ $old ] ) {
            $data[ $new ]        = $d[ $old ];
            $data[ '_' . $new ]  = 'field_' . $new;
        }
    }

    // Legacy features: {"row-0":{"field_pb_feature_text":"..."}, ...}
    $rows = array();
    if ( isset( $d['field_pb_features'] ) && is_array( $d['field_pb_features'] ) ) {
        foreach ( $d['field_pb_features'] as $row ) {
            if ( is_array( $row ) && isset( $row['field_pb_feature_text'] ) && '' !== (string) $row['field_pb_feature_text'] ) {
                $rows[] = (string) $row['field_pb_feature_text'];
            }
        }
    }

    $data['pb_features']  = (string) count( $rows );
    $data['_pb_features'] = 'field_pb_features';
    foreach ( $rows as $i => $text ) {
        $data[ "pb_features_{$i}_pb_feature_text" ]  = $text;
        $data[ "_pb_features_{$i}_pb_feature_text" ] = 'field_pb_feature_text';
    }

    return acf_blocks_migrator_make_acf_block( 'acf/product-box', $data );
}

/**
 * acf/acf-accordion -> acf/accordion (acc_question/acc_answer sub-fields).
 *
 * @param array $block Legacy parsed block.
 * @return array New acf/accordion block.
 */
function acf_blocks_migrator_transform_acf_accordion( $block ) {
    $d    = (array) ( $block['attrs']['data'] ?? array() );
    $rows = array();

    foreach ( $d as $k => $v ) {
        if ( preg_match( '/^acf_accord_groups_(\d+)_acc_question$/', $k, $m ) ) {
            $rows[ (int) $m[1] ]['title'] = (string) $v;
        } elseif ( preg_match( '/^acf_accord_groups_(\d+)_acc_answer$/', $k, $m ) ) {
            $rows[ (int) $m[1] ]['content'] = (string) $v;
        }
    }

    ksort( $rows );
    $clean = array();
    foreach ( $rows as $r ) {
        $clean[] = array(
            'title'   => isset( $r['title'] ) ? $r['title'] : '',
            'content' => isset( $r['content'] ) ? $r['content'] : '',
        );
    }

    $faq = ! empty( $d['acf_accord_enable_faq_schema'] ) || ! isset( $d['acf_accord_enable_faq_schema'] );

    return acf_blocks_migrator_make_acf_block(
        'acf/accordion',
        acf_blocks_migrator_build_accordion_data( $clean, $faq )
    );
}

/**
 * Extract the two text values (title, content) from a legacy accordion-item.
 *
 * Item data uses opaque field-key names, so values are read positionally:
 * first non-meta value is the question, second is the answer.
 *
 * @param array $block Legacy acf/accordion-item block.
 * @return array array( 'title' => string, 'content' => string )
 */
function acf_blocks_migrator_accordion_item_row( $block ) {
    $d   = (array) ( $block['attrs']['data'] ?? array() );
    $vals = array();
    foreach ( $d as $k => $v ) {
        if ( is_string( $k ) && 0 === strpos( $k, '_' ) ) {
            continue; // skip _field meta keys
        }
        $vals[] = (string) $v;
    }
    return array(
        'title'   => isset( $vals[0] ) ? $vals[0] : '',
        'content' => isset( $vals[1] ) ? $vals[1] : '',
    );
}

/**
 * Recursively transform a list of blocks, applying legacy migrations.
 *
 * Handles sibling-run merging for acf/accordion-item.
 *
 * @param array[] $blocks  Parsed blocks.
 * @param array   $stats   Per-type counters (by reference).
 * @param bool    $changed Set true when a real (content-altering) migration runs.
 * @return array[] Transformed blocks.
 */
function acf_blocks_migrator_transform_list( $blocks, &$stats, &$changed ) {
    $out = array();
    $n   = count( $blocks );
    $i   = 0;

    while ( $i < $n ) {
        $block = $blocks[ $i ];
        $name  = isset( $block['blockName'] ) ? $block['blockName'] : null;

        switch ( $name ) {
            case 'acf/accordion-item':
                $rows = array();
                while ( $i < $n && ( $blocks[ $i ]['blockName'] ?? null ) === 'acf/accordion-item' ) {
                    $rows[] = acf_blocks_migrator_accordion_item_row( $blocks[ $i ] );
                    $i++;
                }
                $out[] = acf_blocks_migrator_make_acf_block(
                    'acf/accordion',
                    acf_blocks_migrator_build_accordion_data( $rows, true )
                );
                $stats['accordion-item'] = ( $stats['accordion-item'] ?? 0 ) + count( $rows );
                $changed = true;
                continue 2;

            case 'acf/accordion-group':
                $faq  = ! empty( $block['attrs']['data']['faq_schema'] );
                $rows = array();
                foreach ( (array) ( $block['innerBlocks'] ?? array() ) as $child ) {
                    if ( ( $child['blockName'] ?? '' ) === 'acf/accordion' ) {
                        $cd     = (array) ( $child['attrs']['data'] ?? array() );
                        $rows[] = array(
                            'title'   => isset( $cd['question'] ) ? (string) $cd['question'] : '',
                            'content' => isset( $cd['answer'] ) ? (string) $cd['answer'] : '',
                        );
                    }
                }
                $out[] = acf_blocks_migrator_make_acf_block(
                    'acf/accordion',
                    acf_blocks_migrator_build_accordion_data( $rows, $faq )
                );
                $stats['accordion-group'] = ( $stats['accordion-group'] ?? 0 ) + 1;
                $changed = true;
                $i++;
                continue 2;

            case 'acf/acf-accordion':
                $out[] = acf_blocks_migrator_transform_acf_accordion( $block );
                $stats['acf-accordion'] = ( $stats['acf-accordion'] ?? 0 ) + 1;
                $changed = true;
                $i++;
                continue 2;

            case 'acf/table-of-contents':
            case 'acf/table-of-content':
                $out[] = acf_blocks_migrator_transform_toc( $block );
                $stats['toc'] = ( $stats['toc'] ?? 0 ) + 1;
                $changed = true;
                $i++;
                continue 2;

            case 'acf/productbox':
                $out[] = acf_blocks_migrator_transform_productbox( $block );
                $stats['productbox'] = ( $stats['productbox'] ?? 0 ) + 1;
                $changed = true;
                $i++;
                continue 2;

            case 'acf/poll':
                $stats['poll-unsupported'] = ( $stats['poll-unsupported'] ?? 0 ) + 1;
                $out[] = $block; // left untouched
                $i++;
                continue 2;
        }

        if ( ! empty( $block['innerBlocks'] ) ) {
            $block['innerBlocks'] = acf_blocks_migrator_transform_list( $block['innerBlocks'], $stats, $changed );
        }

        $out[] = $block;
        $i++;
    }

    return $out;
}

/*
 * --------------------------------------------------------------------------
 * Unparseable-comment repair (string level, runs before parse_blocks)
 * --------------------------------------------------------------------------
 */

/**
 * Find the index of the brace matching the '{' at $start, honouring JSON
 * strings and escapes.
 *
 * @param string $s     Haystack.
 * @param int    $start Index of the opening '{'.
 * @return int Index of the matching '}', or -1 if unbalanced.
 */
function acf_blocks_migrator_match_brace( $s, $start ) {
    $depth  = 0;
    $in_str = false;
    $esc    = false;
    $len    = strlen( $s );

    for ( $i = $start; $i < $len; $i++ ) {
        $ch = $s[ $i ];

        if ( $in_str ) {
            if ( $esc ) {
                $esc = false;
            } elseif ( '\\' === $ch ) {
                $esc = true;
            } elseif ( '"' === $ch ) {
                $in_str = false;
            }
            continue;
        }

        if ( '"' === $ch ) {
            $in_str = true;
        } elseif ( '{' === $ch ) {
            $depth++;
        } elseif ( '}' === $ch ) {
            $depth--;
            if ( 0 === $depth ) {
                return $i;
            }
        }
    }

    return -1;
}

/**
 * Remove orphaned closing block delimiters (a "<!-- /wp:x -->" with no
 * matching opener). A stray closer — most often "<!-- /wp:post-content -->"
 * left over from a template paste — makes WordPress treat everything after it
 * as freeform text, hiding real blocks behind "Attempt Recovery".
 *
 * Uses a delimiter stack so only genuinely unmatched closers are stripped;
 * correctly nested closers are preserved. Delimiters are located with strpos
 * and validated with a small regex on each short delimiter string, so there
 * is no catastrophic backtracking on large posts.
 *
 * @param string $content Post content.
 * @param int    $count   Closers removed (by reference).
 * @return string Repaired content.
 */
function acf_blocks_migrator_strip_orphaned_closers( $content, &$count ) {
    $count = 0;

    if ( ! is_string( $content ) || false === strpos( $content, '<!--' ) ) {
        return $content;
    }

    $stack  = array();
    $remove = array();
    $offset = 0;
    // Anchored, applied only to a single short delimiter at a time.
    $delim_re = '/^<!--\s+(\/)?wp:([a-z][a-z0-9_-]*(?:\/[a-z0-9_-]+)?)\s*(\{.*\})?\s*(\/)?-->$/s';

    while ( false !== ( $pos = strpos( $content, '<!--', $offset ) ) ) {
        $end = strpos( $content, '-->', $pos );
        if ( false === $end ) {
            break;
        }
        $delim  = substr( $content, $pos, $end + 3 - $pos );
        $offset = $end + 3;

        if ( ! preg_match( $delim_re, $delim, $m ) ) {
            continue; // not a block delimiter (plain HTML comment)
        }

        $is_closer = '/' === ( $m[1] ?? '' );
        $name      = $m[2];
        $is_void   = '/' === ( $m[4] ?? '' );

        if ( $is_closer ) {
            if ( ! empty( $stack ) && end( $stack ) === $name ) {
                array_pop( $stack );
            } else {
                // Unmatched closer — schedule for removal.
                $remove[] = array( $pos, strlen( $delim ) );
            }
            continue;
        }

        if ( ! $is_void ) {
            $stack[] = $name;
        }
    }

    if ( empty( $remove ) ) {
        return $content;
    }

    // Remove from the end backwards to keep offsets valid; also swallow one
    // trailing newline so the removal leaves no blank gap.
    usort( $remove, function ( $a, $b ) {
        return $b[0] - $a[0];
    } );

    foreach ( $remove as $r ) {
        list( $offset, $length ) = $r;
        if ( "\n" === substr( $content, $offset + $length, 1 ) ) {
            $length++;
        }
        $content = substr( $content, 0, $offset ) . substr( $content, $offset + $length );
        $count++;
    }

    return $content;
}

/**
 * Remove dangling block openers — a "<!-- wp:name" fragment that never closes
 * with "-->" before the next comment. These are truncated leftovers (the
 * block's data is already gone) that the editor renders as broken markup.
 *
 * @param string $content Post content.
 * @param int    $count   Fragments removed (by reference).
 * @return string Repaired content.
 */
function acf_blocks_migrator_strip_dangling_openers( $content, &$count ) {
    $count = 0;

    if ( ! is_string( $content ) || false === strpos( $content, '<!-- wp:' ) ) {
        return $content;
    }

    // Linear scan (no regex — avoids PCRE backtrack/JIT failures on large
    // posts, which would otherwise return null and wipe the content). For each
    // "<!-- wp:" opener, if the next comment delimiter starts before this
    // opener's "-->" closes, the opener is dangling and its fragment is dropped.
    $out    = '';
    $offset = 0;
    $len    = strlen( $content );

    while ( false !== ( $pos = strpos( $content, '<!-- wp:', $offset ) ) ) {
        $close = strpos( $content, '-->', $pos );
        $next  = strpos( $content, '<!--', $pos + 8 );

        $dangling = ( false === $close ) || ( false !== $next && $next < $close );

        if ( $dangling ) {
            // Keep everything before the broken opener; drop the fragment up to
            // the next delimiter (or end of content).
            $cut    = ( false !== $next ) ? $next : $len;
            $out   .= substr( $content, $offset, $pos - $offset );
            $offset = $cut;
            $count++;
            continue;
        }

        // Healthy opener — copy through to and including its "-->".
        $out   .= substr( $content, $offset, $close + 3 - $offset );
        $offset = $close + 3;
    }

    $out .= substr( $content, $offset );

    return $out;
}

/**
 * Neutralise a literal "-->" inside ACF block-comment JSON so the markup
 * parses. Rare, but a literal "-->" in (for example) FAQ answer text closes
 * the comment early. Replaced with "--&gt;", identical once rendered as HTML.
 *
 * @param string $content Post content.
 * @param int    $count   Number of blocks repaired (by reference).
 * @return string Repaired content.
 */
function acf_blocks_migrator_repair_json_arrows( $content, &$count ) {
    $count = 0;

    if ( ! is_string( $content ) || false === strpos( $content, 'wp:acf/' ) ) {
        return $content;
    }

    $out    = '';
    $offset = 0;

    while ( false !== ( $pos = strpos( $content, '<!-- wp:acf/', $offset ) ) ) {
        $brace       = strpos( $content, '{', $pos );
        $comment_end = strpos( $content, '-->', $pos );

        if ( false === $brace || ( false !== $comment_end && $comment_end < $brace ) ) {
            $stop   = ( false !== $comment_end ) ? $comment_end + 3 : $pos + 12;
            $out   .= substr( $content, $offset, $stop - $offset );
            $offset = $stop;
            continue;
        }

        $end = acf_blocks_migrator_match_brace( $content, $brace );

        if ( -1 === $end ) {
            $out   .= substr( $content, $offset, $brace + 1 - $offset );
            $offset = $brace + 1;
            continue;
        }

        $json  = substr( $content, $brace, $end - $brace + 1 );
        $fixed = str_replace( '-->', '--&gt;', $json );
        if ( $fixed !== $json ) {
            $count++;
        }

        $out   .= substr( $content, $offset, $brace - $offset ) . $fixed;
        $offset = $end + 1;
    }

    $out .= substr( $content, $offset );

    return $out;
}

/**
 * Run all string-level markup repairs (orphaned closers, dangling openers,
 * JSON "-->" escapes) and return the total number of fixes.
 *
 * @param string $content Post content.
 * @param int    $count   Total repairs (by reference).
 * @return string Repaired content.
 */
function acf_blocks_migrator_repair_markup( $content, &$count ) {
    // JSON "-->" escaping runs first: a container block whose attribute JSON
    // contains a literal "-->" is invisible to the delimiter regex, which
    // would otherwise make strip_orphaned_closers treat that block's valid
    // closer as orphaned and remove it.
    $content = acf_blocks_migrator_repair_json_arrows( $content, $c1 );
    $content = acf_blocks_migrator_strip_orphaned_closers( $content, $c2 );
    $content = acf_blocks_migrator_strip_dangling_openers( $content, $c3 );
    $count   = $c1 + $c2 + $c3;
    return $content;
}

/*
 * --------------------------------------------------------------------------
 * Orchestration
 * --------------------------------------------------------------------------
 */

/**
 * Run all migrations + repairs on a block of content.
 *
 * @param string $content Post content.
 * @param array  $report  Per-stage report (by reference).
 * @return string Migrated content (unchanged if nothing applied).
 */
function acf_blocks_migrator_migrate_content( $content, &$report ) {
    $report = array(
        'unparseable' => 0,
        'legacy'      => array(),
        'orphaned'    => false,
        'changed'     => false,
    );

    if ( ! is_string( $content ) || '' === $content ) {
        return $content;
    }

    $original = $content;

    // 1. Make unparseable markup parseable (orphaned closers, dangling
    //    openers, JSON "-->" escapes) — all string level, before parse.
    $content = acf_blocks_migrator_repair_markup( $content, $report['unparseable'] );

    // 2. Legacy block-name migrations (needs a parseable tree).
    if ( function_exists( 'parse_blocks' ) && false !== strpos( $content, 'wp:acf/' ) ) {
        $stats   = array();
        $changed = false;
        $blocks  = acf_blocks_migrator_transform_list( parse_blocks( $content ), $stats, $changed );
        if ( $changed ) {
            $content = serialize_blocks( $blocks );
            // Rebuilt ACF blocks carry migrated field content in their JSON.
            // If any value contains a literal "-->" it would re-break the
            // comment, so neutralise it again on the serialized output.
            $content = acf_blocks_migrator_repair_json_arrows( $content, $arrows_after );
            $report['unparseable'] += (int) $arrows_after;
        }
        // Keep informational counts (e.g. poll-unsupported) even if not "changed".
        $report['legacy'] = $stats;
    }

    // 3. Orphaned InnerBlocks HTML (delegated to the recovery helper).
    if ( function_exists( 'acf_blocks_recovery_repair_content' ) ) {
        $content = acf_blocks_recovery_repair_content( $content, $orph );
        $report['orphaned'] = (bool) $orph;
    }

    // Safety net: never destroy content. If any step produced a non-string or
    // emptied a non-empty post, abandon the result and return the original.
    if ( ! is_string( $content ) || ( '' === $content && '' !== $original ) ) {
        $report = array(
            'unparseable' => 0,
            'legacy'      => array(),
            'orphaned'    => false,
            'changed'     => false,
        );
        return $original;
    }

    $report['changed'] = ( $content !== $original );

    return $content;
}

/**
 * Scan all editable posts and tally migration opportunities.
 *
 * @return array {
 *     @type int   $scanned       Posts containing ACF markup.
 *     @type int   $posts_changed Posts a migration run would alter.
 *     @type array $totals        Per-issue instance counts.
 *     @type array $post_ids      Affected post IDs (capped sample per issue).
 * }
 */
function acf_blocks_migrator_scan() {
    $ids = get_posts( array(
        'post_type'      => get_post_types( array( 'show_in_rest' => true ), 'names' ),
        'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );

    $totals = array(
        'unparseable'      => 0,
        'orphaned_posts'   => 0,
        'toc'              => 0,
        'productbox'       => 0,
        'accordion-item'   => 0,
        'accordion-group'  => 0,
        'acf-accordion'    => 0,
        'poll-unsupported' => 0,
    );
    $scanned       = 0;
    $posts_changed = 0;

    foreach ( $ids as $id ) {
        $content = get_post_field( 'post_content', $id );
        if ( '' === $content || false === strpos( $content, 'wp:acf/' ) ) {
            continue;
        }
        $scanned++;

        acf_blocks_migrator_migrate_content( $content, $report );

        $totals['unparseable'] += (int) $report['unparseable'];
        if ( ! empty( $report['orphaned'] ) ) {
            $totals['orphaned_posts']++;
        }
        foreach ( (array) $report['legacy'] as $k => $v ) {
            if ( isset( $totals[ $k ] ) ) {
                $totals[ $k ] += (int) $v;
            }
        }
        if ( ! empty( $report['changed'] ) ) {
            $posts_changed++;
        }
    }

    return array(
        'scanned'       => $scanned,
        'posts_changed' => $posts_changed,
        'totals'        => $totals,
    );
}

/**
 * Post meta key holding a post's pre-migration content (the revert source).
 */
const ACF_BLOCKS_MIGRATOR_BACKUP_META = '_acf_blocks_migrator_backup';

/**
 * Option key tracking the most recent migration batch (for one-click revert).
 */
const ACF_BLOCKS_MIGRATOR_BATCH_OPTION = 'acf_blocks_migrator_last_batch';

/**
 * Apply migrations across all editable posts.
 *
 * For every post that changes, the original content is first snapshotted as a
 * native WordPress revision (best effort) and stored in post meta so the run
 * can be reverted later regardless of the site's revision settings.
 *
 * @param bool $dry_run When true, count but do not write.
 * @return array { @type int $changed, @type int $scanned, @type array $errors }
 */
function acf_blocks_migrator_run( $dry_run = false ) {
    $ids = get_posts( array(
        'post_type'      => get_post_types( array( 'show_in_rest' => true ), 'names' ),
        'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );

    $changed = 0;
    $scanned = 0;
    $errors  = array();

    // Only one revert point is kept. Starting a real run supersedes any
    // previous batch, so clear its leftover backups first.
    if ( ! $dry_run ) {
        acf_blocks_migrator_clear_backups();
    }

    $migrated_ids = array();

    foreach ( $ids as $id ) {
        $content = get_post_field( 'post_content', $id );
        if ( '' === $content || false === strpos( $content, 'wp:acf/' ) ) {
            continue;
        }
        $scanned++;

        $new = acf_blocks_migrator_migrate_content( $content, $report );
        if ( empty( $report['changed'] ) ) {
            continue;
        }

        if ( $dry_run ) {
            $changed++;
            continue;
        }

        // Snapshot the original: a native revision (so it shows in the
        // editor's Revisions browser) and a meta backup (the reliable
        // revert source). Store the backup before updating the post.
        wp_save_post_revision( $id );
        update_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, $content );

        $result = wp_update_post( array(
            'ID'           => $id,
            'post_content' => wp_slash( $new ),
        ), true );

        if ( is_wp_error( $result ) ) {
            delete_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META );
            $errors[] = sprintf( '#%d: %s', $id, $result->get_error_message() );
        } else {
            $changed++;
            $migrated_ids[] = $id;
        }
    }

    if ( ! $dry_run && ! empty( $migrated_ids ) ) {
        update_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION, array(
            'ids'  => $migrated_ids,
            'time' => time(),
        ), false );
    }

    return array(
        'changed' => $changed,
        'scanned' => $scanned,
        'errors'  => $errors,
    );
}

/**
 * Get the most recent migration batch, if any.
 *
 * @return array|null array( 'ids' => int[], 'time' => int ) or null.
 */
function acf_blocks_migrator_get_last_batch() {
    $batch = get_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION );
    if ( ! is_array( $batch ) || empty( $batch['ids'] ) ) {
        return null;
    }
    return $batch;
}

/**
 * Delete stored backups for the recorded batch and clear the batch record.
 */
function acf_blocks_migrator_clear_backups() {
    $batch = acf_blocks_migrator_get_last_batch();
    if ( $batch ) {
        foreach ( $batch['ids'] as $id ) {
            delete_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META );
        }
    }
    delete_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION );
}

/**
 * Revert the most recent migration batch, restoring each post's pre-migration
 * content from its stored backup. The restore is itself saved as a revision.
 *
 * @return array { @type int $reverted, @type int $total, @type array $errors }
 */
function acf_blocks_migrator_revert() {
    $batch = acf_blocks_migrator_get_last_batch();
    if ( ! $batch ) {
        return array( 'reverted' => 0, 'total' => 0, 'errors' => array() );
    }

    $reverted = 0;
    $errors   = array();

    foreach ( $batch['ids'] as $id ) {
        $backup = get_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, true );

        if ( '' === $backup || null === $backup ) {
            $errors[] = sprintf( '#%d: %s', $id, __( 'no backup found', 'acf-blocks' ) );
            continue;
        }

        $result = wp_update_post( array(
            'ID'           => $id,
            'post_content' => wp_slash( $backup ),
        ), true );

        if ( is_wp_error( $result ) ) {
            $errors[] = sprintf( '#%d: %s', $id, $result->get_error_message() );
        } else {
            delete_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META );
            $reverted++;
        }
    }

    $total = count( $batch['ids'] );
    delete_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION );

    return array( 'reverted' => $reverted, 'total' => $total, 'errors' => $errors );
}

/*
 * --------------------------------------------------------------------------
 * Admin UI — rendered on the ACF Blocks options page (License page)
 * --------------------------------------------------------------------------
 */

/**
 * Handle Migrator form submissions (scan / dry-run / apply).
 */
function acf_blocks_migrator_handle_actions() {
    if ( ! isset( $_POST['acf_blocks_migrator_action'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    check_admin_referer( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' );

    // Scanning/migrating runs over every post in one request; lift the time
    // limit so a large site can't half-complete on a PHP timeout.
    if ( function_exists( 'set_time_limit' ) ) {
        @set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
    }

    $action = sanitize_text_field( wp_unslash( $_POST['acf_blocks_migrator_action'] ) );

    if ( 'scan' === $action ) {
        $scan = acf_blocks_migrator_scan();
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'scan', 'data' => $scan ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'dry_run' === $action ) {
        $res = acf_blocks_migrator_run( true );
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'dry_run', 'data' => $res ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'apply' === $action ) {
        $res = acf_blocks_migrator_run( false );
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'apply', 'data' => $res ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'revert' === $action ) {
        $res = acf_blocks_migrator_revert();
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'revert', 'data' => $res ), 5 * MINUTE_IN_SECONDS );
    }

    wp_safe_redirect( admin_url( 'options-general.php?page=acf-blocks-license&acf_blocks_migrated=1#acf-blocks-migrator' ) );
    exit;
}
add_action( 'admin_init', 'acf_blocks_migrator_handle_actions' );

/**
 * Render the Migrator card on the options page.
 *
 * Hooked to the action emitted at the foot of the License page.
 */
function acf_blocks_migrator_render_card() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $report = get_transient( 'acf_blocks_migrator_report' );
    $labels = array(
        'unparseable'      => __( 'Unparseable markup (orphaned/dangling delimiters)', 'acf-blocks' ),
        'orphaned_posts'   => __( 'Posts with orphaned InnerBlocks HTML', 'acf-blocks' ),
        'toc'              => __( 'Legacy Table of Contents → Toc', 'acf-blocks' ),
        'productbox'       => __( 'Legacy Product Box → Product Box', 'acf-blocks' ),
        'accordion-item'   => __( 'Legacy Accordion Item → Accordion', 'acf-blocks' ),
        'accordion-group'  => __( 'Legacy Accordion Group → Accordion', 'acf-blocks' ),
        'acf-accordion'    => __( 'Legacy ACF Accordion → Accordion', 'acf-blocks' ),
        'poll-unsupported' => __( 'Poll blocks (no equivalent — needs manual action)', 'acf-blocks' ),
    );
    ?>
    <div class="card" id="acf-blocks-migrator" style="max-width: 600px; margin-top: 20px;">
        <h2 style="margin-top: 0;"><?php esc_html_e( 'Block Migrator &amp; Repair', 'acf-blocks' ); ?></h2>
        <p style="color:#50575e;">
            <?php esc_html_e( 'Migrate legacy/renamed blocks to their current versions and repair markup that causes "Attempt Recovery" or "block unavailable" errors. Changes are saved as post revisions, so they can be reverted.', 'acf-blocks' ); ?>
        </p>

        <?php if ( is_array( $report ) && isset( $report['type'], $report['data'] ) ) : ?>
            <?php $data = $report['data']; ?>

            <?php if ( 'scan' === $report['type'] ) : ?>
                <div style="background:#f0f6fc;border:1px solid #c3d4e6;border-radius:6px;padding:12px 14px;margin-bottom:14px;">
                    <strong><?php printf( esc_html__( 'Scan complete — %1$d posts with ACF blocks, %2$d would be changed.', 'acf-blocks' ), (int) $data['scanned'], (int) $data['posts_changed'] ); ?></strong>
                    <table class="widefat striped" style="margin-top:10px;">
                        <tbody>
                        <?php foreach ( $labels as $key => $label ) : ?>
                            <tr>
                                <td><?php echo esc_html( $label ); ?></td>
                                <td style="text-align:right;width:70px;"><strong><?php echo (int) ( $data['totals'][ $key ] ?? 0 ); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ( 'dry_run' === $report['type'] ) : ?>
                <div style="background:#fcf9e8;border:1px solid #e6db8e;border-radius:6px;padding:12px 14px;margin-bottom:14px;">
                    <strong><?php printf( esc_html__( 'Dry run — %1$d of %2$d scanned posts would be migrated. No changes were written.', 'acf-blocks' ), (int) $data['changed'], (int) $data['scanned'] ); ?></strong>
                </div>
            <?php elseif ( 'apply' === $report['type'] ) : ?>
                <div style="background:#edfaef;border:1px solid #a7e3b4;border-radius:6px;padding:12px 14px;margin-bottom:14px;">
                    <strong><?php printf( esc_html__( 'Migration complete — %1$d post(s) updated. A revision and a restore point were saved for each.', 'acf-blocks' ), (int) $data['changed'] ); ?></strong>
                    <?php if ( ! empty( $data['errors'] ) ) : ?>
                        <p style="color:#b32d2e;margin:8px 0 0;"><?php esc_html_e( 'Errors:', 'acf-blocks' ); ?></p>
                        <ul style="margin:4px 0 0 18px;list-style:disc;">
                            <?php foreach ( array_slice( $data['errors'], 0, 20 ) as $err ) : ?>
                                <li style="color:#b32d2e;"><?php echo esc_html( $err ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php elseif ( 'revert' === $report['type'] ) : ?>
                <div style="background:#f0f6fc;border:1px solid #c3d4e6;border-radius:6px;padding:12px 14px;margin-bottom:14px;">
                    <strong><?php printf( esc_html__( 'Revert complete — %1$d of %2$d post(s) restored to their pre-migration content.', 'acf-blocks' ), (int) $data['reverted'], (int) $data['total'] ); ?></strong>
                    <?php if ( ! empty( $data['errors'] ) ) : ?>
                        <ul style="margin:4px 0 0 18px;list-style:disc;">
                            <?php foreach ( array_slice( $data['errors'], 0, 20 ) as $err ) : ?>
                                <li style="color:#b32d2e;"><?php echo esc_html( $err ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
            <button type="submit" name="acf_blocks_migrator_action" value="scan" class="button">
                <?php esc_html_e( 'Scan', 'acf-blocks' ); ?>
            </button>
            <button type="submit" name="acf_blocks_migrator_action" value="dry_run" class="button">
                <?php esc_html_e( 'Dry Run', 'acf-blocks' ); ?>
            </button>
            <button type="submit" name="acf_blocks_migrator_action" value="apply" class="button button-primary"
                onclick="return confirm('<?php echo esc_js( __( 'Migrate all affected posts now? A revision and restore point are saved for each, so the run can be reverted.', 'acf-blocks' ) ); ?>');">
                <?php esc_html_e( 'Migrate All', 'acf-blocks' ); ?>
            </button>
        </form>

        <?php $batch = acf_blocks_migrator_get_last_batch(); ?>
        <?php if ( $batch ) : ?>
            <div style="margin-top:14px;padding-top:14px;border-top:1px solid #e2e4e7;">
                <p style="margin:0 0 8px;color:#50575e;">
                    <?php
                    printf(
                        esc_html__( 'Last migration: %1$d post(s) on %2$s. You can roll them all back to their previous content.', 'acf-blocks' ),
                        count( $batch['ids'] ),
                        esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $batch['time'] ) )
                    );
                    ?>
                </p>
                <form method="post">
                    <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                    <button type="submit" name="acf_blocks_migrator_action" value="revert" class="button button-secondary"
                        onclick="return confirm('<?php echo esc_js( __( 'Revert the last migration? All affected posts will be restored to their pre-migration content.', 'acf-blocks' ) ); ?>');">
                        <?php esc_html_e( 'Revert Last Migration', 'acf-blocks' ); ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
add_action( 'acf_blocks_options_page_after_cards', 'acf_blocks_migrator_render_card' );

/*
 * --------------------------------------------------------------------------
 * WP-CLI parity
 * --------------------------------------------------------------------------
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    /**
     * Migrate legacy ACF blocks and repair unparseable markup site-wide.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Report what would change without writing.
     *
     * [--revert]
     * : Roll back the most recent migration, restoring pre-migration content.
     *
     * ## EXAMPLES
     *
     *     wp acf-blocks migrate --dry-run
     *     wp acf-blocks migrate
     *     wp acf-blocks migrate --revert
     *
     * @when after_wp_load
     *
     * @param array $args       Positional args (unused).
     * @param array $assoc_args Named args.
     */
    function acf_blocks_migrator_cli( $args, $assoc_args ) {
        if ( isset( $assoc_args['revert'] ) ) {
            $res = acf_blocks_migrator_revert();
            foreach ( $res['errors'] as $err ) {
                WP_CLI::warning( $err );
            }
            if ( 0 === $res['total'] ) {
                WP_CLI::success( 'No migration batch to revert.' );
            } else {
                WP_CLI::success( sprintf( '%d of %d post(s) reverted.', $res['reverted'], $res['total'] ) );
            }
            return;
        }

        $dry = isset( $assoc_args['dry-run'] );
        $res = acf_blocks_migrator_run( $dry );

        foreach ( $res['errors'] as $err ) {
            WP_CLI::warning( $err );
        }

        $verb = $dry ? 'would be migrated' : 'migrated';
        WP_CLI::success( sprintf( '%d of %d post(s) %s.', $res['changed'], $res['scanned'], $verb ) );
    }

    WP_CLI::add_command( 'acf-blocks migrate', 'acf_blocks_migrator_cli' );
}
