<?php
/**
 * ACF Blocks Core Functions
 *
 * Handles block registration, field group loading, and asset management.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PHP 7.4-compatible string suffix check.
 *
 * The plugin currently supports PHP 7.4, while str_ends_with() was added in
 * PHP 8.0. Keep the compatibility boundary in one helper so runtime code does
 * not accidentally raise the minimum PHP version.
 *
 * @param string $haystack String to inspect.
 * @param string $needle   Expected suffix.
 * @return bool
 */
function acf_blocks_str_ends_with( $haystack, $needle ) {
    $haystack = (string) $haystack;
    $needle   = (string) $needle;

    if ( '' === $needle ) {
        return true;
    }

    if ( strlen( $needle ) > strlen( $haystack ) ) {
        return false;
    }

    return 0 === substr_compare( $haystack, $needle, -strlen( $needle ) );
}

/**
 * Get cached block metadata from the generated manifest.
 *
 * Production requests load a generated PHP array, which is OPcache-friendly
 * and avoids directory scans plus repeated JSON decoding. A filesystem scan
 * remains as a development fallback when the generated file is missing.
 *
 * @param bool $include_disabled Include blocks disabled through Block Manager.
 * @return array[] Array of block info arrays with keys: folder, folder_name, metadata.
 */
function acf_blocks_get_block_metadata_cache( $include_disabled = false ) {
    static $all_blocks = null;

    if ( null === $all_blocks ) {
        $all_blocks   = array();
        $manifest_file = ACF_BLOCKS_PLUGIN_DIR . 'includes/generated-block-manifest.php';
        $manifest      = is_readable( $manifest_file ) ? require $manifest_file : array();

        if ( is_array( $manifest ) && ! empty( $manifest ) ) {
            foreach ( $manifest as $folder_name => $definition ) {
                if ( empty( $definition['metadata']['name'] ) ) {
                    continue;
                }
                $all_blocks[] = array(
                    'folder'       => trailingslashit( ACF_BLOCKS_PLUGIN_DIR . 'blocks/' . $folder_name ),
                    'folder_name'  => $folder_name,
                    'metadata'     => $definition['metadata'],
                    'field_groups' => $definition['field_groups'] ?? array(),
                );
            }
        } else {
            $all_blocks = acf_blocks_discover_block_metadata();
        }
    }

    if ( $include_disabled ) {
        return $all_blocks;
    }

    $disabled = acf_blocks_get_disabled_blocks();
    if ( empty( $disabled ) ) {
        return $all_blocks;
    }

    return array_values( array_filter( $all_blocks, function( $block_info ) use ( $disabled ) {
        return ! in_array( $block_info['metadata']['name'], $disabled, true );
    } ) );
}

/**
 * Filesystem fallback used when the generated block manifest is unavailable.
 *
 * @return array[]
 */
function acf_blocks_discover_block_metadata() {
    $blocks = array();
    $folders = glob( ACF_BLOCKS_PLUGIN_DIR . 'blocks/*', GLOB_ONLYDIR );

    foreach ( (array) $folders as $folder ) {
        $folder     = trailingslashit( $folder );
        $block_file = $folder . 'block.json';
        if ( ! is_readable( $block_file ) ) {
            continue;
        }
        $metadata = json_decode( (string) file_get_contents( $block_file ), true );
        if ( empty( $metadata['name'] ) ) {
            continue;
        }
        $blocks[] = array(
            'folder'       => $folder,
            'folder_name'  => basename( untrailingslashit( $folder ) ),
            'metadata'     => $metadata,
            'field_groups' => array(),
        );
    }

    return $blocks;
}

/**
 * Return block names disabled through the Block Manager.
 *
 * @return string[]
 */
function acf_blocks_get_disabled_blocks() {
    static $disabled = null;

    if ( null === $disabled ) {
        $disabled = function_exists( 'get_option' ) ? get_option( 'acf_blocks_disabled_blocks', array() ) : array();
        $disabled = is_array( $disabled ) ? array_values( array_filter( array_map( 'sanitize_text_field', $disabled ) ) ) : array();
        $disabled = apply_filters( 'acf_blocks_disabled_blocks', $disabled );
    }

    return $disabled;
}

