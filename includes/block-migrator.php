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

            case 'acf/accordion':
                // Current block name, but older content stored its repeater
                // sub-fields as acf_accord_heading / acf_accord_content, which
                // the template no longer reads — so the block renders blank.
                // Remap those sub-fields in place to the current schema.
                $block = acf_blocks_migrator_remap_accordion_fields( $block, $did );
                if ( $did ) {
                    $stats['accordion-fields'] = ( $stats['accordion-fields'] ?? 0 ) + 1;
                    $changed = true;
                }
                if ( ! empty( $block['innerBlocks'] ) ) {
                    $block['innerBlocks'] = acf_blocks_migrator_transform_list( $block['innerBlocks'], $stats, $changed );
                }
                $out[] = $block;
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

/**
 * Remap a current acf/accordion block's legacy repeater sub-fields.
 *
 * Older content used acf_accord_heading / acf_accord_content; the current
 * template reads acf_accord_group_title / acf_accord_group_content. This
 * renames the value keys (and their _field-key references) in place, leaving
 * every other setting — FAQ schema, classes, group count — untouched.
 *
 * @param array $block Parsed acf/accordion block.
 * @param bool  $did   Set true if any sub-field was remapped.
 * @return array The block (modified when $did is true).
 */
function acf_blocks_migrator_remap_accordion_fields( $block, &$did ) {
    $did  = false;
    $data = isset( $block['attrs']['data'] ) ? (array) $block['attrs']['data'] : array();

    if ( empty( $data ) ) {
        return $block;
    }

    $remapped = array();
    foreach ( $data as $k => $v ) {
        if ( preg_match( '/^(_?)acf_accord_groups_(\d+)_acf_accord_heading$/', $k, $m ) ) {
            $nk              = $m[1] . 'acf_accord_groups_' . $m[2] . '_acf_accord_group_title';
            $remapped[ $nk ] = ( '_' === $m[1] ) ? 'field_acf_accord_group_title' : $v;
            $did             = true;
        } elseif ( preg_match( '/^(_?)acf_accord_groups_(\d+)_acf_accord_content$/', $k, $m ) ) {
            $nk              = $m[1] . 'acf_accord_groups_' . $m[2] . '_acf_accord_group_content';
            $remapped[ $nk ] = ( '_' === $m[1] ) ? 'field_acf_accord_group_content' : $v;
            $did             = true;
        } else {
            $remapped[ $k ] = $v;
        }
    }

    if ( $did ) {
        $block['attrs']['data'] = $remapped;
    }

    return $block;
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
 * Issue categories: key => [ label, text colour, background colour ].
 *
 * Single source of truth for the scan breakdown and the per-post badges.
 *
 * @return array<string,array{0:string,1:string,2:string}>
 */
function acf_blocks_migrator_categories() {
    return array(
        'unparseable'      => array( __( 'Repaired markup', 'acf-blocks' ),            '#9a5700', '#fcf0e0' ),
        'orphaned_posts'   => array( __( 'Recovered InnerBlocks HTML', 'acf-blocks' ), '#0b5394', '#e6f0fa' ),
        'toc'              => array( __( 'Table of Contents → Toc', 'acf-blocks' ),     '#0a6b2e', '#e6f4ea' ),
        'productbox'       => array( __( 'Product Box', 'acf-blocks' ),                 '#0a6b2e', '#e6f4ea' ),
        'accordion-item'   => array( __( 'Accordion Item → Accordion', 'acf-blocks' ),  '#0a6b2e', '#e6f4ea' ),
        'accordion-group'  => array( __( 'Accordion Group → Accordion', 'acf-blocks' ), '#0a6b2e', '#e6f4ea' ),
        'acf-accordion'    => array( __( 'ACF Accordion → Accordion', 'acf-blocks' ),   '#0a6b2e', '#e6f4ea' ),
        'accordion-fields' => array( __( 'Accordion fields fixed', 'acf-blocks' ),      '#0a6b2e', '#e6f4ea' ),
        'poll-unsupported' => array( __( 'Poll (manual — skipped)', 'acf-blocks' ),     '#8a6d00', '#fbf6e0' ),
    );
}

/**
 * Reduce a per-post migrate report to the category keys it touched.
 *
 * @param array $report Result from acf_blocks_migrator_migrate_content().
 * @return string[] Category keys (e.g. array( 'orphaned_posts', 'toc' )).
 */
function acf_blocks_migrator_report_keys( $report ) {
    $keys = array();
    if ( ! empty( $report['unparseable'] ) ) {
        $keys[] = 'unparseable';
    }
    if ( ! empty( $report['orphaned'] ) ) {
        $keys[] = 'orphaned_posts';
    }
    foreach ( (array) $report['legacy'] as $k => $v ) {
        if ( $v ) {
            $keys[] = $k;
        }
    }
    return $keys;
}

/**
 * Scan all editable posts and tally migration opportunities.
 *
 * @param int $list_limit Max affected posts to include in the returned list.
 * @return array {
 *     @type int   $scanned       Posts containing ACF markup.
 *     @type int   $posts_changed Posts a migration run would alter.
 *     @type array $totals        Per-issue instance counts.
 *     @type array $posts         Affected posts (id, title, edit, view, status, keys).
 * }
 */
function acf_blocks_migrator_scan( $list_limit = 500 ) {
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
        'accordion-fields' => 0,
        'poll-unsupported' => 0,
    );
    $scanned       = 0;
    $posts_changed = 0;
    $posts         = array();

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
            if ( count( $posts ) < $list_limit ) {
                $posts[] = acf_blocks_migrator_post_detail( $id, $report );
            }
        }
    }

    return array(
        'scanned'        => $scanned,
        'posts_changed'  => $posts_changed,
        'totals'         => $totals,
        'posts'          => $posts,
        'list_truncated' => $posts_changed > count( $posts ),
    );
}

