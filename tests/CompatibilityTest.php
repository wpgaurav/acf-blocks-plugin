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