const ACF_BLOCKS_SEMANTIC_STYLES_OPTION = 'acf_blocks_semantic_styles_enabled';

/**
 * Whether the optional semantic HTML fallback stylesheet should load.
 *
 * @return bool
 */
function acf_blocks_semantic_styles_enabled() {
    $enabled = function_exists( 'get_option' ) && (bool) get_option( ACF_BLOCKS_SEMANTIC_STYLES_OPTION, false );

    return (bool) apply_filters( 'acf_blocks_semantic_styles_enabled', $enabled );
}

/**
 * Add a predictable class to the first HTML element rendered by every ACF block.
 *
 * WordPress 6.2+ uses the HTML API. The conservative regular-expression
 * fallback keeps the plugin's WordPress 6.0 minimum working.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block data.
 * @return string
 */
function acf_blocks_add_common_wrapper_class( $block_content, $block ) {
    if ( empty( $block['blockName'] ) || 0 !== strpos( $block['blockName'], 'acf/' ) || '' === trim( $block_content ) ) {
        return $block_content;
    }

    if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
        $processor = new WP_HTML_Tag_Processor( $block_content );
        $ignored_tags = array( 'script', 'style', 'template', 'noscript', 'link', 'meta' );
        while ( $processor->next_tag() ) {
            if ( in_array( strtolower( $processor->get_tag() ), $ignored_tags, true ) ) {
                continue;
            }
            $processor->add_class( 'acf-block' );
            return $processor->get_updated_html();
        }
    }

    $updated = preg_replace_callback(
        '~(?:<!--.*?(?:-->|\z)|<!\[CDATA\[.*?(?:\]\]>|\z)|<(?:script|style|template|noscript)\b[^>]*>.*?(?:</(?:script|style|template|noscript)\s*>|\z))(*SKIP)(*F)|<(?!(?:script|style|template|noscript|link|meta)\b)([a-z][a-z0-9:-]*)\b([^>]*)>~is',
        function( $matches ) {
            $attributes = $matches[2];
            $class_pattern = '/\sclass\s*=\s*(["\'])(.*?)\1/is';

            if ( preg_match( $class_pattern, $attributes, $class_match ) ) {
                $classes = preg_split( '/\s+/', trim( $class_match[2] ) );
                if ( in_array( 'acf-block', $classes, true ) ) {
                    return $matches[0];
                }

                $attributes = preg_replace_callback(
                    $class_pattern,
                    function( $existing ) {
                        return ' class=' . $existing[1] . trim( $existing[2] . ' acf-block' ) . $existing[1];
                    },
                    $attributes,
                    1
                );
            } else {
                $attributes .= ' class="acf-block"';
            }

            return '<' . $matches[1] . $attributes . '>';
        },
        $block_content,
        1
    );

    return null === $updated ? $block_content : $updated;
}
add_filter( 'render_block', 'acf_blocks_add_common_wrapper_class', 10, 2 );

/**
 * Resolve a plugin asset to its minified build when one is available.
 *
 * `php tools/build-assets.php` writes a .min sibling next to every CSS and JS
 * source. This returns that sibling when it exists, so a missing or stale
 * artifact degrades to the readable source rather than to a 404.
 *
 * Defining SCRIPT_DEBUG serves the sources, which is what debugging a style
 * or a handler in devtools needs.
 *
 * @param string $relative Plugin-relative path, e.g. 'assets/css/tokens.css'.
 * @return array{path:string,url:string,min:bool} Resolved filesystem path and URL.
 */