/**
 * Build a display record for an affected post.
 *
 * @param int   $id     Post ID.
 * @param array $report Per-post migrate report.
 * @return array
 */
function acf_blocks_migrator_post_detail( $id, $report ) {
    $title = get_the_title( $id );
    return array(
        'id'     => $id,
        'title'  => ( '' !== $title ) ? $title : sprintf( '#%d', $id ),
        'edit'   => get_edit_post_link( $id, 'raw' ),
        'view'   => get_permalink( $id ),
        'status' => get_post_status( $id ),
        'keys'   => acf_blocks_migrator_report_keys( $report ),
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
 * How many posts a single "Migrate" run processes before pausing.
 */
const ACF_BLOCKS_MIGRATOR_BATCH_SIZE = 30;

/**
 * Migrate up to $limit affected posts, then report what is left.
 *
 * Processes posts in order, writing at most $limit of them. For each post
 * written, the original content is snapshotted as a native WordPress revision
 * and (if not already present) stored in post meta as a restore point. The
 * migrated IDs accumulate into the batch record so a multi-batch run can be
 * reverted as a whole.
 *
 * @param int $limit Max posts to write this run. 0 = no limit (whole site).
 * @return array {
 *     @type array $migrated  Per-post detail records written this run.
 *     @type int   $count     Posts written this run.
 *     @type int   $remaining Affected posts still pending after this run.
 *     @type int   $scanned   Posts containing ACF markup that were examined.
 *     @type array $errors    Per-post error strings.
 * }
 */
function acf_blocks_migrator_run_batch( $limit = ACF_BLOCKS_MIGRATOR_BATCH_SIZE ) {
    $ids = get_posts( array(
        'post_type'      => get_post_types( array( 'show_in_rest' => true ), 'names' ),
        'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );

    $migrated     = array();
    $migrated_ids = array();
    $errors       = array();
    $remaining    = 0;
    $scanned      = 0;

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

        // Already hit this run's quota — just count what remains.
        if ( $limit > 0 && count( $migrated_ids ) >= $limit ) {
            $remaining++;
            continue;
        }

        $detail = acf_blocks_migrator_post_detail( $id, $report );

        // Native revision + restore point (only set the backup once, so the
        // true pre-migration content survives repeated runs).
        wp_save_post_revision( $id );
        $had_backup = '' !== get_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, true );
        if ( ! $had_backup ) {
            update_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, $content );
        }

        $result = wp_update_post( array(
            'ID'           => $id,
            'post_content' => wp_slash( $new ),
        ), true );

        if ( is_wp_error( $result ) ) {
            if ( ! $had_backup ) {
                delete_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META );
            }
            $errors[] = sprintf( '#%d: %s', $id, $result->get_error_message() );
            continue;
        }

        $migrated[]     = $detail;
        $migrated_ids[] = $id;
    }

    if ( ! empty( $migrated_ids ) ) {
        $batch    = acf_blocks_migrator_get_last_batch();
        $existing = $batch ? $batch['ids'] : array();
        update_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION, array(
            'ids'  => array_values( array_unique( array_merge( $existing, $migrated_ids ) ) ),
            'time' => time(),
        ), false );
    }

    return array(
        'migrated'  => $migrated,
        'count'     => count( $migrated_ids ),
        'remaining' => $remaining,
        'scanned'   => $scanned,
        'errors'    => $errors,
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
 *
 * @return int Number of restore points discarded.
 */
function acf_blocks_migrator_clear_backups() {
    $batch   = acf_blocks_migrator_get_last_batch();
    $count   = 0;
    if ( $batch ) {
        foreach ( $batch['ids'] as $id ) {
            delete_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META );
            $count++;
        }
    }
    delete_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION );
    return $count;
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
 * Handle Migrator form submissions (scan / migrate / revert / discard).
 */
