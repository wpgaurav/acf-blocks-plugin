<?php
/**
 * Table of Contents Block Template
 *
 * @var array   $block       The block settings and attributes
 * @var string  $content     The block inner HTML (empty for this block)
 * @var bool    $is_preview  True during AJAX preview
 * @var int     $post_id     The post ID
 * @var array   $context     The context array
 */

/**
 * Extract headings from post content
 */
if ( ! function_exists( 'acf_toc_extract_headings' ) ) {
    function acf_toc_extract_headings( $content, $levels ) {
        if ( empty( $content ) ) {
            return array();
        }

        // Build regex pattern for selected heading levels
        $level_pattern = implode( '|', array_map( function( $level ) {
            return preg_quote( $level, '/' );
        }, $levels ) );

        // Match headings with their content and existing IDs
        $pattern = '/<(' . $level_pattern . ')([^>]*)>(.*?)<\/\1>/is';

        preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

        $headings = array();
        $id_counts = array();

        foreach ( $matches as $match ) {
            $tag        = strtolower( $match[1] );
            $attributes = $match[2];
            $text       = wp_strip_all_tags( $match[3] );
            $level      = (int) substr( $tag, 1 );

            // Try to extract existing ID from attributes
            $id = '';
            if ( preg_match( '/id=["\']([^"\']+)["\']/', $attributes, $id_match ) ) {
                $id = $id_match[1];
            }

            // Generate ID from text if no ID exists
            if ( empty( $id ) ) {
                $id = sanitize_title( $text );
                if ( empty( $id ) ) {
                    $id = 'heading-' . count( $headings );
                }
            }

            // Handle duplicate IDs
            if ( isset( $id_counts[ $id ] ) ) {
                $id_counts[ $id ]++;
                $id = $id . '-' . $id_counts[ $id ];
            } else {
                $id_counts[ $id ] = 1;
            }

            $headings[] = array(
                'id'    => $id,
                'text'  => $text,
                'level' => $level,
                'tag'   => $tag,
            );
        }

        return $headings;
    }
}

/**
 * Build hierarchical TOC list
 */
if ( ! function_exists( 'acf_toc_build_list' ) ) {
    function acf_toc_build_list( $headings, $list_type, $list_class, $link_class, $is_plain = false ) {
        if ( empty( $headings ) ) {
            return '';
        }

        $list_class_attr = ! empty( $list_class ) ? ' class="' . esc_attr( $list_class ) . '"' : '';
        $link_class_str  = ! empty( $link_class ) ? esc_attr( $link_class ) : '';

        // Find minimum level to use as base for depth calculation
        $min_level = min( array_column( $headings, 'level' ) );

        // For plain list, output all at same level
        if ( $is_plain ) {
            $tag = 'ul';
            $output = '<' . $tag . $list_class_attr . '>';
            foreach ( $headings as $heading ) {
                $link_classes = 'acf-toc__link';
                if ( $link_class_str ) {
                    $link_classes .= ' ' . $link_class_str;
                }
                $depth = $heading['level'] - $min_level;
                $output .= '<li class="acf-toc__item acf-toc__item--depth-' . esc_attr( $depth ) . '" data-level="' . esc_attr( $heading['level'] ) . '">';
                $output .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="' . esc_attr( $link_classes ) . '">';
                $output .= esc_html( $heading['text'] );
                $output .= '</a></li>';
            }
            $output .= '</' . $tag . '>';
            return $output;
        }

        // For hierarchical list, build nested structure
        $tag = ( $list_type === 'ol' ) ? 'ol' : 'ul';

        $output = '';
        $current_level = $min_level;
        $current_depth = 0;
        $stack = array();

        foreach ( $headings as $index => $heading ) {
            $level = $heading['level'];
            $depth = $level - $min_level;
            $link_classes = 'acf-toc__link';
            if ( $link_class_str ) {
                $link_classes .= ' ' . $link_class_str;
            }

            // Opening list item and nested lists
            if ( $index === 0 ) {
                $output .= '<' . $tag . $list_class_attr . '>';
                $stack[] = $min_level;
                $current_depth = 0;
            } elseif ( $level > $current_level ) {
                // Go deeper - open new nested list(s)
                for ( $i = $current_level; $i < $level; $i++ ) {
                    $sub_depth = ( $i + 1 ) - $min_level;
                    $output .= '<' . $tag . ' class="acf-toc__sublist acf-toc__sublist--depth-' . esc_attr( $sub_depth ) . '">';
                    $stack[] = $i + 1;
                }
                $current_depth = $depth;
            } elseif ( $level < $current_level ) {
                // Go up - close nested lists
                for ( $i = $current_level; $i > $level; $i-- ) {
                    $output .= '</li></' . $tag . '>';
                    array_pop( $stack );
                }
                $output .= '</li>';
                $current_depth = $depth;
            } else {
                // Same level - close previous item
                $output .= '</li>';
            }

            $output .= '<li class="acf-toc__item acf-toc__item--depth-' . esc_attr( $depth ) . '" data-level="' . esc_attr( $level ) . '">';
            $output .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="' . esc_attr( $link_classes ) . '">';
            $output .= esc_html( $heading['text'] );
            $output .= '</a>';

            $current_level = $level;
        }

        // Close remaining open elements
        while ( ! empty( $stack ) ) {
            $output .= '</li></' . $tag . '>';
            array_pop( $stack );
        }

        return $output;
    }
}

