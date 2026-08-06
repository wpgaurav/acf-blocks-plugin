<?php

use PHPUnit\Framework\TestCase;

final class CompatibilityTest extends TestCase {
    public function test_php_74_suffix_helper(): void {
        $this->assertTrue( acf_blocks_str_ends_with( 'cdn.example.com', '.example.com' ) );
        $this->assertTrue( acf_blocks_str_ends_with( 'abc', '' ) );
        $this->assertFalse( acf_blocks_str_ends_with( 'example.com', '.example.com' ) );
        $this->assertFalse( acf_blocks_str_ends_with( 'a', 'longer' ) );
    }

    public function test_generated_manifest_contains_all_blocks(): void {
        $manifest = require dirname( __DIR__ ) . '/includes/generated-block-manifest.php';
        $this->assertCount( 29, $manifest );
        foreach ( $manifest as $definition ) {
            $this->assertArrayHasKey( 'metadata', $definition );
            $this->assertArrayHasKey( 'field_groups', $definition );
            $this->assertStringStartsWith( 'acf/', $definition['metadata']['name'] );
        }
    }

    public function test_editor_bundle_contains_each_block_stylesheet(): void {
        $css = file_get_contents( dirname( __DIR__ ) . '/assets/css/editor-blocks.css' );
        $this->assertGreaterThanOrEqual( 29, substr_count( $css, '/* blocks/' ) );
    }

    public function test_toc_styles_inherit_from_the_theme(): void {
        $root        = dirname( __DIR__ );
        $block_css   = file_get_contents( $root . '/blocks/toc-block/toc-block.css' );
        $runtime_css = file_get_contents( $root . '/blocks/toc-block/toc-runtime.css' );
        $template    = file_get_contents( $root . '/blocks/toc-block/toc-block.php' );

        $visual_properties = array(
            '/\bbackground(?:-color)?\s*:/i',
            '/\bcolor\s*:/i',
            '/\bfont-size\s*:/i',
            '/\bfont-family\s*:/i',
        );
        foreach ( $visual_properties as $property ) {
            $this->assertDoesNotMatchRegularExpression( $property, $block_css );
            $this->assertDoesNotMatchRegularExpression( $property, $runtime_css );
        }

        $this->assertStringContainsString( '.acf-toc__list--ol', $block_css );
        $this->assertStringContainsString( '.acf-toc__list--ul', $block_css );
        $this->assertStringContainsString( '.acf-toc__list--plain', $block_css );
        $this->assertStringContainsString( 'border: 1px solid color-mix(in srgb, currentColor 18%, transparent)', $block_css );
        $this->assertStringContainsString( 'border-block-end: 1px solid color-mix(in srgb, currentColor 14%, transparent)', $block_css );
        $this->assertStringContainsString( '.acf-toc__content > .acf-toc__list', $block_css );
        $this->assertStringContainsString( '.acf-toc__list > .acf-toc__item:first-child', $block_css );
        $this->assertStringContainsString( '.acf-toc__list > .acf-toc__item:last-child', $block_css );
        $this->assertStringContainsString( 'padding-block-start: 1rem', $block_css );
        $this->assertStringNotContainsString( 'border-inline-start:', $block_css );
        $this->assertStringNotContainsString( 'border-radius:', $block_css );
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b/i', $block_css );
        $this->assertStringNotContainsString( 'rgb(', $block_css );
        $this->assertStringNotContainsString( '[data-theme=', $block_css );
        $this->assertStringNotContainsString( 'prefers-color-scheme', $block_css );
        $this->assertStringContainsString( '--acf-toc-sticky-offset:', $runtime_css );
        $this->assertStringContainsString( 'top: var(--acf-toc-sticky-offset)', $runtime_css );
        $this->assertStringContainsString( "'acf-toc__list--' . \$list_mode", $template );
        $this->assertStringNotContainsString( 'acf-toc__preview-notice" style=', $template );
    }