function acf_blocks_asset( $relative ) {
    $source = array(
        'path' => ACF_BLOCKS_PLUGIN_DIR . $relative,
        'url'  => ACF_BLOCKS_PLUGIN_URL . $relative,
        'min'  => false,
    );

    if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
        return $source;
    }

    $minified = preg_replace( '/\.(css|js)$/', '.min.$1', $relative );
    if ( null === $minified || $minified === $relative ) {
        return $source;
    }

    $path = ACF_BLOCKS_PLUGIN_DIR . $minified;
    if ( ! is_readable( $path ) ) {
        return $source;
    }

    return array(
        'path' => $path,
        'url'  => ACF_BLOCKS_PLUGIN_URL . $minified,
        'min'  => true,
    );
}

/**
 * Swap an absolute asset path for its minified build when one exists.
 *
 * Used where code reads asset bytes off disk rather than enqueueing a URL —
 * notably the site-specific editor bundle, which concatenates block CSS.
 *
 * @param string $path Absolute path to a .css or .js file.
 * @return string The .min sibling when readable, otherwise the input path.
 */
function acf_blocks_minified_path( $path ) {
    if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
        return $path;
    }

    $minified = preg_replace( '/(?<!\.min)\.(css|js)$/', '.min.$1', $path );

    return ( null !== $minified && $minified !== $path && is_readable( $minified ) )
        ? $minified
        : $path;
}

/**
 * Serve the minified build for any plugin asset WordPress resolves on its own.
 *
 * Direct enqueues go through acf_blocks_asset(), but block.json `viewScript`
 * and `style` entries are resolved by WordPress from the metadata path, so
 * they never reach that helper. This rewrites those URLs at output time, which
 * also covers any asset added later without touching this file.
 *
 * Only rewrites plugin-owned URLs that have a readable .min sibling.
 *
 * @param string $src Registered source URL.
 * @return string Possibly rewritten URL.
 */
function acf_blocks_minify_loader_src( $src ) {
    if ( ! is_string( $src ) || '' === $src ) {
        return $src;
    }
    if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
        return $src;
    }
    if ( 0 !== strpos( $src, ACF_BLOCKS_PLUGIN_URL ) ) {
        return $src;
    }

    $parts = explode( '?', $src, 2 );
    $base  = $parts[0];

    if ( ! preg_match( '/(?<!\.min)\.(css|js)$/', $base ) ) {
        return $src;
    }

    $relative = substr( $base, strlen( ACF_BLOCKS_PLUGIN_URL ) );
    $minified = preg_replace( '/\.(css|js)$/', '.min.$1', $relative );

    if ( null === $minified || ! is_readable( ACF_BLOCKS_PLUGIN_DIR . $minified ) ) {
        return $src;
    }

    return ACF_BLOCKS_PLUGIN_URL . $minified . ( isset( $parts[1] ) ? '?' . $parts[1] : '' );
}
add_filter( 'script_loader_src', 'acf_blocks_minify_loader_src', 20 );
add_filter( 'style_loader_src', 'acf_blocks_minify_loader_src', 20 );

/**
 * Attach the design token bridge and block-gap fallback to every enabled block.
 *
 * WordPress loads each shared handle once and can keep it block-on-demand on
 * the frontend. Any normal theme or block selector outranks the fallback.
 *
 * The token sheet must ride along with every block: block CSS styles entirely
 * through --acfb-* custom properties, which resolve to Marketers Delight theme
 * tokens when MD is active and to neutral literals otherwise.
 */
function acf_blocks_register_layout_styles() {
    if ( ! function_exists( 'wp_enqueue_block_style' ) ) {
        return;
    }

    $sheets = array(
        'acf-blocks-tokens' => 'assets/css/tokens.css',
        'acf-blocks-layout' => 'assets/css/block-layout.css',
    );

    $blocks = acf_blocks_get_block_metadata_cache();

    foreach ( $sheets as $handle => $relative ) {
        if ( ! is_readable( ACF_BLOCKS_PLUGIN_DIR . $relative ) ) {
            continue;
        }

        $asset = acf_blocks_asset( $relative );
        $args  = array(
            'handle' => $handle,
            'src'    => $asset['url'],
            'path'   => $asset['path'],
            'ver'    => ACF_BLOCKS_VERSION,
        );

        foreach ( $blocks as $block_info ) {
            wp_enqueue_block_style( $block_info['metadata']['name'], $args );
        }
    }
}
add_action( 'init', 'acf_blocks_register_layout_styles', 6 );