/**
 * Generate JSON-LD schema for TOC
 */
if ( ! function_exists( 'acf_toc_generate_schema' ) ) {
    function acf_toc_generate_schema( $headings, $post_id ) {
        if ( empty( $headings ) ) {
            return '';
        }

        $permalink = get_permalink( $post_id );
        $items = array();

        foreach ( $headings as $index => $heading ) {
            $items[] = array(
                '@type'    => 'SiteNavigationElement',
                'position' => $index + 1,
                'name'     => $heading['text'],
                'url'      => $permalink . '#' . $heading['id'],
            );
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'itemListElement' => $items,
        );

        return '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
    }
}

// Retrieve field values
$title             = acf_blocks_get_field( 'toc_title', $block ) ?: 'Table of Contents';
$title_tag         = acf_blocks_get_field( 'toc_title_tag', $block ) ?: 'p';
$heading_levels    = acf_blocks_get_field( 'toc_heading_levels', $block ) ?: array( 'h2' );
$list_type         = acf_blocks_get_field( 'toc_list_type', $block ) ?: 'ul';
$collapsible       = acf_blocks_get_field( 'toc_collapsible', $block );
$collapsed_default = acf_blocks_get_field( 'toc_collapsed_default', $block );
$sticky            = acf_blocks_get_field( 'toc_sticky', $block );
$sticky_offset     = acf_blocks_get_field( 'toc_sticky_offset', $block ) ?: 20;
$smooth_scroll     = acf_blocks_get_field( 'toc_smooth_scroll', $block );
$highlight_active  = acf_blocks_get_field( 'toc_highlight_active', $block );
$custom_class      = acf_blocks_get_field( 'toc_custom_class', $block );
$title_class       = acf_blocks_get_field( 'toc_title_class', $block );
$list_class        = acf_blocks_get_field( 'toc_list_class', $block );
$link_class        = acf_blocks_get_field( 'toc_link_class', $block );
$include_schema    = acf_blocks_get_field( 'toc_schema', $block );
$aria_label        = acf_blocks_get_field( 'toc_aria_label', $block ) ?: 'Table of Contents';
$include_acf_headings = acf_blocks_get_field( 'toc_include_acf_block_headings', $block );

// Validate heading levels
if ( ! is_array( $heading_levels ) || empty( $heading_levels ) ) {
    $heading_levels = array( 'h2' );
}

// Build block classes
$block_classes = array( 'acf-toc' );
$className = $block['className'] ?? '';
if ( ! empty( $block['align'] ) ) {
    $block_classes[] = 'align' . $block['align'];
}
if ( ! empty( $className ) ) {
    $block_classes[] = $className;
}
if ( ! empty( $custom_class ) ) {
    $block_classes[] = esc_attr( $custom_class );
}
if ( $sticky ) {
    $block_classes[] = 'acf-toc--sticky';
}
if ( $smooth_scroll ) {
    $block_classes[] = 'acf-toc--smooth-scroll';
}
if ( $highlight_active ) {
    $block_classes[] = 'acf-toc--highlight-active';
}

