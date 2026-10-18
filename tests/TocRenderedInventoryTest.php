<?php

use PHPUnit\Framework\TestCase;

if ( ! function_exists( 'is_admin' ) ) {
    function is_admin() { return false; }
}
if ( ! function_exists( 'has_block' ) ) {
    function has_block( $name, $content ) { return false !== strpos( $content, $name ); }
}
if ( ! function_exists( 'sanitize_title' ) ) {
    function sanitize_title( $text ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) ), '-' ); }
}
if ( ! function_exists( 'wp_strip_all_tags' ) ) {
    function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
}
if ( ! function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode( $value, $options = 0, $depth = 512 ) { return json_encode( $value, $options, $depth ); }
}
if ( ! function_exists( 'get_permalink' ) ) {
    function get_permalink( $id ) { return 'https://example.test/post-' . $id; }
}
require_once dirname( __DIR__ ) . '/blocks/toc-block/extra.php';
// Load the real list/schema helpers through the template's editor preview path.
$block = array();
$is_preview = true;
$post_id = 0;
ob_start();
require dirname( __DIR__ ) . '/blocks/toc-block/toc-block.php';
ob_end_clean();


final class TocRenderedInventoryTest extends TestCase {
    private function render( $html, $include_acf = false ) {
        $GLOBALS['post'] = (object) array( 'post_content' => '<!-- wp:acf/toc /-->' );
        $nav = '<nav class="acf-toc" data-heading-levels="h2,h3" data-include-acf-headings="' . ( $include_acf ? '1' : '0' ) . '" data-list-type="ul"><h2 class="acf-toc__title">Contents</h2><div class="acf-toc__content"><!--acf-toc-list--></div></nav>';
        return acf_toc_add_heading_ids( $nav . $html );
    }

    public function test_final_rendered_inventory_includes_html_and_synced_headings_without_shifting_anchors(): void {
        $html = '<h2>Intro</h2><div class="acf-block"><h2>Intro</h2></div><h2 id="custom">HTML heading</h2><section><h3>Synced heading</h3></section>';
        $result = $this->render( $html );
        $this->assertStringContainsString( '<h2 id="intro">Intro</h2>', $result );
        $this->assertStringContainsString( 'href="#intro" class="acf-toc__link">Intro</a>', $result );
        $this->assertStringContainsString( 'href="#custom" class="acf-toc__link">HTML heading</a>', $result );
        $this->assertStringContainsString( 'href="#synced-heading" class="acf-toc__link">Synced heading</a>', $result );
        $this->assertStringNotContainsString( 'href="#intro-2"', $result );
        $this->assertStringContainsString( '<h2 id="intro-2">Intro</h2>', $result );
        $including = $this->render( $html, true );
        $this->assertStringContainsString( 'href="#intro-2" class="acf-toc__link">Intro</a>', $including );
    }

    public function test_id_inventory_reserves_custom_anchors_and_nonheading_ids(): void {
        $result = $this->render( '<div id="intro"></div><h2>Intro</h2><h2 id="intro-2">Custom</h2><h2>Intro</h2><h2 id="intro-2">Repeated custom</h2>' );
        $this->assertStringContainsString( '<div id="intro"></div><h2 id="intro-3">Intro</h2>', $result );
        $this->assertStringContainsString( '<h2 id="intro-2">Custom</h2>', $result );
        $this->assertStringContainsString( '<h2 id="intro-4">Intro</h2>', $result );
        $this->assertStringContainsString( 'href="#intro-3" class="acf-toc__link">Intro</a>', $result );
        $this->assertStringContainsString( 'href="#intro-2-2" class="acf-toc__link">Repeated custom</a>', $result );
    }

    public function test_toc_titles_scripts_templates_and_comments_do_not_pollute_inventory(): void {
        $unrelated = '<script>var example = "<h2 id=\"example\">Fake</h2>";</script><template><h2>Template</h2></template><!-- <h2>Comment</h2> -->';
        $result = $this->render( $unrelated . '<h2 data-note="a > b">Real &amp; useful</h2>' );
        $this->assertStringContainsString( $unrelated, $result );
        $this->assertSame( 1, substr_count( $result, 'class="acf-toc__link"' ) );
        $this->assertStringContainsString( 'href="#real-useful" class="acf-toc__link">Real &amp; useful</a>', $result );
        $this->assertStringNotContainsString( 'href="#contents"', $result );
    }