/**
 * Load opt-in semantic HTML defaults in the block editor and frontend.
 */
function acf_blocks_enqueue_semantic_styles() {
    if ( ! acf_blocks_semantic_styles_enabled() ) {
        return;
    }

    if ( ! is_readable( ACF_BLOCKS_PLUGIN_DIR . 'assets/css/semantic-blocks.css' ) ) {
        return;
    }

    $asset = acf_blocks_asset( 'assets/css/semantic-blocks.css' );

    wp_enqueue_style(
        'acf-blocks-semantic-styles',
        $asset['url'],
        array(),
        ACF_BLOCKS_VERSION
    );
}
add_action( 'enqueue_block_assets', 'acf_blocks_enqueue_semantic_styles', 20 );

/**
 * Register custom block category for ACF Blocks.
 *
 * @param array $categories Existing block categories.
 * @return array Modified block categories.
 */
function acf_blocks_register_category( $categories ) {
    return array_merge(
        array(
            array(
                'slug'  => 'acf-blocks',
                'title' => __( 'ACF Blocks', 'acf-blocks' ),
                'icon'  => 'layout',
            ),
        ),
        $categories
    );
}
add_filter( 'block_categories_all', 'acf_blocks_register_category', 10, 1 );

/**
 * Pre-register block stylesheets so WordPress loads them as separate files.
 *
 * This ensures stylesheets are loaded as separate files in both the editor
 * and frontend when should_load_separate_assets() is true.
 */
function acf_blocks_register_styles() {
    $blocks_url = ACF_BLOCKS_PLUGIN_URL . 'blocks/';

    foreach ( acf_blocks_get_block_metadata_cache() as $block_info ) {
        $metadata    = $block_info['metadata'];
        $block_folder = $block_info['folder'];
        $folder_name = $block_info['folder_name'];

        // Check for style property in block.json
        if ( ! empty( $metadata['style'] ) && is_string( $metadata['style'] ) ) {
            // Extract filename from "file:./filename.css"
            if ( strpos( $metadata['style'], 'file:./' ) === 0 ) {
                $css_file = substr( $metadata['style'], 7 );
                $css_path = $block_folder . $css_file;

                if ( file_exists( $css_path ) ) {
                    $asset  = acf_blocks_asset( 'blocks/' . $folder_name . '/' . $css_file );
                    $handle = str_replace( '/', '-', $metadata['name'] ) . '-style';
                    wp_register_style(
                        $handle,
                        $asset['url'],
                        array(),
                        ACF_BLOCKS_VERSION
                    );
                }
            }
        }
    }
}
add_action( 'init', 'acf_blocks_register_styles', 5 );

/**
 * Load ACF blocks from block.json files.
 *
 * Scans the blocks directory and registers each block that has a block.json file.
 * Also auto-loads field groups from JSON files and optional extra.php files.
 */
