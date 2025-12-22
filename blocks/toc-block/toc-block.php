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

// Retrieve field values
$title             = get_field( 'toc_title' ) ?: 'Table of Contents';
$title_tag         = get_field( 'toc_title_tag' ) ?: 'p';
$heading_levels    = get_field( 'toc_heading_levels' ) ?: array( 'h2' );
$list_type         = get_field( 'toc_list_type' ) ?: 'ul';
$collapsible       = get_field( 'toc_collapsible' );
$collapsed_default = get_field( 'toc_collapsed_default' );
$sticky            = get_field( 'toc_sticky' );
$sticky_offset     = get_field( 'toc_sticky_offset' ) ?: 20;
$smooth_scroll     = get_field( 'toc_smooth_scroll' );
$highlight_active  = get_field( 'toc_highlight_active' );
$custom_class      = get_field( 'toc_custom_class' );
$title_class       = get_field( 'toc_title_class' );
$list_class        = get_field( 'toc_list_class' );
$link_class        = get_field( 'toc_link_class' );
$include_schema    = get_field( 'toc_schema' );
$aria_label        = get_field( 'toc_aria_label' ) ?: 'Table of Contents';

// Validate heading levels
if ( ! is_array( $heading_levels ) || empty( $heading_levels ) ) {
    $heading_levels = array( 'h2' );
}

// Build block classes
$block_classes = array( 'acf-toc' );
if ( ! empty( $block['align'] ) ) {
    $block_classes[] = 'align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $block_classes[] = $block['className'];
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

/**
 * Extract headings from post content
 */
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

/**
 * Build hierarchical TOC list
 */
function acf_toc_build_list( $headings, $list_type, $list_class, $link_class, $is_plain = false ) {
    if ( empty( $headings ) ) {
        return '';
    }

    $list_class_attr = ! empty( $list_class ) ? ' class="' . esc_attr( $list_class ) . '"' : '';
    $link_class_str  = ! empty( $link_class ) ? esc_attr( $link_class ) : '';

    // For plain list, output all at same level
    if ( $is_plain ) {
        $tag = 'ul';
        $output = '<' . $tag . $list_class_attr . '>';
        foreach ( $headings as $heading ) {
            $link_classes = 'acf-toc__link';
            if ( $link_class_str ) {
                $link_classes .= ' ' . $link_class_str;
            }
            $output .= '<li class="acf-toc__item" data-level="' . esc_attr( $heading['level'] ) . '">';
            $output .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="' . esc_attr( $link_classes ) . '">';
            $output .= esc_html( $heading['text'] );
            $output .= '</a></li>';
        }
        $output .= '</' . $tag . '>';
        return $output;
    }

    // For hierarchical list, build nested structure
    $tag = ( $list_type === 'ol' ) ? 'ol' : 'ul';

    // Find minimum level to use as base
    $min_level = min( array_column( $headings, 'level' ) );

    $output = '';
    $current_level = $min_level;
    $stack = array();

    foreach ( $headings as $index => $heading ) {
        $level = $heading['level'];
        $link_classes = 'acf-toc__link';
        if ( $link_class_str ) {
            $link_classes .= ' ' . $link_class_str;
        }

        // Opening list item and nested lists
        if ( $index === 0 ) {
            $output .= '<' . $tag . $list_class_attr . '>';
            $stack[] = $min_level;
        } elseif ( $level > $current_level ) {
            // Go deeper - open new nested list(s)
            for ( $i = $current_level; $i < $level; $i++ ) {
                $output .= '<' . $tag . ' class="acf-toc__sublist">';
                $stack[] = $i + 1;
            }
        } elseif ( $level < $current_level ) {
            // Go up - close nested lists
            for ( $i = $current_level; $i > $level; $i-- ) {
                $output .= '</li></' . $tag . '>';
                array_pop( $stack );
            }
            $output .= '</li>';
        } else {
            // Same level - close previous item
            $output .= '</li>';
        }

        $output .= '<li class="acf-toc__item" data-level="' . esc_attr( $level ) . '">';
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

/**
 * Generate JSON-LD schema for TOC
 */
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

// Get post content for heading extraction
$post_content = '';
if ( ! $is_preview && $post_id ) {
    $post = get_post( $post_id );
    if ( $post ) {
        $post_content = $post->post_content;
    }
}

// Extract headings
$headings = acf_toc_extract_headings( $post_content, $heading_levels );

// Preview mode handling
if ( $is_preview ) {
    $headings = array(
        array( 'id' => 'intro', 'text' => 'Introduction', 'level' => 2, 'tag' => 'h2' ),
        array( 'id' => 'getting-started', 'text' => 'Getting Started', 'level' => 2, 'tag' => 'h2' ),
        array( 'id' => 'installation', 'text' => 'Installation', 'level' => 3, 'tag' => 'h3' ),
        array( 'id' => 'configuration', 'text' => 'Configuration', 'level' => 3, 'tag' => 'h3' ),
        array( 'id' => 'advanced', 'text' => 'Advanced Usage', 'level' => 2, 'tag' => 'h2' ),
        array( 'id' => 'conclusion', 'text' => 'Conclusion', 'level' => 2, 'tag' => 'h2' ),
    );
    // Filter preview headings based on selected levels
    $headings = array_filter( $headings, function( $h ) use ( $heading_levels ) {
        return in_array( $h['tag'], $heading_levels );
    });
    $headings = array_values( $headings ); // Re-index array
}

// Don't render if no headings found
if ( empty( $headings ) && ! $is_preview ) {
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
                    <span class="acf-toc__toggle-icon" aria-hidden="true"></span>
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
// Schema markup
if ( $include_schema && ! $is_preview && ! empty( $headings ) ) {
    echo acf_toc_generate_schema( $headings, $post_id );
}

// Inline CSS for sticky behavior (only when sticky is enabled)
if ( $sticky && ! defined( 'ACF_TOC_STICKY_CSS_LOADED' ) ) :
    define( 'ACF_TOC_STICKY_CSS_LOADED', true );
?>
<style>
@media (min-width: 1400px) {
    .acf-toc--sticky {
        position: sticky;
        top: var(--acf-toc-sticky-offset, 20px);
        max-height: calc(100vh - var(--acf-toc-sticky-offset, 20px) - 40px);
        overflow-y: auto;
        scrollbar-width: thin;
    }
    .acf-toc--sticky::-webkit-scrollbar {
        width: 4px;
    }
    .acf-toc--sticky::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 2px;
    }
}
</style>
<?php
    // Set CSS custom property for sticky offset
    echo '<style>#' . esc_attr( $block_id ) . ' { --acf-toc-sticky-offset: ' . intval( $sticky_offset ) . 'px; }</style>';
endif;

// Inline CSS for smooth scroll (only when enabled)
if ( $smooth_scroll && ! defined( 'ACF_TOC_SMOOTH_CSS_LOADED' ) ) :
    define( 'ACF_TOC_SMOOTH_CSS_LOADED', true );
?>
<style>
html:has(.acf-toc--smooth-scroll) {
    scroll-behavior: smooth;
}
@media (prefers-reduced-motion: reduce) {
    html:has(.acf-toc--smooth-scroll) {
        scroll-behavior: auto;
    }
}
</style>
<?php endif; ?>

<?php
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