    public function test_multiple_tocs_share_ids_but_keep_level_and_acf_options(): void {
        $GLOBALS['post'] = (object) array( 'post_content' => '<!-- wp:acf/toc /-->' );
        $first = '<nav class="acf-toc" data-heading-levels="h2" data-include-acf-headings="0" data-list-type="ul"><div class="acf-toc__content"><!--acf-toc-list--></div></nav>';
        $second = '<nav class="acf-toc" data-heading-levels="h3" data-include-acf-headings="1" data-list-type="plain"><div class="acf-toc__content"><!--acf-toc-list--></div></nav>';
        $result = acf_toc_add_heading_ids( $first . $second . '<h2>Shared</h2><section class="acf-block"><h3>Shared</h3></section>' );
        $this->assertSame( 1, substr_count( $result, 'href="#shared"' ) );
        $this->assertSame( 1, substr_count( $result, 'href="#shared-2"' ) );
        $this->assertStringContainsString( 'acf-toc__list--plain', $result );
        $this->assertSame( $result, acf_toc_add_heading_ids( $result ) );
    }
    public function test_multi_root_acf_templates_are_excluded_using_render_provenance(): void {
        $acf = acf_toc_mark_acf_headings( '<h2>First</h2><h2>Second</h2>', array( 'blockName' => 'acf/custom' ) );
        $this->assertSame( 2, substr_count( $acf, 'data-acf-toc-heading="1"' ) );
        $this->assertSame( $acf, acf_toc_mark_acf_headings( $acf, array( 'blockName' => 'acf/custom' ) ) );
        $excluded = $this->render( $acf . '<h2>Visible</h2>' );
        $this->assertStringContainsString( 'href="#visible"', $excluded );
        $this->assertStringNotContainsString( 'href="#first"', $excluded );
        $included = $this->render( $acf . '<h2>Visible</h2>', true );
        $this->assertStringContainsString( 'href="#first"', $included );
        $this->assertStringContainsString( 'href="#second"', $included );
    }

    public function test_literal_nav_examples_are_not_rebuilt_as_real_tocs(): void {
        $example = '<script>const example = `<nav class="acf-toc"><h2>Fake</h2></nav>`;</script>';
        $result = $this->render( $example . '<h2>Real</h2>' );
        $this->assertStringContainsString( $example, $result );
        $this->assertStringContainsString( 'href="#real"', $result );
        $this->assertStringNotContainsString( 'href="#fake"', $result );
    }

    public function test_skipped_and_out_of_order_levels_produce_valid_nested_lists(): void {
        $headings = array();
        foreach ( array( 3, 2, 4, 3, 2 ) as $index => $level ) {
            $headings[] = array( 'level' => $level, 'id' => 'h-' . $index, 'text' => 'Heading ' . $index );
        }
        $html = acf_toc_build_list( $headings, 'ul', '', '' );
        $dom = new DOMDocument();
        $dom->loadHTML( $html );
        $xpath = new DOMXPath( $dom );
        $this->assertSame( 5, $xpath->query( '//a' )->length );
        $this->assertSame( 0, $xpath->query( '//ul/ul' )->length );
        $this->assertSame( 0, $xpath->query( '//body/li' )->length );
        $this->assertSame( 3, $xpath->query( '//body/ul/li' )->length );
    }

    public function test_attribute_values_cannot_impersonate_ids_or_acf_provenance(): void {
        $html = '<h2 title="an id=wrong data-acf-block marker" data-id="also-wrong" id="real">Title</h2>';
        $result = $this->render( $html );
        $this->assertStringContainsString( $html, $result );
        $this->assertStringContainsString( 'href="#real"', $result );
        $this->assertStringNotContainsString( 'href="#wrong"', $result );
        $collision = $this->render( '<div id="real"></div>' . $html );
        $this->assertStringContainsString( 'href="#real-2"', $collision );
        $this->assertStringContainsString( 'title="an id=wrong data-acf-block marker" data-id="also-wrong"', $collision );
    }

    public function test_schema_cannot_close_its_script_with_literal_heading_examples(): void {
        $heading = array( 'id' => 'example', 'text' => '</script><script>alert(1)</script>', 'level' => 2, 'tag' => 'h2' );
        $schema = acf_toc_generate_schema( array( $heading ), 17 );
        $this->assertSame( 1, substr_count( $schema, '</script>' ) );
        $this->assertStringContainsString( '\\u003C/script\\u003E', $schema );
        $json = substr( $schema, strpos( $schema, '>' ) + 1, -strlen( '</script>' ) );
        $this->assertSame( $heading['text'], json_decode( $json, true )['itemListElement'][0]['name'] );
    }

}