    public function test_accordion_uses_native_theme_styles(): void {
        $root     = dirname( __DIR__ );
        $metadata = json_decode( file_get_contents( $root . '/blocks/accordion-block/block.json' ), true );
        $manifest = require $root . '/includes/generated-block-manifest.php';

        $this->assertFileDoesNotExist( $root . '/blocks/accordion-block/accordion.css' );
        $this->assertArrayNotHasKey( 'style', $metadata );
        $this->assertArrayNotHasKey( 'editorStyle', $metadata );
        $this->assertArrayNotHasKey( 'style', $manifest['accordion-block']['metadata'] );
        $this->assertArrayNotHasKey( 'editorStyle', $manifest['accordion-block']['metadata'] );
    }

    public function test_tabs_style_through_tokens_not_literals(): void {
        $root     = dirname( __DIR__ );
        $css      = file_get_contents( $root . '/blocks/tabs-block/tabs.css' );
        $template = file_get_contents( $root . '/blocks/tabs-block/tabs-block.php' );

        // The block now carries its own visual treatment, but every value comes
        // from a token, so the theme's dark toggle still drives it and no
        // [data-theme] rules are needed here.
        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b/i', $css );
        $this->assertStringNotContainsString( 'rgb(', $css );
        $this->assertStringNotContainsString( '[data-theme=', $css );
        $this->assertStringNotContainsString( 'prefers-color-scheme', $css );
        $this->assertStringContainsString( 'var(--acfb-', $css );

        $this->assertStringContainsString( '.acf-tab-panel.active', $css );
        $this->assertStringContainsString( '.acf-tab-button:focus-visible', $css );
        $this->assertStringContainsString( '.acf-tabs-pills', $css );
        $this->assertStringContainsString( '.acf-tabs-underline', $css );
        $this->assertStringContainsString( '.acf-tabs-boxed', $css );

        // wp-element-button made every tab render as a filled CTA button.
        $this->assertStringNotContainsString( 'wp-element-button', $template );
    }

    public function test_tabs_behaviour_is_external_and_keyboard_accessible(): void {
        $root     = dirname( __DIR__ );
        $template = file_get_contents( $root . '/blocks/tabs-block/tabs-block.php' );
        $script   = file_get_contents( $root . '/blocks/tabs-block/tabs.js' );
        $metadata = json_decode( file_get_contents( $root . '/blocks/tabs-block/block.json' ), true );

        // No inline handlers, and no inline <script> left in the template.
        $this->assertStringNotContainsString( 'onclick', $template );
        $this->assertStringNotContainsString( '<script', $template );
        $this->assertSame( 'file:./tabs.js', $metadata['viewScript'] );

        // ARIA tabs keyboard pattern.
        foreach ( array( 'ArrowRight', 'ArrowLeft', 'ArrowDown', 'ArrowUp', 'Home', 'End' ) as $key ) {
            $this->assertStringContainsString( "'" . $key . "'", $script );
        }

        // Roving tabindex: exactly one tab reachable via Tab.
        $this->assertStringContainsString( 'tabindex', $script );
        $this->assertStringContainsString( 'tabindex="<?php echo $is_active ? \'0\' : \'-1\'; ?>"', $template );
    }

    public function test_section_styles_are_structural_only(): void {
        $root     = dirname( __DIR__ );
        $css      = file_get_contents( $root . '/blocks/section-block/section-block.css' );
        $template = file_get_contents( $root . '/blocks/section-block/section-block.php' );

        $this->assertStringNotContainsString( 'color:', $css );
        $this->assertStringNotContainsString( 'background:', $css );
        $this->assertStringNotContainsString( '[data-theme=', $css );
        $this->assertStringNotContainsString( 'prefers-color-scheme', $css );
        $this->assertStringNotContainsString( 'max-width:', $css );
        $this->assertStringNotContainsString( 'padding:', $css );
        $this->assertStringContainsString( '.acf-section-bg-video', $css );
        $this->assertStringContainsString( '.acf-section-bg-overlay', $css );
        $this->assertStringContainsString( "array( 'acf-section-block' )", $template );
        $this->assertStringContainsString( "'align' . \$block['align']", $template );
    }