function acf_blocks_migrator_handle_actions() {
    if ( ! isset( $_POST['acf_blocks_migrator_action'] ) || ! current_user_can( 'manage_options' ) ) {
        return;
    }
    check_admin_referer( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' );

    // A scan/migrate run iterates every post; lift the time limit so a large
    // site can't half-complete on a PHP timeout.
    if ( function_exists( 'set_time_limit' ) ) {
        @set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
    }

    $action = sanitize_text_field( wp_unslash( $_POST['acf_blocks_migrator_action'] ) );

    if ( 'scan' === $action ) {
        $data = acf_blocks_migrator_scan();
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'scan', 'data' => $data ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'migrate' === $action ) {
        $data = acf_blocks_migrator_run_batch( ACF_BLOCKS_MIGRATOR_BATCH_SIZE );
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'migrate', 'data' => $data ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'revert' === $action ) {
        $data = acf_blocks_migrator_revert();
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'revert', 'data' => $data ), 5 * MINUTE_IN_SECONDS );
    } elseif ( 'discard' === $action ) {
        $count = acf_blocks_migrator_clear_backups();
        set_transient( 'acf_blocks_migrator_report', array( 'type' => 'discard', 'data' => array( 'count' => $count ) ), 5 * MINUTE_IN_SECONDS );
    }

    wp_safe_redirect( admin_url( 'options-general.php?page=acf-blocks-license&acf_blocks_migrated=1#acf-blocks-migrator' ) );
    exit;
}
add_action( 'admin_init', 'acf_blocks_migrator_handle_actions' );

/**
 * Render a row of category badges for a set of category keys.
 *
 * @param string[] $keys Category keys.
 * @param array    $cats Output of acf_blocks_migrator_categories().
 */
function acf_blocks_migrator_render_badges( $keys, $cats ) {
    foreach ( $keys as $k ) {
        if ( ! isset( $cats[ $k ] ) ) {
            continue;
        }
        list( $label, $fg, $bg ) = $cats[ $k ];
        printf(
            '<span class="acfbm-badge" style="color:%1$s;background:%2$s;">%3$s</span>',
            esc_attr( $fg ),
            esc_attr( $bg ),
            esc_html( $label )
        );
    }
}

/**
 * Render a table of affected/migrated posts.
 *
 * @param array $posts Post detail records.
 * @param array $cats  Categories map.
 */
