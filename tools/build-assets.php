<?php
/**
 * Minify every CSS and JS asset the plugin ships.
 *
 * Writes a .min.css / .min.js sibling next to each source file. The enqueue
 * layer prefers the minified file and falls back to the source, so a missing
 * artifact degrades to "unminified", never to "broken".
 *
 * Both minifiers are character scanners rather than regex passes: a regex that
 * strips comments will happily destroy a "//" inside a string literal or a
 * "/*" inside a JS regex literal. Savings are deliberately conservative — no
 * identifier renaming, no semicolon elision.
 *
 * Usage:
 *   php tools/build-assets.php
 *   php tools/build-assets.php --check
 */

/**
 * Minify CSS.
 *
 * Preserves /*! … *\/ banners and never touches bytes inside strings or url().
 *
 * @param string $css Source CSS.
 * @return string Minified CSS.
 */
function acfb_minify_css( $css ) {
    $len    = strlen( $css );
    $out    = '';
    $i      = 0;
    $quote  = '';

    while ( $i < $len ) {
        $c    = $css[ $i ];
        $next = $i + 1 < $len ? $css[ $i + 1 ] : '';

        // Inside a string: copy verbatim until the matching quote.
        if ( '' !== $quote ) {
            $out .= $c;
            if ( '\\' === $c && '' !== $next ) {
                $out .= $next;
                $i   += 2;
                continue;
            }
            if ( $c === $quote ) {
                $quote = '';
            }
            $i++;
            continue;
        }

        if ( '"' === $c || "'" === $c ) {
            $quote = $c;
            $out  .= $c;
            $i++;
            continue;
        }

        // Comments: drop, but keep /*! … */ banners.
        if ( '/' === $c && '*' === $next ) {
            $end = strpos( $css, '*/', $i + 2 );
            $end = false === $end ? $len : $end + 2;
            if ( isset( $css[ $i + 2 ] ) && '!' === $css[ $i + 2 ] ) {
                $out .= substr( $css, $i, $end - $i );
                // Skip the whitespace run that follows a preserved banner.
                while ( $end < $len && preg_match( '/\s/', $css[ $end ] ) ) {
                    $end++;
                }
            }
            $i = $end;
            continue;
        }

        // Collapse any whitespace run to a single space, dropping it only where
        // CSS cannot possibly need it.
        //
        // The two sets are deliberately asymmetric:
        //   ')' is absent from $drop_after — "var(--x) 3%" needs that space.
        //   '(' is absent from $drop_before — "@media (max-width:1px)" needs it.
        // Arithmetic operators (+ - * /) are in neither set, because calc()
        // requires whitespace around them: "calc(1px+2px)" is invalid.
        if ( preg_match( '/\s/', $c ) ) {
            $j = $i;
            while ( $j < $len && preg_match( '/\s/', $css[ $j ] ) ) {
                $j++;
            }
            $prev  = '' === $out ? '' : substr( $out, -1 );
            $after = $j < $len ? $css[ $j ] : '';

            $drop_after  = '{};:,>([';
            $drop_before = '{};:,>)]';

            $droppable = '' === $prev || '' === $after
                || false !== strpos( $drop_after, $prev )
                || false !== strpos( $drop_before, $after );

            if ( ! $droppable ) {
                $out .= ' ';
            }
            $i = $j;
            continue;
        }

        // Trailing semicolon before a closing brace is redundant.
        if ( ';' === $c ) {
            $j = $i + 1;
            while ( $j < $len && preg_match( '/\s/', $css[ $j ] ) ) {
                $j++;
            }
            if ( $j < $len && '}' === $css[ $j ] ) {
                $i = $j;
                continue;
            }
        }

        $out .= $c;
        $i++;
    }

    return trim( $out );
}

/**
 * Minify JavaScript conservatively.
 *
 * Strips comments and per-line indentation while preserving newlines, so
 * automatic semicolon insertion behaves exactly as it did in the source.
 * Correctly skips strings, template literals and regex literals.
 *
 * @param string $js Source JavaScript.
 * @return string Minified JavaScript.
 */