    public function test_common_acf_block_class_is_added_without_touching_core_blocks(): void {
        $acf_html = '<nav class="acf-toc"><p>Contents</p></nav>';
        $rendered = acf_blocks_add_common_wrapper_class( $acf_html, array( 'blockName' => 'acf/toc' ) );

        $this->assertStringContainsString( 'class="acf-toc acf-block"', $rendered );
        $this->assertSame(
            $rendered,
            acf_blocks_add_common_wrapper_class( $rendered, array( 'blockName' => 'acf/toc' ) )
        );
        $this->assertSame(
            '<p>Core paragraph</p>',
            acf_blocks_add_common_wrapper_class( '<p>Core paragraph</p>', array( 'blockName' => 'core/paragraph' ) )
        );
        $this->assertStringContainsString(
            '<section class="acf-block">',
            acf_blocks_add_common_wrapper_class( '<section><p>Content</p></section>', array( 'blockName' => 'acf/section-block' ) )
        );

        $script_first = '<script>window.config = {};</script><div class="acf-email-form-wrapper"><form></form></div>';
        $rendered     = acf_blocks_add_common_wrapper_class( $script_first, array( 'blockName' => 'acf/email-form' ) );
        $this->assertStringContainsString( '<script>window.config = {};</script>', $rendered );
        $this->assertStringContainsString( 'class="acf-email-form-wrapper acf-block"', $rendered );
        $this->assertStringNotContainsString( '<script class="acf-block">', $rendered );

        $comment_first = '<!-- Example: <aside>not markup</aside> --><article><p>Content</p></article>';
        $rendered      = acf_blocks_add_common_wrapper_class( $comment_first, array( 'blockName' => 'acf/callout' ) );
        $this->assertStringContainsString( '<!-- Example: <aside>not markup</aside> -->', $rendered );
        $this->assertStringContainsString( '<article class="acf-block">', $rendered );
    }

    public function test_common_block_gap_is_zero_specificity_and_block_scoped(): void {
        $root = dirname( __DIR__ );
        $css  = file_get_contents( $root . '/assets/css/block-layout.css' );

        $this->assertStringContainsString( ':where(.acf-block)', $css );
        $this->assertStringContainsString( 'margin-block-end: 1.5rem', $css );
        $this->assertStringNotContainsString( '!important', $css );
        $this->assertStringNotContainsString( 'background', $css );
        $this->assertStringNotContainsString( 'border', $css );

        $GLOBALS['acf_blocks_test_block_styles'] = array();
        acf_blocks_register_layout_styles();

        $this->assertCount( 29, $GLOBALS['acf_blocks_test_block_styles'] );
        foreach ( $GLOBALS['acf_blocks_test_block_styles'] as $block_name => $args ) {
            $this->assertStringStartsWith( 'acf/', $block_name );
            $this->assertSame( 'acf-blocks-layout', $args['handle'] );
            // Minified build is served whenever it exists and SCRIPT_DEBUG is off.
            $this->assertStringEndsWith( '/assets/css/block-layout.min.css', $args['src'] );
            $this->assertSame( ACF_BLOCKS_VERSION, $args['ver'] );
        }
    }

    public function test_semantic_styles_are_opt_in_and_zero_specificity(): void {
        $root = dirname( __DIR__ );
        $css  = file_get_contents( $root . '/assets/css/semantic-blocks.css' );

        $this->assertStringContainsString( ':where(.acf-block)', $css );
        $this->assertStringNotContainsString( '!important', $css );
        $this->assertFalse( acf_blocks_semantic_styles_enabled() );

        $GLOBALS['acf_blocks_test_styles'] = array();
        acf_blocks_enqueue_semantic_styles();
        $this->assertArrayNotHasKey( 'acf-blocks-semantic-styles', $GLOBALS['acf_blocks_test_styles'] );

        $GLOBALS['acf_blocks_test_options'][ ACF_BLOCKS_SEMANTIC_STYLES_OPTION ] = 1;
        $this->assertTrue( acf_blocks_semantic_styles_enabled() );
        acf_blocks_enqueue_semantic_styles();
        $this->assertArrayHasKey( 'acf-blocks-semantic-styles', $GLOBALS['acf_blocks_test_styles'] );
        $this->assertStringEndsWith( '/assets/css/semantic-blocks.min.css', $GLOBALS['acf_blocks_test_styles']['acf-blocks-semantic-styles']['src'] );
        unset( $GLOBALS['acf_blocks_test_options'][ ACF_BLOCKS_SEMANTIC_STYLES_OPTION ] );
    }