// Generate unique ID for this block instance
$block_id = ! empty( $block['id'] ) ? $block['id'] : 'acf-toc-' . uniqid();

// Get post content for heading extraction
// Render blocks so headings from ACF blocks (e.g. Hero) are included
$post_content = '';
if ( $post_id ) {
    $post_obj = get_post( $post_id );
    if ( $post_obj ) {
        $raw_content = $post_obj->post_content;

        // Remove TOC blocks to prevent infinite recursion during rendering
        $content_without_toc = preg_replace(
            '/<!-- wp:acf\/toc\b.*?(?:\/-->|-->.*?<!-- \/wp:acf\/toc -->)/s',
            '',
            $raw_content
        );

        // Render blocks to get full HTML including headings from ACF blocks
        $post_content = do_blocks( $content_without_toc );
    }
}

// Strip headings inside ACF blocks when checkbox is OFF (default)
if ( ! $include_acf_headings && ! empty( $post_content ) ) {
    // After do_blocks(), rendered HTML contains data-acf-block attributes
    // on product boxes, pros/cons, etc. Strip those wrappers and their content
    // so their internal headings don't appear in the TOC.
    $post_content = preg_replace(
        '/<([a-z][a-z0-9]*)\b[^>]*\bdata-acf-block\b[^>]*>.*?<\/\1>/is',
        '',
        $post_content
    );
}

// Extract headings from the rendered content
$headings = acf_toc_extract_headings( $post_content, $heading_levels );

// Preview mode - show message if no headings
if ( $is_preview && empty( $headings ) ) {
    ?>
    <div class="acf-toc acf-toc--preview <?php echo esc_attr( implode( ' ', array_slice( $block_classes, 1 ) ) ); ?>">
        <p class="acf-toc__title"><?php echo esc_html( $title ); ?></p>
        <p class="acf-toc__preview-notice" style="color: #666; font-style: italic; margin: 0.5em 0;">
            <?php esc_html_e( 'Table of contents will be generated from headings in your content.', 'acf-blocks' ); ?>
            <br>
            <small><?php printf( esc_html__( 'Included levels: %s', 'acf-blocks' ), esc_html( implode( ', ', array_map( 'strtoupper', $heading_levels ) ) ) ); ?></small>
        </p>
    </div>
    <?php
    return;
}

// Don't render if no headings found (frontend)
if ( empty( $headings ) ) {
    return;
}

// Build the TOC list
$is_plain   = ( $list_type === 'plain' );
$toc_list   = acf_toc_build_list( $headings, $list_type, $list_class, $link_class, $is_plain );

// Title classes
$title_classes = 'acf-toc__title';
if ( ! empty( $title_class ) ) {
    $title_classes .= ' ' . esc_attr( $title_class );
}