function acfb_minify_js( $js ) {
    $len  = strlen( $js );
    $out  = '';
    $i    = 0;

    while ( $i < $len ) {
        $c    = $js[ $i ];
        $next = $i + 1 < $len ? $js[ $i + 1 ] : '';

        // Line comment. Also drop the whitespace that preceded it so the line
        // does not end in a dangling space.
        if ( '/' === $c && '/' === $next ) {
            $out = rtrim( $out, " \t" );
            while ( $i < $len && "\n" !== $js[ $i ] ) {
                $i++;
            }
            continue;
        }

        // Block comment (keep /*! banners).
        if ( '/' === $c && '*' === $next ) {
            $end = strpos( $js, '*/', $i + 2 );
            $end = false === $end ? $len : $end + 2;
            if ( isset( $js[ $i + 2 ] ) && '!' === $js[ $i + 2 ] ) {
                $out .= substr( $js, $i, $end - $i );
            }
            $i = $end;
            continue;
        }

        // String or template literal.
        if ( '"' === $c || "'" === $c || '`' === $c ) {
            $quote = $c;
            $out  .= $c;
            $i++;
            while ( $i < $len ) {
                $ch = $js[ $i ];
                $out .= $ch;
                if ( '\\' === $ch && $i + 1 < $len ) {
                    $out .= $js[ $i + 1 ];
                    $i   += 2;
                    continue;
                }
                $i++;
                if ( $ch === $quote ) {
                    break;
                }
            }
            continue;
        }

        // Regex literal — only where a regex may legally start.
        if ( '/' === $c ) {
            $prev = rtrim( $out );
            $last = '' === $prev ? '' : substr( $prev, -1 );
            if ( '' === $last || false !== strpos( "(,=:[!&|?{};+-*%~^<>\n", $last ) ) {
                $out .= $c;
                $i++;
                $in_class = false;
                while ( $i < $len ) {
                    $ch   = $js[ $i ];
                    $out .= $ch;
                    if ( '\\' === $ch && $i + 1 < $len ) {
                        $out .= $js[ $i + 1 ];
                        $i   += 2;
                        continue;
                    }
                    $i++;
                    if ( '[' === $ch ) {
                        $in_class = true;
                    } elseif ( ']' === $ch ) {
                        $in_class = false;
                    } elseif ( '/' === $ch && ! $in_class ) {
                        break;
                    }
                }
                continue;
            }
        }

        // Horizontal whitespace: collapse to one space, drop at line edges.
        if ( ' ' === $c || "\t" === $c ) {
            $j = $i;
            while ( $j < $len && ( ' ' === $js[ $j ] || "\t" === $js[ $j ] ) ) {
                $j++;
            }
            $prev = '' === $out ? '' : substr( $out, -1 );
            $rest = $j < $len ? $js[ $j ] : '';
            if ( '' !== $prev && "\n" !== $prev && '' !== $rest && "\n" !== $rest ) {
                $out .= ' ';
            }
            $i = $j;
            continue;
        }

        // Collapse blank lines.
        if ( "\n" === $c || "\r" === $c ) {
            $j = $i;
            while ( $j < $len && ( "\n" === $js[ $j ] || "\r" === $js[ $j ] || ' ' === $js[ $j ] || "\t" === $js[ $j ] ) ) {
                $j++;
            }
            if ( '' !== $out && "\n" !== substr( $out, -1 ) ) {
                $out .= "\n";
            }
            $i = $j;
            continue;
        }

        $out .= $c;
        $i++;
    }

    return trim( $out );
}

/* -------------------------------------------------------------------------- */

// Only run the build when invoked as a script; tests require this file to
// exercise the two minifiers directly.
if ( ! isset( $argv[0] ) || realpath( $argv[0] ) !== realpath( __FILE__ ) ) {
    return;
}

$root  = dirname( __DIR__ );
$check = in_array( '--check', $argv, true );

$sources = array_values(
    array_filter(
        array_merge(
            (array) glob( $root . '/assets/css/*.css' ),
            (array) glob( $root . '/assets/js/*.js' ),
            (array) glob( $root . '/blocks/*/*.css' ),
            (array) glob( $root . '/blocks/*/*.js' )
        ),
        static function ( $file ) {
            return ! preg_match( '/\.min\.(css|js)$/', $file );
        }
    )
);

sort( $sources, SORT_STRING );

$stale     = array();
$written   = 0;
$src_bytes = 0;
$min_bytes = 0;

foreach ( $sources as $file ) {
    $ext      = pathinfo( $file, PATHINFO_EXTENSION );
    $target   = preg_replace( '/\.(css|js)$/', '.min.$1', $file );
    $source   = (string) file_get_contents( $file );
    $minified = 'css' === $ext ? acfb_minify_css( $source ) : acfb_minify_js( $source );
    $minified .= "\n";

    $src_bytes += strlen( $source );
    $min_bytes += strlen( $minified );

    $current = is_file( $target ) ? (string) file_get_contents( $target ) : '';

    if ( $check ) {
        if ( $current !== $minified ) {
            $stale[] = substr( $target, strlen( $root ) + 1 );
        }
        continue;
    }

    if ( $current !== $minified ) {
        file_put_contents( $target, $minified );
        $written++;
    }
}

if ( $check ) {
    if ( $stale ) {
        fwrite( STDERR, "Minified assets are out of date:\n  " . implode( "\n  ", $stale ) . "\n" );
        exit( 1 );
    }
    echo "Minified assets are current.\n";
    exit( 0 );
}

$saved = $src_bytes > 0 ? round( ( 1 - $min_bytes / $src_bytes ) * 100, 1 ) : 0;
printf(
    "Minified %d assets (%d rewritten): %s -> %s bytes (-%s%%).\n",
    count( $sources ),
    $written,
    number_format( $src_bytes ),
    number_format( $min_bytes ),
    $saved
);