    public function test_license_page_exposes_semantic_style_setting(): void {
        $source = file_get_contents( dirname( __DIR__ ) . '/includes/performance-manager.php' );

        $this->assertStringContainsString( "'save_semantic_styles'", $source );
        $this->assertStringContainsString( 'semantic_styles_enabled', $source );
        $this->assertStringContainsString( 'Load semantic fallback block styles', $source );
    }

    public function test_faq_schema_stays_removed(): void {
        $root = dirname( __DIR__ );

        // Google dropped FAQ rich results; the accordion must emit no FAQPage
        // JSON-LD, and must ignore the flag older posts still carry.
        $template = file_get_contents( $root . '/blocks/accordion-block/accordion-block.php' );
        $this->assertStringNotContainsString( 'FAQPage', $template );
        $this->assertStringNotContainsString( 'acf_accord_enable_faq_schema', $template );
        $this->assertStringNotContainsString( 'ld+json', $template );

        // The migrator may reference the key to strip it, but must never write
        // it (or its _field reference) back into a block's data array.
        $migrator = file_get_contents( $root . '/includes/block-migrator.php' );
        $this->assertDoesNotMatchRegularExpression(
            '/[\'"]_?acf_accord_enable_faq_schema[\'"]\s*=>/',
            $migrator
        );

        // The field must be gone from the registered field group.
        $manifest = require $root . '/includes/generated-block-manifest.php';
        $fields   = $manifest['accordion-block']['field_groups'][0]['fields'];
        $this->assertNotContains( 'acf_accord_enable_faq_schema', array_column( $fields, 'name' ) );
        $this->assertNotContains( 'field_acf_accord_enable_faq_schema', array_column( $fields, 'key' ) );
    }

    public function test_migrator_strips_retired_faq_schema_flag(): void {
        $stats   = array();
        $changed = false;

        $out = acf_blocks_migrator_transform_list(
            array(
                array(
                    'blockName'   => 'acf/accordion',
                    'attrs'       => array(
                        'data' => array(
                            'acf_accord_enable_faq_schema'                 => '1',
                            '_acf_accord_enable_faq_schema'                => 'field_acf_accord_enable_faq_schema',
                            'acf_accord_groups'                            => '1',
                            '_acf_accord_groups'                           => 'field_acf_accord_groups',
                            'acf_accord_groups_0_acf_accord_group_title'   => 'Q',
                            '_acf_accord_groups_0_acf_accord_group_title'  => 'field_acf_accord_group_title',
                            'acf_accord_groups_0_acf_accord_group_content' => '<p>A</p>',
                            'acf_accordion_class'                          => 'my-faq',
                        ),
                    ),
                    'innerBlocks' => array(),
                ),
            ),
            $stats,
            $changed
        );

        $data = $out[0]['attrs']['data'];

        // Retired flag and its reference are gone.
        $this->assertArrayNotHasKey( 'acf_accord_enable_faq_schema', $data );
        $this->assertArrayNotHasKey( '_acf_accord_enable_faq_schema', $data );

        // Everything else survives untouched.
        $this->assertSame( '1', $data['acf_accord_groups'] );
        $this->assertSame( 'Q', $data['acf_accord_groups_0_acf_accord_group_title'] );
        $this->assertSame( '<p>A</p>', $data['acf_accord_groups_0_acf_accord_group_content'] );
        $this->assertSame( 'my-faq', $data['acf_accordion_class'] );
        $this->assertSame( 'field_acf_accord_groups', $data['_acf_accord_groups'] );

        // The post is flagged for rewrite and counted under its own category.
        $this->assertTrue( $changed );
        $this->assertSame( 1, $stats['accordion-faq-schema'] );

        // Reported categories must all have a label, or the badge renders blank.
        $cats = acf_blocks_migrator_categories();
        $this->assertArrayHasKey( 'accordion-faq-schema', $cats );
    }

