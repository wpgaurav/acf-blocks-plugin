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
