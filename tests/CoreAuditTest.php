<?php
use PHPUnit\Framework\TestCase;

final class CoreAuditTest extends TestCase {
    private function probe( string $name ): array {
        $pipes = array();
        $process = proc_open( array( PHP_BINARY, __DIR__ . '/fixtures/core-audit.php', $name ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        fclose( $pipes[0] );
        $output = stream_get_contents( $pipes[1] );
        $errors = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $this->assertSame( 0, proc_close( $process ), $errors );
        $result = json_decode( $output, true );
        $this->assertIsArray( $result, $output . $errors );
        $this->assertNotEmpty( $result );
        return $result;
    }

    public function test_failed_license_deactivation_preserves_credentials(): void {
        $result = $this->probe( 'license' );
        foreach ( array( 'transport', 'rejected' ) as $failure ) {
            $this->assertTrue( $result[ $failure ]['error'], $failure );
            $this->assertSame( 'test-key', $result[ $failure ]['stored']['license_key'], $failure );
            $this->assertSame( 'test-activation', $result[ $failure ]['stored']['activation_hash'], $failure );
        }
        $this->assertFalse( $result['confirmed']['error'] );
        $this->assertSame( '', $result['confirmed']['stored']['license_key'] );
    }

    public function test_voter_identity_ignores_proxy_headers_unless_explicitly_trusted(): void {
        $result = $this->probe( 'proxy' );
        $this->assertTrue( $result['default_ignores_spoofed_headers'] );
        $this->assertTrue( $result['explicit_trust_uses_client_ip'] );
    }

    public function test_filesystem_fallback_registers_the_block_fields(): void {
        $result = $this->probe( 'manifest' );
        $this->assertGreaterThanOrEqual( 33, $result['blocks'] );
        $this->assertGreaterThanOrEqual( $result['blocks'], $result['groups'] );
    }

    public function test_url_import_requires_permission_to_edit_its_parent_post(): void {
        $result = $this->probe( 'import-permission' );
        $this->assertNotSame( 'import-reached-query', $result['other_post'] );
        $this->assertSame( 'import-reached-query', $result['own_post'] );
        $this->assertSame( 'import-reached-query', $result['unattached'] );
    }

    public function test_plugin_assets_retire_explicit_old_block_versions(): void {
        $metadata = acf_blocks_version_block_assets( array( 'file' => ACF_BLOCKS_PLUGIN_DIR . 'blocks/toc-block/block.json', 'version' => '2.10.2' ) );
        $this->assertSame( ACF_BLOCKS_VERSION, $metadata['version'] );
        $external = array( 'file' => '/other-plugin/block.json', 'version' => '1.2.3' );
        $this->assertSame( $external, acf_blocks_version_block_assets( $external ) );
    }
}
