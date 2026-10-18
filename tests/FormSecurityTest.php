<?php
use PHPUnit\Framework\TestCase;

final class FormSecurityTest extends TestCase {
    private function scenario( $name ) {
        $output = shell_exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __DIR__ . '/fixtures/form-security.php' ) . ' ' . escapeshellarg( $name ) );
        $value = json_decode( $output, true );
        $this->assertIsArray( $value, $output ?: 'Harness returned no JSON.' );
        return $value;
    }

    public function test_public_form_contains_only_reference_and_preserves_flat_fields(): void {
        $result = $this->scenario( 'render' );
        $this->assertStringContainsString( 'data-form-type="webhook"', $result['html'] );
        $this->assertStringNotContainsString( 'test-private-token', $result['html'] );
        $this->assertStringNotContainsString( 'hooks.example', $result['html'] );
        $this->assertNotEmpty( $result['config']['formRef'] );
    }

    public function test_flat_hidden_fields_and_attribute_groups_are_rendered(): void {
        $result = $this->scenario( 'render' );
        $this->assertStringContainsString( 'name="list_id"', $result['html'] );
        $this->assertStringContainsString( 'value="123"', $result['html'] );
        $this->assertStringContainsString( 'id="list-field"', $result['html'] );
        $this->assertStringContainsString( 'id="signup"', $result['html'] );
        $this->assertStringContainsString( 'custom-name', $result['html'] );
        $this->assertSame( array(), $result['warnings'] );
    }

    public function test_arbitrary_client_destination_is_rejected_without_network(): void {
        $result = $this->scenario( 'arbitrary' );
        $this->assertNotEmpty( $result['error'] );
        $this->assertSame( array(), $result['calls'] );
    }

    /** @dataProvider source_provider */
    public function test_saved_sources_resolve_safely_and_keep_multiline_headers( $source ): void {
        $result = $this->scenario( $source );
        $this->assertNull( $result['error'] );
        $this->assertCount( 1, $result['calls'] );
        $call = $result['calls'][0];
        $this->assertTrue( $call['safe'] );
        $this->assertSame( 'https://hooks.example/submit', $call['url'] );
        $this->assertSame( 0, $call['args']['redirection'] );
        $this->assertSame( 'Bearer test-private-token', $call['args']['headers']['Authorization'] );
        $this->assertSame( 'second-private-key', $call['args']['headers']['X-API-Key'] );
        $this->assertArrayNotHasKey( 'X-Override', $call['args']['headers'] );
        $this->assertSame( array( 'email_form_email' => 'test@example.com' ), json_decode( $call['args']['body'], true ) );
    }
    public function source_provider(): array { return array( array( 'submit' ), array( 'nested' ), array( 'cycle' ), array( 'template' ), array( 'template_ref' ), array( 'widget' ), array( 'unrelated' ) ); }

    /** @dataProvider invalid_provider */
    public function test_removed_private_or_tampered_sources_fail_closed( $scenario ): void {
        $result = $this->scenario( $scenario );
        $this->assertNotEmpty( $result['error'] );
        $this->assertSame( array(), $result['calls'] );
    }
    public function invalid_provider(): array { return array( array( 'changed' ), array( 'draft' ), array( 'tampered' ), array( 'private_template' ), array( 'inactive_widget' ), array( 'bad_headers' ), array( 'insecure' ) ); }

    public function test_redirect_is_not_reported_as_success(): void {
        $result = $this->scenario( 'redirect' );
        $this->assertSame( 'webhook_error', $result['error'] );
        $this->assertSame( 0, $result['calls'][0]['args']['redirection'] );
    }

    public function test_image_dimensions_rejects_local_streams_and_uses_bounded_safe_http(): void {
        $result = $this->scenario( 'dimensions' );
        $this->assertFalse( $result['local'] );
        $this->assertSame( array( 'width' => 1, 'height' => 1 ), $result['public'] );
        $this->assertCount( 1, $result['calls'] );
        $this->assertTrue( $result['calls'][0]['safe'] );
        $this->assertSame( 32768, $result['calls'][0]['args']['limit_response_size'] );
    }
}
