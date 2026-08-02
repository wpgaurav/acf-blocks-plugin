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
        $this->assertStringContainsString( 'border-inline-start: 0.3rem solid currentColor', $block_css );
        $this->assertStringContainsString( 'border-block-end: 1px solid color-mix(in srgb, currentColor 14%, transparent)', $block_css );
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

    public function test_tabs_keep_behavior_without_a_plugin_palette(): void {
        $root     = dirname( __DIR__ );
        $css      = file_get_contents( $root . '/blocks/tabs-block/tabs.css' );
        $template = file_get_contents( $root . '/blocks/tabs-block/tabs-block.php' );

        $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b/i', $css );
        $this->assertStringNotContainsString( 'rgb(', $css );
        $this->assertStringNotContainsString( 'color-mix(', $css );
        $this->assertStringNotContainsString( '[data-theme=', $css );
        $this->assertStringNotContainsString( 'prefers-color-scheme', $css );

        $this->assertStringContainsString( '.acf-tab-panel.active', $css );
        $this->assertStringContainsString( '.acf-tab-button:focus-visible', $css );
        $this->assertStringContainsString( '.acf-tabs-pills', $css );
        $this->assertStringContainsString( '.acf-tabs-underline', $css );
        $this->assertStringContainsString( '.acf-tabs-boxed', $css );
        $this->assertStringContainsString( 'acf-tab-button wp-element-button', $template );
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
        $this->assertStringEndsWith( '/assets/css/semantic-blocks.css', $GLOBALS['acf_blocks_test_styles']['acf-blocks-semantic-styles']['src'] );
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
