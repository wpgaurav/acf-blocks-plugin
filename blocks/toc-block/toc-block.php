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
        $link_class_str  = ! empty( $link_class ) ? $link_class : '';

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

        // Keep only observed levels on the stack. Missing levels need no empty
        // lists, and a later shallower heading must never close the root early.
        $tag = ( $list_type === 'ol' ) ? 'ol' : 'ul';
        $output = '<' . $tag . $list_class_attr . '>';
        $stack = array();
        foreach ( $headings as $index => $heading ) {
            $level = $heading['level'];
            $depth = $level - $min_level;
            if ( 0 === $index ) {
                $stack[] = $level;
            } else {
                while ( count( $stack ) > 1 && $level < end( $stack ) ) {
                    $output .= '</li></' . $tag . '>';
                    array_pop( $stack );
                }
                if ( $level > end( $stack ) ) {
                    $output .= '<' . $tag . ' class="acf-toc__sublist acf-toc__sublist--depth-' . esc_attr( $depth ) . '">';
                    $stack[] = $level;
                } else {
                    $output .= '</li>';
                    if ( 1 === count( $stack ) ) {
                        $stack[0] = $level;
                    }
                }
            }
            $link_classes = 'acf-toc__link' . ( $link_class ? ' ' . $link_class : '' );
            $output .= '<li class="acf-toc__item acf-toc__item--depth-' . esc_attr( $depth ) . '" data-level="' . esc_attr( $level ) . '">';
            $output .= '<a href="#' . esc_attr( $heading['id'] ) . '" class="' . esc_attr( $link_classes ) . '">' . esc_html( $heading['text'] ) . '</a>';
        }
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

        return '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
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

// The final the_content filter builds the list from the HTML WordPress actually
// rendered, including synced patterns, HTML blocks and dynamic block headings.
// Reading or rendering post_content here creates a different heading inventory.
if ( $is_preview ) {
    ?>
    <div class="acf-toc acf-toc--preview <?php echo esc_attr( implode( ' ', array_slice( $block_classes, 1 ) ) ); ?>">
        <p class="acf-toc__title"><?php echo esc_html( $title ); ?></p>
        <p class="acf-toc__preview-notice"><?php esc_html_e( 'Table of contents will be generated from headings in your content.', 'acf-blocks' ); ?></p>
    </div>
    <?php
    return;
}
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
    data-toc-finalized="0"
    data-heading-levels="<?php echo esc_attr( implode( ',', array_map( 'strtolower', $heading_levels ) ) ); ?>"
    data-include-acf-headings="<?php echo $include_acf_headings ? '1' : '0'; ?>"
    data-list-type="<?php echo esc_attr( $list_type ); ?>"
    data-list-class="<?php echo esc_attr( $list_class ); ?>"
    data-link-class="<?php echo esc_attr( $link_class ); ?>"
    data-toc-schema="<?php echo $include_schema ? '1' : '0'; ?>"
    data-toc-post-id="<?php echo absint( $post_id ); ?>"
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
                <!--acf-toc-list:start--><!--acf-toc-list:end-->
            </div>
        </details>
    <?php else : ?>
        <?php if ( $title ) : ?>
            <<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>">
                <?php echo esc_html( $title ); ?>
            </<?php echo esc_attr( $title_tag ); ?>>
        <?php endif; ?>
        <div class="acf-toc__content">
            <!--acf-toc-list:start--><!--acf-toc-list:end-->
        </div>
    <?php endif; ?>
    <!--acf-toc-schema:start--><!--acf-toc-schema:end-->
</nav>