// Allowed tags for title
$allowed_title_tags = array( 'p', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span' );
if ( ! in_array( $title_tag, $allowed_title_tags ) ) {
    $title_tag = 'p';
}
?>

<nav
    id="<?php echo esc_attr( $block_id ); ?>"
    class="<?php echo esc_attr( implode( ' ', $block_classes ) ); ?>"
    aria-label="<?php echo esc_attr( $aria_label ); ?>"
    <?php if ( $sticky ) : ?>data-sticky="true" data-sticky-offset="<?php echo esc_attr( $sticky_offset ); ?>"<?php endif; ?>
    <?php if ( $highlight_active ) : ?>data-highlight-active="true"<?php endif; ?>
>
    <?php if ( $collapsible ) : ?>
        <details<?php echo ! $collapsed_default ? ' open' : ''; ?> class="acf-toc__details">
            <summary class="acf-toc__summary">
                <<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>">
                    <?php echo esc_html( $title ); ?>
                </<?php echo esc_attr( $title_tag ); ?>>
            </summary>
            <div class="acf-toc__content">
                <?php echo $toc_list; ?>
            </div>
        </details>
    <?php else : ?>
        <?php if ( $title ) : ?>
            <<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>">
                <?php echo esc_html( $title ); ?>
            </<?php echo esc_attr( $title_tag ); ?>>
        <?php endif; ?>
        <div class="acf-toc__content">
            <?php echo $toc_list; ?>
        </div>
    <?php endif; ?>
</nav>

<?php
// Schema markup (frontend only)
if ( $include_schema && ! $is_preview && ! empty( $headings ) ) {
    echo acf_toc_generate_schema( $headings, $post_id );
}

// Inline CSS for sticky behavior (only when sticky is enabled)
if ( $sticky && ! defined( 'ACF_TOC_STICKY_CSS_LOADED' ) ) :
    define( 'ACF_TOC_STICKY_CSS_LOADED', true );
    $sticky_css = ':root{--acf-toc-sticky-offset:calc(var(--header-height,0px) + var(--wp-admin--admin-bar--height,0px) + 20px)}@media(min-width:1400px){.acf-toc--sticky{position:fixed;top:var(--acf-toc-sticky-offset);left:0;max-width:220px;max-height:calc(100vh - var(--acf-toc-sticky-offset) - 20px);overflow-y:auto;scrollbar-width:thin;font-size:0.8125em;line-height:1.4;z-index:100;padding:1rem}.acf-toc--sticky .acf-toc__title{font-size:0.75em;margin-bottom:0.5em;padding-bottom:0.375em}.acf-toc--sticky .acf-toc__item{padding:0}.acf-toc--sticky .acf-toc__link{padding:0.15em 0}.acf-toc--sticky .acf-toc__sublist{padding-left:0.75em;margin-top:0;margin-left:0}.acf-toc--sticky::-webkit-scrollbar{width:3px}.acf-toc--sticky::-webkit-scrollbar-thumb{background-color:rgba(0,0,0,0.15);border-radius:2px}}';
    echo '<style>' . acf_blocks_minify_css( $sticky_css ) . '</style>';
    // Set custom offset if provided
    if ( $sticky_offset && $sticky_offset != 20 ) {
        $offset_css = '#' . esc_attr( $block_id ) . '{--acf-toc-sticky-offset:calc(var(--header-height,0px) + var(--wp-admin--admin-bar--height,0px) + ' . intval( $sticky_offset ) . 'px)}';
        echo '<style>' . acf_blocks_minify_css( $offset_css ) . '</style>';
    }
endif;

// Inline CSS for smooth scroll (only when enabled)
if ( $smooth_scroll && ! defined( 'ACF_TOC_SMOOTH_CSS_LOADED' ) ) :
    define( 'ACF_TOC_SMOOTH_CSS_LOADED', true );
    $smooth_css = 'html:has(.acf-toc--smooth-scroll){scroll-behavior:smooth}@media(prefers-reduced-motion:reduce){html:has(.acf-toc--smooth-scroll){scroll-behavior:auto}}';
    echo '<style>' . acf_blocks_minify_css( $smooth_css ) . '</style>';
endif;

// Inline JS for active section highlighting (only when enabled)
if ( $highlight_active && ! defined( 'ACF_TOC_HIGHLIGHT_JS_LOADED' ) ) :
    define( 'ACF_TOC_HIGHLIGHT_JS_LOADED', true );
?>
<script>
(function() {
    'use strict';

    function initTocHighlighting() {
        var tocs = document.querySelectorAll('.acf-toc--highlight-active');
        if (!tocs.length || !('IntersectionObserver' in window)) return;

        tocs.forEach(function(toc) {
            var links = toc.querySelectorAll('.acf-toc__link');
            var headingIds = [];

            links.forEach(function(link) {
                var href = link.getAttribute('href');
                if (href && href.startsWith('#')) {
                    headingIds.push(href.substring(1));
                }
            });

            if (!headingIds.length) return;

            var headings = headingIds.map(function(id) {
                return document.getElementById(id);
            }).filter(Boolean);

            if (!headings.length) return;

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    var link = toc.querySelector('a[href="#' + entry.target.id + '"]');
                    if (link) {
                        if (entry.isIntersecting) {
                            // Remove active from all links
                            links.forEach(function(l) { l.classList.remove('acf-toc__link--active'); });
                            link.classList.add('acf-toc__link--active');
                        }
                    }
                });
            }, {
                rootMargin: '-80px 0px -80% 0px',
                threshold: 0
            });

            headings.forEach(function(heading) {
                observer.observe(heading);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTocHighlighting);
    } else {
        initTocHighlighting();
    }
})();
</script>
<?php endif; ?>
