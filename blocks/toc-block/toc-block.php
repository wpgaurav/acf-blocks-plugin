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

        // Match every heading so duplicate IDs remain deterministic even when
        // an intermediate heading level is excluded from the TOC.
        $pattern = '/<(h[1-6])([^>]*)>(.*?)<\/\1>/is';

        preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

        $headings = array();
        $used_ids = array();
        $heading_index = 0;

        foreach ( $matches as $match ) {
            $heading_index++;
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
                    $id = 'heading-' . $heading_index;
                }
            }

            // Handle duplicate IDs using the same algorithm as the final
            // content filter in extra.php.
            $original_id = $id;
            $counter     = 2;
            while ( isset( $used_ids[ $id ] ) ) {
                $id = $original_id . '-' . $counter;
                $counter++;
            }
            $used_ids[ $id ] = true;

            if ( in_array( $tag, $levels, true ) ) {
                $headings[] = array(
                    'id'    => $id,
                    'text'  => $text,
                    'level' => $level,
                    'tag'   => $tag,
                );
            }
        }

        return $headings;
    }
}

/**
 * Collect heading-bearing HTML from parsed blocks without rendering the post.
 *
 * The previous implementation called do_blocks() from inside the TOC render
 * callback, causing every dynamic block and query in the post to execute a
 * second time. This walker reads core heading markup directly and recurses into
 * normal InnerBlocks. ACF blocks are included only when requested; data-only
 * ACF blocks may be rendered individually as an opt-in compatibility fallback.
 *
 * @param array $blocks               Parsed blocks.
 * @param bool  $include_acf_headings Include headings nested inside ACF blocks.
 * @return string
 */
if ( ! function_exists( 'acf_toc_collect_heading_html' ) ) {
    function acf_toc_collect_heading_html( $blocks, $include_acf_headings ) {
        $html = '';

        foreach ( $blocks as $parsed_block ) {
            $name   = array_key_exists( 'blockName', $parsed_block ) ? $parsed_block['blockName'] : '';
            $is_acf = 0 === strpos( $name, 'acf/' );

            if ( 'core/heading' === $name || null === $name ) {
                $html .= (string) ( $parsed_block['innerHTML'] ?? '' );
            }

            if ( $is_acf && $include_acf_headings ) {
                /**
                 * Supply heading HTML generated from ACF data-only blocks.
                 *
                 * InnerBlocks headings are handled by recursion. Integrations
                 * can expose template-generated headings without rendering the
                 * full post by returning a small heading-only HTML fragment.
                 *
                 * @param string $html_fragment Heading-only HTML.
                 * @param array  $parsed_block  Parsed ACF block.
                 */
                $acf_heading_html = (string) apply_filters( 'acf_toc_acf_block_heading_html', '', $parsed_block );
                $may_render_fallback = 'acf/toc' !== $name
                    && empty( $parsed_block['innerBlocks'] )
                    && apply_filters( 'acf_toc_render_acf_block_for_headings', true, $parsed_block );
                if ( '' === $acf_heading_html && $may_render_fallback && function_exists( 'render_block' ) ) {
                    $acf_heading_html = render_block( $parsed_block );
                }
                $html .= $acf_heading_html;
            }

            if ( ! empty( $parsed_block['innerBlocks'] ) && ( ! $is_acf || $include_acf_headings ) ) {
                $html .= acf_toc_collect_heading_html( $parsed_block['innerBlocks'], $include_acf_headings );
            }
        }

        return $html;
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

        $list_mode    = $is_plain ? 'plain' : ( 'ol' === $list_type ? 'ol' : 'ul' );
        $list_classes = array( 'acf-toc__list', 'acf-toc__list--' . $list_mode );
        if ( ! empty( $list_class ) ) {
            $list_classes[] = $list_class;
        }
        $list_class_attr = ' class="' . esc_attr( implode( ' ', $list_classes ) ) . '"';
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
if ( is_array( $heading_levels ) ) {
    $heading_levels = array_values( array_intersect(
        array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
        array_map( 'strtolower', $heading_levels )
    ) );
}
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
if ( $collapsible ) {
    $block_classes[] = 'acf-toc--collapsible';
}

// Generate unique ID for this block instance
$block_id = ! empty( $block['id'] ) ? $block['id'] : 'acf-toc-' . wp_unique_id();

// Get heading markup without re-rendering the post or its dynamic blocks.
$post_content = '';
if ( $post_id ) {
    $post_obj = get_post( $post_id );
    if ( $post_obj ) {
        $post_content = acf_toc_collect_heading_html(
            parse_blocks( $post_obj->post_content ),
            (bool) $include_acf_headings
        );
    }
}

// Extract headings from the rendered content
$headings = acf_toc_extract_headings( $post_content, $heading_levels );

// Preview mode - show message if no headings
if ( $is_preview && empty( $headings ) ) {
    ?>
    <div class="acf-toc acf-toc--preview <?php echo esc_attr( implode( ' ', array_slice( $block_classes, 1 ) ) ); ?>">
        <p class="acf-toc__title"><?php echo esc_html( $title ); ?></p>
        <p class="acf-toc__preview-notice">
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
    data-heading-levels="<?php echo esc_attr( implode( ',', array_map( 'strtolower', $heading_levels ) ) ); ?>"
    data-include-acf-headings="<?php echo $include_acf_headings ? '1' : '0'; ?>"
    <?php if ( $sticky ) : ?>data-sticky="true" data-sticky-offset="<?php echo esc_attr( $sticky_offset ); ?>"<?php endif; ?>
    <?php if ( $highlight_active ) : ?>data-highlight-active="true"<?php endif; ?>
    <?php if ( $sticky ) : ?>style="--acf-toc-sticky-offset:calc(var(--header-height,0px) + var(--wp-admin--admin-bar--height,0px) + <?php echo absint( $sticky_offset ); ?>px);"<?php endif; ?>
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