    public function test_migrator_leaves_clean_accordion_untouched(): void {
        $stats   = array();
        $changed = false;

        $clean = array(
            'acf_accord_groups'                          => '1',
            '_acf_accord_groups'                         => 'field_acf_accord_groups',
            'acf_accord_groups_0_acf_accord_group_title' => 'Q',
        );

        $out = acf_blocks_migrator_transform_list(
            array(
                array(
                    'blockName'   => 'acf/accordion',
                    'attrs'       => array( 'data' => $clean ),
                    'innerBlocks' => array(),
                ),
            ),
            $stats,
            $changed
        );

        // No flag to strip: nothing changes, so no needless post rewrite.
        $this->assertSame( $clean, $out[0]['attrs']['data'] );
        $this->assertFalse( $changed );
        $this->assertArrayNotHasKey( 'accordion-faq-schema', $stats );
    }

    /**
     * The minifiers are scanners, not regex passes. These are the cases where a
     * regex-based minifier silently corrupts output.
     *
     * @dataProvider css_minify_cases
     */
    public function test_css_minifier_preserves_meaning( string $source, string $expected ): void {
        $this->assertSame( $expected, acfb_minify_css( $source ) );
    }

    public function css_minify_cases(): array {
        return array(
            'strips comments'         => array( 'a { /* x */ color: red; }', 'a{color:red}' ),
            'keeps bang banner'       => array( "/*! keep */\na { color: red; }", '/*! keep */a{color:red}' ),
            'url in string'           => array( 'a::after{content:"https://x.com"}', 'a::after{content:"https://x.com"}' ),
            'comment open in string'  => array( 'a::after{content:"/* x"}', 'a::after{content:"/* x"}' ),
            'semicolon in string'     => array( 'a::after{content:"a;b"}', 'a::after{content:"a;b"}' ),
            'data uri'                => array( 'a{background:url(data:image/svg+xml;base64,AA//BB)}', 'a{background:url(data:image/svg+xml;base64,AA//BB)}' ),
            'descendant space kept'   => array( 'a b { color: red; }', 'a b{color:red}' ),
            'child combinator'        => array( 'a > b { color: red; }', 'a>b{color:red}' ),
            // Space after "@media" is required; "@media(" fails to parse.
            'media query space'       => array( '@media (max-width: 480px) { a { color: red; } }', '@media (max-width:480px){a{color:red}}' ),
            // Space after ")" is significant inside color-mix percentages.
            'color-mix percentage'    => array( 'a{color:color-mix(in srgb, var(--x) 3%, var(--y))}', 'a{color:color-mix(in srgb,var(--x) 3%,var(--y))}' ),
            // calc() requires whitespace around + and -.
            'calc keeps operators'    => array( 'a{width:calc(100% - 2px)}', 'a{width:calc(100% - 2px)}' ),
        );
    }

    /**
     * @dataProvider js_minify_cases
     */
    public function test_js_minifier_preserves_meaning( string $source, string $expected ): void {
        $this->assertSame( $expected, acfb_minify_js( $source ) );
    }

    public function js_minify_cases(): array {
        return array(
            'line comment'        => array( "var a = 1; // note\nvar b = 2;", "var a = 1;\nvar b = 2;" ),
            'url in string'       => array( 'var u = "https://x.com";', 'var u = "https://x.com";' ),
            'comment in string'   => array( 'var s = "/* x */";', 'var s = "/* x */";' ),
            'regex with slashstar'=> array( 'var re = /\/\*/g;', 'var re = /\/\*/g;' ),
            'regex char class'    => array( 'var re = /[/]/;', 'var re = /[/]/;' ),
            'division not regex'  => array( "var x = a / b;\nvar y = c / d;", "var x = a / b;\nvar y = c / d;" ),
            'template literal'    => array( 'var s = `a ${b} // c`;', 'var s = `a ${b} // c`;' ),
            // Newlines are kept so automatic semicolon insertion is unchanged.
            'asi preserved'       => array( "function f() {\n  return\n  1;\n}", "function f() {\nreturn\n1;\n}" ),
            'blank lines'         => array( "var a = 1;\n\n\nvar b = 2;", "var a = 1;\nvar b = 2;" ),
        );
    }

