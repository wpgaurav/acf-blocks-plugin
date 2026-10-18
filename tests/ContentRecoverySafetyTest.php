<?php
use PHPUnit\Framework\TestCase;

final class ContentRecoverySafetyTest extends TestCase {
    /** @dataProvider literal_comment_examples */
    public function test_valid_json_comment_examples_survive_dangling_repair( $text ): void {
        $content = '<!-- wp:acf/callout ' . json_encode( array( 'data' => array( 'body' => $text ) ), JSON_UNESCAPED_SLASHES ) . ' /-->';
        $this->assertSame( $content, acf_blocks_migrator_strip_dangling_openers( $content, $count ) );
        $this->assertSame( 0, $count );
    }
    public function literal_comment_examples(): array {
        return array(
            array( 'Use <!-- wp:paragraph --> as an example.' ),
            array( '<!-- just a comment -->' ),
            array( 'Escaped "quote" and C:\\files\\example with <!-- wp:html -->' ),
            array( 'Nested braces {"example":true} <!-- wp:html -->' ),
        );
    }
    public function test_truly_dangling_openers_still_get_removed(): void {
        $content = '<!-- wp:acf/callout {"broken":<!-- wp:paragraph --><p>Keep me.</p><!-- /wp:paragraph -->';
        $this->assertSame( '<!-- wp:paragraph --><p>Keep me.</p><!-- /wp:paragraph -->', acf_blocks_migrator_strip_dangling_openers( $content, $count ) );
        $this->assertSame( 1, $count );
    }
    public function test_recovery_keeps_escaped_markup_inert(): void {
        $blocks = acf_blocks_recovery_html_to_blocks( '&lt;img src=x onerror=alert(1)&gt; &amp; "quoted"' );
        $this->assertCount( 1, $blocks );
        $this->assertSame( 'core/paragraph', $blocks[0]['blockName'] );
        $this->assertStringNotContainsString( '<img', $blocks[0]['innerHTML'] );
        $this->assertStringContainsString( '&lt;img src=x onerror=alert(1)&gt; &amp;', $blocks[0]['innerHTML'] );
    }
}