function acf_blocks_migrator_render_post_table( $posts, $cats ) {
    if ( empty( $posts ) ) {
        return;
    }
    ?>
    <div class="acfbm-scroll">
        <table class="widefat striped acfbm-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Post', 'acf-blocks' ); ?></th>
                    <th><?php esc_html_e( 'What changes', 'acf-blocks' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $posts as $p ) : ?>
                <tr>
                    <td>
                        <?php if ( ! empty( $p['edit'] ) ) : ?>
                            <a href="<?php echo esc_url( $p['edit'] ); ?>"><strong><?php echo esc_html( $p['title'] ); ?></strong></a>
                        <?php else : ?>
                            <strong><?php echo esc_html( $p['title'] ); ?></strong>
                        <?php endif; ?>
                        <span style="color:#787c82;">— <?php echo esc_html( $p['status'] ); ?></span>
                        <?php if ( ! empty( $p['view'] ) ) : ?>
                            · <a href="<?php echo esc_url( $p['view'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'view', 'acf-blocks' ); ?></a>
                        <?php endif; ?>
                    </td>
                    <td><?php acf_blocks_migrator_render_badges( $p['keys'], $cats ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Render the Migrator card on the options page.
 *
 * Hooked to the action emitted at the foot of the License page.
 */
function acf_blocks_migrator_render_card() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $report    = get_transient( 'acf_blocks_migrator_report' );
    $cats      = acf_blocks_migrator_categories();
    $batch     = acf_blocks_migrator_get_last_batch();
    $batch_n   = $batch ? count( $batch['ids'] ) : 0;
    $type      = ( is_array( $report ) && isset( $report['type'] ) ) ? $report['type'] : '';
    $data      = ( is_array( $report ) && isset( $report['data'] ) ) ? $report['data'] : array();
    ?>
    <style>
        #acf-blocks-migrator .acfbm-badge{display:inline-block;font-size:11px;font-weight:600;line-height:1.7;padding:0 8px;border-radius:10px;margin:2px 4px 2px 0;white-space:nowrap;}
        #acf-blocks-migrator .acfbm-scroll{max-height:360px;overflow:auto;border:1px solid #e2e4e7;border-radius:6px;margin-top:10px;}
        #acf-blocks-migrator .acfbm-table{border:0;margin:0;}
        #acf-blocks-migrator .acfbm-bar{height:12px;border-radius:6px;background:#e2e4e7;overflow:hidden;margin:8px 0;}
        #acf-blocks-migrator .acfbm-bar > span{display:block;height:100%;background:#2271b1;transition:width .3s;}
        #acf-blocks-migrator .acfbm-note{border-radius:6px;padding:12px 14px;margin-bottom:14px;}
        #acf-blocks-migrator .acfbm-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:6px;}
        #acf-blocks-migrator .acfbm-summary{display:flex;flex-wrap:wrap;gap:6px;margin:10px 0;}
        #acf-blocks-migrator .acfbm-chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;padding:3px 10px;border-radius:14px;border:1px solid #dcdcde;}
        #acf-blocks-migrator .acfbm-chip b{font-size:13px;}
    </style>
    <div class="card" id="acf-blocks-migrator" style="max-width: 820px; margin-top: 20px;">
        <h2 style="margin-top: 0;"><?php esc_html_e( 'Block Migrator &amp; Repair', 'acf-blocks' ); ?></h2>
        <p style="color:#50575e;">
            <?php
            printf(
                /* translators: %d: batch size */
                esc_html__( 'Migrate legacy/renamed blocks and repair markup that causes "Attempt Recovery" or "block unavailable" errors. Posts are processed %d at a time, and every change keeps a revision and a restore point so it can be reverted.', 'acf-blocks' ),
                (int) ACF_BLOCKS_MIGRATOR_BATCH_SIZE
            );
            ?>
        </p>

        <?php if ( 'scan' === $type ) : ?>
            <?php $affected = (int) $data['posts_changed']; ?>
            <div class="acfbm-note" style="background:<?php echo $affected ? '#f0f6fc' : '#edfaef'; ?>;border:1px solid <?php echo $affected ? '#c3d4e6' : '#a7e3b4'; ?>;">
                <strong>
                    <?php
                    if ( $affected ) {
                        printf(
                            esc_html__( '%1$d post(s) need migration (out of %2$d with ACF blocks).', 'acf-blocks' ),
                            $affected,
                            (int) $data['scanned']
                        );
                    } else {
                        esc_html_e( 'All clear — no posts need migration.', 'acf-blocks' );
                    }
                    ?>
                </strong>
                <?php if ( $affected ) : ?>
                    <div class="acfbm-summary">
                        <?php foreach ( $cats as $key => $cat ) : ?>
                            <?php $n = (int) ( $data['totals'][ $key ] ?? 0 ); ?>
                            <?php if ( $n > 0 ) : ?>
                                <span class="acfbm-chip"><b><?php echo esc_html( number_format_i18n( $n ) ); ?></b> <?php echo esc_html( $cat[0] ); ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php acf_blocks_migrator_render_post_table( $data['posts'], $cats ); ?>
                    <?php if ( ! empty( $data['list_truncated'] ) ) : ?>
                        <p style="color:#787c82;margin:8px 0 0;"><em><?php esc_html_e( 'List truncated for display; all affected posts will still be migrated.', 'acf-blocks' ); ?></em></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        <?php elseif ( 'migrate' === $type ) : ?>
            <?php
            $done      = $batch_n; // total migrated so far this session
            $remaining = (int) $data['remaining'];
            $total     = $done + $remaining;
            $pct       = $total > 0 ? (int) round( $done / $total * 100 ) : 100;
            ?>
            <div class="acfbm-note" style="background:<?php echo $remaining ? '#f0f6fc' : '#edfaef'; ?>;border:1px solid <?php echo $remaining ? '#c3d4e6' : '#a7e3b4'; ?>;">
                <strong>
                    <?php printf( esc_html__( 'Migrated %1$d post(s) this batch. %2$d remaining.', 'acf-blocks' ), (int) $data['count'], $remaining ); ?>
                </strong>
                <div class="acfbm-bar" title="<?php echo esc_attr( $pct . '%' ); ?>"><span style="width:<?php echo (int) $pct; ?>%;"></span></div>
                <p style="margin:0;color:#50575e;">
                    <?php printf( esc_html__( '%1$d of %2$d done (%3$d%%).', 'acf-blocks' ), $done, $total, $pct ); ?>
                </p>
                <?php if ( ! empty( $data['errors'] ) ) : ?>
                    <ul style="margin:8px 0 0 18px;list-style:disc;color:#b32d2e;">
                        <?php foreach ( array_slice( $data['errors'], 0, 20 ) as $err ) : ?>
                            <li><?php echo esc_html( $err ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php acf_blocks_migrator_render_post_table( $data['migrated'], $cats ); ?>
            </div>

            <?php if ( $remaining > 0 ) : ?>
                <div class="acfbm-note" style="background:#fcf9e8;border:1px solid #e6db8e;">
                    <strong><?php printf( esc_html__( '%d post(s) still to go.', 'acf-blocks' ), $remaining ); ?></strong>
                    <?php esc_html_e( 'Click Continue to migrate the next batch.', 'acf-blocks' ); ?>
                    <div class="acfbm-actions">
                        <form method="post">
                            <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                            <button type="submit" name="acf_blocks_migrator_action" value="migrate" class="button button-primary">
                                <?php printf( esc_html__( 'Continue — Migrate Next %d', 'acf-blocks' ), (int) ACF_BLOCKS_MIGRATOR_BATCH_SIZE ); ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <div class="acfbm-note" style="background:#edfaef;border:1px solid #a7e3b4;">
                    <strong><?php esc_html_e( '🎉 All affected posts have been migrated.', 'acf-blocks' ); ?></strong>
                </div>
            <?php endif; ?>

        <?php elseif ( 'revert' === $type ) : ?>
            <div class="acfbm-note" style="background:#f0f6fc;border:1px solid #c3d4e6;">
                <strong><?php printf( esc_html__( 'Revert complete — %1$d of %2$d post(s) restored to their pre-migration content.', 'acf-blocks' ), (int) $data['reverted'], (int) $data['total'] ); ?></strong>
                <?php if ( ! empty( $data['errors'] ) ) : ?>
                    <ul style="margin:8px 0 0 18px;list-style:disc;color:#b32d2e;">
                        <?php foreach ( array_slice( $data['errors'], 0, 20 ) as $err ) : ?>
                            <li><?php echo esc_html( $err ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        <?php elseif ( 'discard' === $type ) : ?>
            <div class="acfbm-note" style="background:#f6f7f7;border:1px solid #dcdcde;">
                <strong><?php printf( esc_html__( 'Discarded %d restore point(s). Migrated content is unchanged.', 'acf-blocks' ), (int) $data['count'] ); ?></strong>
            </div>
        <?php endif; ?>

        <div class="acfbm-actions">
            <form method="post">
                <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                <button type="submit" name="acf_blocks_migrator_action" value="scan" class="button">
                    <?php esc_html_e( 'Scan for issues', 'acf-blocks' ); ?>
                </button>
            </form>
            <form method="post">
                <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                <button type="submit" name="acf_blocks_migrator_action" value="migrate" class="button button-primary"
                    onclick="return confirm('<?php echo esc_js( sprintf( __( 'Migrate up to %d affected posts now? A revision and restore point are saved for each.', 'acf-blocks' ), (int) ACF_BLOCKS_MIGRATOR_BATCH_SIZE ) ); ?>');">
                    <?php printf( esc_html__( 'Migrate Next %d', 'acf-blocks' ), (int) ACF_BLOCKS_MIGRATOR_BATCH_SIZE ); ?>
                </button>
            </form>
        </div>

        <?php if ( $batch_n > 0 ) : ?>
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid #e2e4e7;">
                <p style="margin:0 0 8px;color:#50575e;">
                    <?php
                    printf(
                        esc_html__( '%1$d post(s) have restore points from this migration session (last change %2$s).', 'acf-blocks' ),
                        $batch_n,
                        esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $batch['time'] ) )
                    );
                    ?>
                </p>
                <div class="acfbm-actions">
                    <form method="post">
                        <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                        <button type="submit" name="acf_blocks_migrator_action" value="revert" class="button button-secondary"
                            onclick="return confirm('<?php echo esc_js( __( 'Revert? All posts in this session will be restored to their pre-migration content.', 'acf-blocks' ) ); ?>');">
                            <?php esc_html_e( 'Revert Migration', 'acf-blocks' ); ?>
                        </button>
                    </form>
                    <form method="post">
                        <?php wp_nonce_field( 'acf_blocks_migrator', 'acf_blocks_migrator_nonce' ); ?>
                        <button type="submit" name="acf_blocks_migrator_action" value="discard" class="button button-link-delete"
                            onclick="return confirm('<?php echo esc_js( __( 'Discard restore points? You will no longer be able to revert, but migrated content stays as-is.', 'acf-blocks' ) ); ?>');">
                            <?php esc_html_e( 'Discard restore points', 'acf-blocks' ); ?>
                        </button>
                    </form>
                </div>
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
     * [--limit=<number>]
     * : Migrate at most this many posts (0 = all). Default: 0.
     *
     * [--revert]
     * : Roll back the migration session, restoring pre-migration content.
     *
     * [--discard]
     * : Delete restore points without changing migrated content.
     *
     * ## EXAMPLES
     *
     *     wp acf-blocks migrate --dry-run
     *     wp acf-blocks migrate
     *     wp acf-blocks migrate --limit=30
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
                WP_CLI::success( 'No migration session to revert.' );
            } else {
                WP_CLI::success( sprintf( '%d of %d post(s) reverted.', $res['reverted'], $res['total'] ) );
            }
            return;
        }

        if ( isset( $assoc_args['discard'] ) ) {
            $n = acf_blocks_migrator_clear_backups();
            WP_CLI::success( sprintf( '%d restore point(s) discarded.', $n ) );
            return;
        }

        if ( isset( $assoc_args['dry-run'] ) ) {
            $scan = acf_blocks_migrator_scan();
            foreach ( acf_blocks_migrator_categories() as $key => $cat ) {
                $n = (int) ( $scan['totals'][ $key ] ?? 0 );
                if ( $n > 0 ) {
                    WP_CLI::log( sprintf( '  %-30s %d', $cat[0], $n ) );
                }
            }
            WP_CLI::success( sprintf( '%d of %d post(s) would be migrated.', $scan['posts_changed'], $scan['scanned'] ) );
            return;
        }

        $limit = isset( $assoc_args['limit'] ) ? max( 0, (int) $assoc_args['limit'] ) : 0;
        $res   = acf_blocks_migrator_run_batch( $limit );

        foreach ( $res['errors'] as $err ) {
            WP_CLI::warning( $err );
        }

        if ( $res['remaining'] > 0 ) {
            WP_CLI::success( sprintf( '%d post(s) migrated; %d remaining (run again to continue).', $res['count'], $res['remaining'] ) );
        } else {
            WP_CLI::success( sprintf( '%d post(s) migrated; none remaining.', $res['count'] ) );
        }
    }

    WP_CLI::add_command( 'acf-blocks migrate', 'acf_blocks_migrator_cli' );
}