    public function test_every_shipped_asset_has_a_minified_build(): void {
        $root    = dirname( __DIR__ );
        $sources = array_merge(
            (array) glob( $root . '/assets/css/*.css' ),
            (array) glob( $root . '/assets/js/*.js' ),
            (array) glob( $root . '/blocks/*/*.css' ),
            (array) glob( $root . '/blocks/*/*.js' )
        );

        $missing = array();
        foreach ( $sources as $file ) {
            if ( preg_match( '/\.min\.(css|js)$/', $file ) ) {
                continue;
            }
            $min = preg_replace( '/\.(css|js)$/', '.min.$1', $file );
            if ( ! is_readable( $min ) ) {
                $missing[] = substr( $file, strlen( $root ) + 1 );
            }
        }

        $this->assertSame( array(), $missing, 'Run: composer build' );
    }

    public function test_asset_resolver_falls_back_to_source(): void {
        // Present: the minified build wins.
        $built = acf_blocks_asset( 'assets/css/tokens.css' );
        $this->assertTrue( $built['min'] );
        $this->assertStringEndsWith( '/assets/css/tokens.min.css', $built['url'] );
        $this->assertStringEndsWith( '/assets/css/tokens.min.css', $built['path'] );

        // Absent: degrade to the readable source rather than emitting a 404.
        $missing = acf_blocks_asset( 'assets/css/does-not-exist.css' );
        $this->assertFalse( $missing['min'] );
        $this->assertStringEndsWith( '/assets/css/does-not-exist.css', $missing['url'] );

        // Non-asset paths pass through untouched.
        $other = acf_blocks_asset( 'assets/img/logo.svg' );
        $this->assertFalse( $other['min'] );
        $this->assertStringEndsWith( '/assets/img/logo.svg', $other['url'] );
    }

    public function test_editor_bundle_excludes_minified_siblings(): void {
        // Globbing blocks/*/*.css would otherwise pull in the .min.css files
        // and include every block's CSS in the bundle twice.
        $css = file_get_contents( dirname( __DIR__ ) . '/assets/css/editor-blocks.css' );
        $this->assertStringNotContainsString( '.min.css */', $css );
    }