function acf_blocks_load_blocks() {
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $started    = microtime( true );
    $registered = 0;

    foreach ( acf_blocks_get_block_metadata_cache() as $block_info ) {
        $block_folder = $block_info['folder'];
        $metadata     = $block_info['metadata'];
        $block_name   = $metadata['name'];
        $extra_php    = $block_folder . 'extra.php';
        $args         = array();

        // If we pre-registered a style, use the handle instead of file path
        if ( ! empty( $metadata['style'] ) ) {
            $handle = str_replace( '/', '-', $block_name ) . '-style';
            if ( wp_style_is( $handle, 'registered' ) ) {
                $args['style'] = $handle;
            }
        }

        /**
         * Filter the template path for an ACF block before registration.
         *
         * Allows themes and plugins to override the block template directory.
         *
         * @param string $block_folder Absolute path to the block folder.
         * @param string $block_name   The block name (e.g. "acf/post-display").
         */
        $block_folder = apply_filters( 'acf_blocks_template_path', $block_folder, $block_name );

        /**
         * Filter the block registration args before a block is registered.
         *
         * @param array  $args       The args array passed to register_block_type().
         * @param string $block_name The block name (e.g. "acf/post-display").
         */
        $args = apply_filters( 'acf_blocks_register_args', $args, $block_name );

        // Register via block.json metadata with style override
        $result = register_block_type( $block_folder, $args );

        if ( is_wp_error( $result ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf(
                    '[ACF Blocks] Failed to register block in "%s": %s',
                    $block_folder,
                    $result->get_error_message()
                ) );
            }
            continue;
        }

        $registered++;

        // Register ACF field groups from JSON files
        acf_blocks_register_field_groups( $block_folder, $block_info['field_groups'] ?? null );

        // Load extra.php if present
        if ( file_exists( $extra_php ) && is_readable( $extra_php ) ) {
            require_once $extra_php;
        }
    }

    $GLOBALS['acf_blocks_runtime_metrics']['registration_ms'] = ( microtime( true ) - $started ) * 1000;
    $GLOBALS['acf_blocks_runtime_metrics']['registered']      = $registered;
}
add_action( 'acf/init', 'acf_blocks_load_blocks', 5 );

/**
 * Register ACF field groups from JSON files in a block folder.
 *
 * Supports both single field group objects and arrays of field groups.
 *
 * @param string     $block_folder Absolute path to the block directory.
 * @param array|null $field_groups Generated field groups, or null for fallback discovery.
 */
function acf_blocks_register_field_groups( $block_folder, $field_groups = null ) {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    if ( is_array( $field_groups ) ) {
        foreach ( $field_groups as $group ) {
            if ( isset( $group['key'], $group['fields'] ) ) {
                acf_add_local_field_group( $group );
            }
        }
        return;
    }

    $json_files = glob( trailingslashit( $block_folder ) . '*.json' );

    if ( empty( $json_files ) ) {
        return;
    }

    foreach ( $json_files as $json_file ) {
        // Skip block.json files
        if ( substr( $json_file, -10 ) === 'block.json' ) {
            continue;
        }

        $raw = file_get_contents( $json_file );

        if ( false === $raw ) {
            continue;
        }

        $data = json_decode( $raw, true );

        if ( json_last_error() !== JSON_ERROR_NONE || empty( $data ) ) {
            continue;
        }

        // Normalize to an array of groups
        if ( isset( $data['key'], $data['fields'] ) ) {
            $data = array( $data );
        }

        if ( ! is_array( $data ) ) {
            continue;
        }

        foreach ( $data as $group ) {
            if ( isset( $group['key'], $group['fields'] ) ) {
                acf_add_local_field_group( $group );
            }
        }
    }
}

/**
 * Get icon markup from an icon field value.
 *
 * Handles both emoji/text output and CSS class-based icons.
 *
 * @param string $icon Raw icon value.
 * @return string Sanitized HTML markup.
 */
function acf_blocks_get_icon_markup( $icon ) {
    $icon = trim( (string) $icon );

    if ( '' === $icon ) {
        return '';
    }

    $contains_emoji   = preg_match( '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $icon );
    $looks_like_class = preg_match( '/^[A-Za-z0-9_\-\s:]+$/u', $icon )
        && ( false !== strpos( $icon, '-' ) || false !== strpos( $icon, ' ' ) );

    if ( $looks_like_class && ! $contains_emoji ) {
        return sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $icon ) );
    }

    return esc_html( $icon );
}


/**
 * Minify CSS string for inline output.
 *
 * Removes comments, whitespace, and unnecessary characters from CSS.
 *
 * @param string $css The CSS string to minify.
 * @return string Minified CSS.
 */
function acf_blocks_minify_css( $css ) {
    // Remove comments
    $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
    // Remove whitespace
    $css = preg_replace( '/\s+/', ' ', $css );
    // Remove space around selectors and braces
    $css = preg_replace( '/\s*([\{\};:,>~+])\s*/', '$1', $css );
    // Remove trailing semicolons before closing braces
    $css = str_replace( ';}', '}', $css );
    // Trim
    $css = trim( $css );
    return $css;
}

