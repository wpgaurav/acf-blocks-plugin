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
            // A space before ":" is a descendant combinator in a selector.
            'descendant pseudo-class' => array( '.a :is(b, ::before) { box-sizing: border-box; }', '.a :is(b,::before){box-sizing:border-box}' ),
            'descendant pseudo-elem'  => array( '.a ::selection { color: red; }', '.a ::selection{color:red}' ),
            'compound pseudo-class'   => array( 'a:hover, a :focus { color: red; }', 'a:hover,a :focus{color:red}' ),
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

    public function test_inlined_block_styles_are_minified(): void {
        // wp_maybe_inline_styles() reads the registered `path` off disk and
        // prints the bytes inline, so style_loader_src never sees the URL.
        // Blocks whose block.json declares `style` as an array get their
        // handles registered by core, path data and all — those were shipping
        // unminified source into every page that rendered one.
        $root = dirname( __DIR__ );

        $registered = static function ( $relative ) use ( $root ) {
            $style        = new stdClass();
            $style->src   = 'https://example.test/wp-content/plugins/acf-blocks-plugin/' . $relative;
            $style->extra = array( 'path' => $root . '/' . $relative );

            return $style;
        };

        $styles             = new stdClass();
        $styles->registered = array(
            'acf-toc-style'    => $registered( 'blocks/toc-block/toc-block.css' ),
            'acf-toc-style-2'  => $registered( 'blocks/toc-block/toc-runtime.css' ),
            'theme-stylesheet' => ( static function () {
                $style        = new stdClass();
                $style->src   = 'https://example.test/wp-content/themes/md/style.css';
                $style->extra = array( 'path' => '/srv/themes/md/style.css' );

                return $style;
            } )(),
        );

        $GLOBALS['wp_styles'] = $styles;
        acf_blocks_minify_registered_styles();

        foreach ( array( 'acf-toc-style' => 'toc-block', 'acf-toc-style-2' => 'toc-runtime' ) as $handle => $file ) {
            $this->assertStringEndsWith( "/blocks/toc-block/{$file}.min.css", $styles->registered[ $handle ]->src );
            $this->assertStringEndsWith( "/blocks/toc-block/{$file}.min.css", $styles->registered[ $handle ]->extra['path'] );
        }

        // Another plugin's or the theme's sheets are none of our business.
        $this->assertSame( 'https://example.test/wp-content/themes/md/style.css', $styles->registered['theme-stylesheet']->src );
        $this->assertSame( '/srv/themes/md/style.css', $styles->registered['theme-stylesheet']->extra['path'] );

        // Idempotent: the sweep runs on three hooks and must not produce
        // toc-block.min.min.css on the second pass.
        acf_blocks_minify_registered_styles();
        $this->assertStringEndsWith( '/blocks/toc-block/toc-block.min.css', $styles->registered['acf-toc-style']->extra['path'] );

        unset( $GLOBALS['wp_styles'] );
    }

    public function test_every_block_json_style_entry_has_a_minified_build(): void {
        // The sweep degrades to the source when a .min sibling is missing, so
        // a stale build would silently undo it rather than 404.
        $root    = dirname( __DIR__ );
        $missing = array();

        foreach ( (array) glob( $root . '/blocks/*/block.json' ) as $file ) {
            $metadata = json_decode( (string) file_get_contents( $file ), true );
            $folder   = dirname( $file );

            foreach ( array( 'style', 'editorStyle', 'viewStyle' ) as $key ) {
                foreach ( (array) ( $metadata[ $key ] ?? array() ) as $entry ) {
                    if ( ! is_string( $entry ) || 0 !== strpos( $entry, 'file:./' ) ) {
                        continue;
                    }

                    $source = $folder . '/' . substr( $entry, 7 );
                    $min    = preg_replace( '/\.css$/', '.min.css', $source );

                    if ( is_readable( $source ) && ! is_readable( $min ) ) {
                        $missing[] = substr( $min, strlen( $root ) + 1 );
                    }
                }
            }
        }

        $this->assertSame( array(), array_values( array_unique( $missing ) ), 'Run: composer build' );
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

    /**
     * A filled control must never take a surface token as its background while
     * its text stays inverted. --acfb-surface-* is a light neutral, so pairing
     * it with --acfb-on-primary (white) renders the control invisible. Filled
     * controls use --acfb-button, which the theme guarantees pairs with
     * --acfb-on-primary in both schemes.
     */
    public function test_filled_controls_are_not_painted_on_surface_tokens(): void {
        $offenders = array();

        foreach ( (array) glob( dirname( __DIR__ ) . '/blocks/*/*.css' ) as $file ) {
            if ( preg_match( '/\.min\.css$/', $file ) ) {
                continue;
            }

            $css = (string) file_get_contents( $file );

            preg_match_all( '/(--[a-z0-9-]+)\s*:\s*([^;{}]+)/i', $css, $m, PREG_SET_ORDER );
            $defs = array();
            foreach ( $m as $d ) {
                $defs[ $d[1] ] = trim( $d[2] );
            }

            foreach ( $defs as $name => $value ) {
                if ( ! preg_match( '/(btn|button|rank|badge|discount|copy|cta)[a-z-]*-bg$/', $name ) ) {
                    continue;
                }
                if ( false === strpos( $value, '--acfb-surface' ) ) {
                    continue;
                }

                $paired = $defs[ preg_replace( '/-bg$/', '-text', $name ) ] ?? '';
                if ( false !== strpos( $paired, '--acfb-on-primary' ) ) {
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

    public function test_star_rating_rest_sanitizers_accept_wordpress_callback_arguments(): void {
        // WP_REST_Request::sanitize_params() passes ( $value, $request, $param ).
        // Internal functions such as floatval() throw ArgumentCountError on PHP 8.
        foreach ( acf_star_rating_rest_args() as $param => $arg ) {
            $callback = $arg['sanitize_callback'];
            if ( is_string( $callback ) ) {
                $function = new ReflectionFunction( $callback );
                $this->assertFalse( $function->isInternal(), $param . ' uses internal function ' . $callback );
            }
            call_user_func( $callback, '4', null, $param );
        }

        $this->assertSame( 4.5, acf_star_rating_sanitize_float( '4.5', null, 'rating' ) );
        $this->assertSame( 0.0, acf_star_rating_sanitize_float( 'five' ) );
        $this->assertSame( 0.0, acf_star_rating_sanitize_float( array( 5 ) ) );
    }

    public function test_star_rating_script_is_localized_before_any_render(): void {
        // Block themes render content before wp_head fires wp_enqueue_scripts.
        $this->assertContains( 'acf_star_rating_register_assets', $GLOBALS['acf_blocks_test_actions']['init'] ?? array() );
        $this->assertNotContains( 'acf_star_rating_register_assets', $GLOBALS['acf_blocks_test_actions']['wp_enqueue_scripts'] ?? array() );

        acf_star_rating_register_assets();
        $data = $GLOBALS['acf_blocks_test_script_data']['acf-star-rating-block']['acfStarRating'] ?? array();
        $this->assertSame( 'https://example.test/wp-json/acf-blocks/v1/ratings', $data['restUrl'] ?? null );

        $template = file_get_contents( dirname( __DIR__ ) . '/blocks/star-rating-block/star-rating-block.php' );
        $this->assertStringNotContainsString( 'wp_localize_script', $template );
    }
    /**
     * @dataProvider product_box_price_cases
     */
    public function test_product_box_parses_prices( string $price, ?float $amount ): void {
        $parsed = acf_product_box_parse_price( $price );
        if ( null === $amount ) {
            $this->assertNull( $parsed );
            return;
        }
        $this->assertNotNull( $parsed );
        $this->assertEqualsWithDelta( $amount, $parsed['amount'], 0.001 );
    }

    public function product_box_price_cases(): array {
        return array(
            'dollars and cents'     => array( '$988.15', 988.15 ),
            'US thousands'          => array( '$1,299', 1299.0 ),
            'US thousands + cents'  => array( '$1,299.99', 1299.99 ),
            'Indian grouping'       => array( '₹1,29,999', 129999.0 ),
            'European'              => array( '1.299,00 €', 1299.0 ),
            'European no decimals'  => array( '€1.299', 1299.0 ),
            'short decimal comma'   => array( '12,5 €', 12.5 ),
            'currency code'         => array( 'USD 49', 49.0 ),
            'free is not a price'   => array( 'Free', null ),
            'leading words refused' => array( 'From $99', null ),
            'per-month refused'     => array( '$9.99/mo', null ),
            'empty'                 => array( '', null ),
        );
    }

    /**
     * @dataProvider product_box_savings_cases
     */
    public function test_product_box_savings( string $original, string $current, string $expected ): void {
        $this->assertSame( $expected, acf_product_box_savings( $original, $current ) );
    }

    public function product_box_savings_cases(): array {
        return array(
            'dollars'               => array( '$988.15', '$927.58', '$60.57' ),
            'whole dollars'         => array( '$1,299', '$999', '$300' ),
            'thousands in saving'   => array( '$2,499', '$1,199', '$1,300' ),
            'rupees under a lakh'   => array( '₹1,49,999', '₹1,29,999', '₹20,000' ),
            'rupees over a lakh'    => array( '₹2,49,999', '₹1,29,999', '₹1,20,000' ),
            'European'              => array( '1.299,00 €', '999,00 €', '300,00 €' ),
            'no saving'             => array( '$10', '$12', '' ),
            'equal prices'          => array( '$10', '$10', '' ),
            'currency mismatch'     => array( '$99', '€79', '' ),
            'unreadable current'    => array( '$99.99', 'Free', '' ),
            'missing original'      => array( '', '$79', '' ),
        );
    }

    public function test_product_box_formats_price_checked_date(): void {
        $acf = acf_product_box_format_date( '20260929' );
        $this->assertSame( '2026-09-29', $acf['iso'] );
        $this->assertSame( '29 Sep 2026', $acf['display'] );

        $this->assertSame( '2026-09-29', acf_product_box_format_date( '2026-09-29' )['iso'] );
        $this->assertNull( acf_product_box_format_date( '' ) );
        $this->assertNull( acf_product_box_format_date( 'not a date' ) );
    }

    public function test_product_box_accent_color_is_sanitized_and_contrasted(): void {
        $this->assertSame( '#ea580c', acf_product_box_sanitize_hex( ' #EA580C ' ) );
        $this->assertSame( '#abc', acf_product_box_sanitize_hex( '#abc' ) );
        $this->assertSame( '', acf_product_box_sanitize_hex( 'red' ) );
        $this->assertSame( '', acf_product_box_sanitize_hex( '#12345g' ) );
        $this->assertSame( '', acf_product_box_sanitize_hex( '#fff;background:url(x)' ) );

        $this->assertSame( '#111827', acf_product_box_contrast_text( '#ffd814' ) );
        $this->assertSame( '#ffffff', acf_product_box_contrast_text( '#1d4ed8' ) );
        $this->assertSame( '#ffffff', acf_product_box_contrast_text( '#000' ) );
    }

    public function test_product_box_link_attrs_add_noopener_once(): void {
        $this->assertSame( '', acf_product_box_link_attrs( '', false ) );
        $this->assertSame( ' rel="nofollow sponsored"', acf_product_box_link_attrs( 'nofollow  sponsored', false ) );
        $this->assertSame( ' rel="nofollow noopener" target="_blank"', acf_product_box_link_attrs( 'nofollow noopener', true ) );
        $this->assertSame( ' rel="noopener" target="_blank"', acf_product_box_link_attrs( '', true ) );
    }

    /**
     * Cropping is CSS-only: the server always sends an uncropped core size, and
     * no plugin sub-sizes are registered (they never were, in practice).
     */
    public function test_product_box_requests_uncropped_core_sizes(): void {
        foreach ( array( 'contain', 'cover' ) as $fit ) {
            $this->assertSame( 'large', acf_product_box_image_size( true, $fit, '16-9' ) );
            $this->assertSame( 'medium_large', acf_product_box_image_size( false, $fit, 'auto' ) );
        }
        $extra = file_get_contents( dirname( __DIR__ ) . '/blocks/product-box/extra.php' );
        $this->assertStringNotContainsString( 'add_image_size', $extra );
    }

    /**
     * Blocks saved before 2.12.0 carry none of the new fields, so every new
     * field must be optional and default to its "off" state.
     */
    public function test_product_box_new_fields_default_off(): void {
        $group  = json_decode( file_get_contents( dirname( __DIR__ ) . '/blocks/product-box/block-data.json' ), true );
        $fields = array();
        foreach ( $group['fields'] as $field ) {
            if ( 'tab' !== $field['type'] ) {
                $fields[ $field['name'] ] = $field;
            }
        }

        foreach ( array( 'pb_new_tab', 'pb_show_savings', 'pb_cta_emphasis', 'pb_btn_arrow' ) as $toggle ) {
            $this->assertSame( 0, $fields[ $toggle ]['default_value'], $toggle );
        }
        $this->assertSame( 'off', $fields['pb_btn_shine']['default_value'] );
        $this->assertSame( 'standard', $fields['pb_box_style']['default_value'] );
        $this->assertSame( 'contain', $fields['pb_image_fit']['default_value'] );
        foreach ( $fields as $name => $field ) {
            $this->assertSame( 0, $field['required'], $name );
        }
    }
}