    /**
     * House rule: a rounded container must never carry a >=2px border, on any
     * single side. That combination is the generic AI-callout signature —
     * conflicting geometry that adds weight without adding information.
     *
     * Narrow exceptions: focus rings, checkbox/radio indicators, avatar rings,
     * buttons, timeline axes and the loading spinner, none of which are cards,
     * callouts, panels or content boxes.
     */
    public function test_no_thick_border_on_rounded_containers(): void {
        $exempt = array(
            'focus', 'spinner', 'checkbox', 'checkmark', 'avatar', 'img',
            'timeline', 'btn', 'button', '::before', '::after', 'icon--empty',
        );

        $offenders = array();

        foreach ( (array) glob( dirname( __DIR__ ) . '/blocks/*/*.css' ) as $file ) {
            if ( preg_match( '/\.min\.css$/', $file ) ) {
                continue;
            }

            $css = preg_replace( '#/\*.*?\*/#s', '', (string) file_get_contents( $file ) );

            if ( ! preg_match_all( '/([^{}]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER ) ) {
                continue;
            }

            foreach ( $rules as $rule ) {
                $selector = strtolower( trim( $rule[1] ) );
                $body     = $rule[2];

                foreach ( $exempt as $needle ) {
                    if ( false !== strpos( $selector, $needle ) ) {
                        continue 2;
                    }
                }

                // Any border shorthand or side declaration of 2px or more.
                $thick = false;
                if ( preg_match_all( '/border(?:-(?:top|right|bottom|left|block|inline)[a-z-]*)?\s*:\s*([^;{}]+)/i', $body, $borders ) ) {
                    foreach ( $borders[1] as $value ) {
                        if ( false !== stripos( $value, 'radius' ) ) {
                            continue;
                        }
                        if ( preg_match( '/(\d+(?:\.\d+)?)px/', $value, $px ) && (float) $px[1] >= 2 ) {
                            $thick = true;
                        }
                    }
                }

                $rounded = preg_match( '/border(?:-[a-z-]*)?radius\s*:\s*(?!0[;\s}])/i', $body );

                if ( $thick && $rounded ) {
                    $offenders[] = basename( dirname( $file ) ) . ': ' . trim( $rule[1] );
                }
            }
        }

        $this->assertSame( array(), $offenders );
    }

    /**
     * The video player is absolutely positioned, so the wrapper is the only
     * source of height. When the stylesheet did not apply the block collapsed
     * to 0px and vanished — visible on the front end, invisible in the editor.
     * The ratio is now inline, so the box survives with no CSS at all.
     */
    public function test_video_block_holds_its_box_without_css(): void {
        $root     = dirname( __DIR__ );
        $template = file_get_contents( $root . '/blocks/video-block/video-block.php' );
        $css      = file_get_contents( $root . '/blocks/video-block/video.css' );

        $this->assertStringContainsString( 'aspect-ratio: ', $template );
        $this->assertStringContainsString( "'16-9' => '16 / 9'", $template );
        $this->assertStringContainsString( '$wrapper_style_at', $template );

        // The padding-bottom hack must stay behind @supports, or it stacks on
        // top of the inline aspect-ratio and doubles the height.
        $this->assertStringContainsString( '@supports not (aspect-ratio:1/1)', $css );

        $outside = preg_replace( '/@supports not \(aspect-ratio:1\/1\)\{.*?\}\}/s', '', $css );
        $this->assertStringNotContainsString( 'padding-bottom:56.25%', (string) $outside );
    }

    /**
     * Blocks style through the --acfb-* bridge, so the theme's own light/dark
     * toggle carries them. A hand-rolled [data-theme] rule in block CSS means
     * a colour escaped the token layer and will drift out of sync.
     */
    public function test_blocks_have_no_hand_rolled_dark_overrides(): void {
        $offenders = array();

        foreach ( (array) glob( dirname( __DIR__ ) . '/blocks/*/*.css' ) as $file ) {
            if ( preg_match( '/\.min\.css$/', $file ) ) {
                continue;
            }
            // Comments may mention the attribute; only real rules count.
            $css = preg_replace( '#/\*.*?\*/#s', '', (string) file_get_contents( $file ) );
            if ( preg_match( '/(?:\[data-theme|\.is-dark-theme)[^{}]*\{/', (string) $css ) ) {
                $offenders[] = basename( dirname( $file ) );
            }
        }

        $this->assertSame( array(), $offenders );
    }

    /**
     * Button fills must pair --acfb-button with --acfb-on-primary. MD lightens
     * --color-primary for dark mode, which drops white button text to ~3.3:1,
     * while --color-button is stable and guaranteed to pair with its text.
     */
    public function test_button_fills_use_the_button_token(): void {
        $tokens = file_get_contents( dirname( __DIR__ ) . '/assets/css/tokens.css' );
        $this->assertStringContainsString( '--acfb-button:', $tokens );

        $offenders = array();
        foreach ( (array) glob( dirname( __DIR__ ) . '/blocks/*/*.css' ) as $file ) {
            if ( preg_match( '/\.min\.css$/', $file ) ) {
                continue;
            }
            $css = (string) file_get_contents( $file );
            if ( preg_match_all( '/(--[a-z0-9-]*(?:btn|button)[a-z0-9-]*(?:bg|hover))\s*:\s*var\(--acfb-primary\)/i', $css, $m ) ) {
                foreach ( $m[1] as $name ) {
                    $offenders[] = basename( dirname( $file ) ) . ': ' . $name;
                }
            }
        }

        $this->assertSame( array(), $offenders );
    }

    public function test_performance_regressions_stay_removed(): void {
        $root = dirname( __DIR__ );
        $toc = file_get_contents( $root . '/blocks/toc-block/toc-block.php' );
        $localizer = file_get_contents( $root . '/includes/image-localizer.php' );
        $migrator = file_get_contents( $root . '/includes/block-migrator.php' );

        $this->assertStringNotContainsString( '$post_content = do_blocks', $toc );
        $this->assertStringNotContainsString( "add_filter( 'wp_insert_post_data'", $localizer );
        $this->assertStringNotContainsString( "'posts_per_page' => -1", $migrator );

        foreach ( array(
            '/blocks/callout/template.php',
            '/blocks/feature-grid-block/feature-grid-block.php',
            '/blocks/post-display/post-display.php',
        ) as $template ) {
            $this->assertStringNotContainsString( '<style>', file_get_contents( $root . $template ) );
        }
    }
}