/**
 * Enqueue block editor assets for block transforms.
 *
 * Loads JavaScript that enables converting core blocks to ACF Blocks.
 */
function acf_blocks_enqueue_editor_assets() {
    if ( ! file_exists( ACF_BLOCKS_PLUGIN_DIR . 'assets/js/block-transforms.js' ) ) {
        return;
    }

    $asset = acf_blocks_asset( 'assets/js/block-transforms.js' );

    wp_enqueue_script(
        'acf-blocks-transforms',
        $asset['url'],
        array( 'wp-blocks', 'wp-hooks', 'wp-element' ),
        ACF_BLOCKS_VERSION,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'acf_blocks_enqueue_editor_assets' );

/**
 * Enqueue the generated ACF Blocks editor bundle.
 *
 * This ensures styles load at the bottom of the head for maximum specificity.
 * Uses a very high priority (999999) to load after theme and other plugin styles.
 */
function acf_blocks_enqueue_editor_styles() {
    if ( ! is_admin() ) {
        return;
    }

    // Block metadata registers per-block style and editorStyle handles. Remove
    // those in the editor and replace them with one cacheable bundle. Frontend
    // requests retain conditional per-block loading.
    foreach ( acf_blocks_get_block_metadata_cache() as $block_info ) {
        $base_handle = str_replace( '/', '-', $block_info['metadata']['name'] );
        wp_dequeue_style( $base_handle . '-style' );
        wp_dequeue_style( $base_handle . '-editor-style' );
        for ( $index = 2; $index <= 5; $index++ ) {
            wp_dequeue_style( $base_handle . '-style-' . $index );
            wp_dequeue_style( $base_handle . '-editor-style-' . $index );
        }
    }

    $site_bundle = get_option( 'acf_blocks_editor_bundle', array() );
    $site_bundle_is_valid = ! empty( $site_bundle['url'] )
        && ! empty( $site_bundle['path'] )
        && is_readable( $site_bundle['path'] );
    $bundle_url = $site_bundle_is_valid
        ? $site_bundle['url']
        : acf_blocks_asset( 'assets/css/editor-blocks.css' )['url'];
    $version    = $site_bundle_is_valid && ! empty( $site_bundle['version'] ) ? $site_bundle['version'] : ACF_BLOCKS_VERSION;

    wp_enqueue_style(
        'acf-blocks-editor-bundle',
        $bundle_url,
        array(),
        $version
    );
}
add_action( 'enqueue_block_assets', 'acf_blocks_enqueue_editor_styles', 999999 );

/**
 * Validate a heading tag against an allowed list.
 *
 * Used by block templates to sanitize user-selected title tags.
 *
 * @param string $tag     The tag value to validate.
 * @param string $default Fallback tag if validation fails. Default 'p'.
 * @return string A safe heading tag.
 */
function acf_blocks_validate_heading_tag( $tag, $default = 'p' ) {
    return in_array( $tag, array( 'p', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $tag : $default;
}

/**
 * Expose opt-in Server-Timing metrics for profiling environments.
 */
function acf_blocks_send_server_timing() {
    $enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
    if ( ! apply_filters( 'acf_blocks_server_timing_enabled', $enabled ) || headers_sent() ) {
        return;
    }

    $bootstrap = defined( 'ACF_BLOCKS_REQUEST_START' ) ? ( microtime( true ) - ACF_BLOCKS_REQUEST_START ) * 1000 : 0;
    $register  = (float) ( $GLOBALS['acf_blocks_runtime_metrics']['registration_ms'] ?? 0 );
    header( sprintf( 'Server-Timing: acf-blocks-bootstrap;dur=%.2f, acf-blocks-register;dur=%.2f', $bootstrap, $register ), false );
}
add_action( 'send_headers', 'acf_blocks_send_server_timing', 100 );
