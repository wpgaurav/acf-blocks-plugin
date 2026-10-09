<?php
use PHPUnit\Framework\TestCase;

final class EditorBundleTest extends TestCase {
    private $directory;

    protected function setUp(): void {
        $this->directory = sys_get_temp_dir() . '/acf-editor-test-' . bin2hex( random_bytes( 8 ) );
        mkdir( $this->directory );
        $GLOBALS['acf_blocks_test_uploads'] = $this->directory;
        unset( $GLOBALS['acf_blocks_test_options']['acf_blocks_editor_bundle'] );
    }

    protected function tearDown(): void {
        foreach ( (array) glob( $this->directory . '/acf-blocks-plugin/*' ) as $file ) unlink( $file );
        if ( is_dir( $this->directory . '/acf-blocks-plugin' ) ) rmdir( $this->directory . '/acf-blocks-plugin' );
        rmdir( $this->directory );
        unset( $GLOBALS['acf_blocks_test_uploads'], $GLOBALS['acf_blocks_test_options']['acf_blocks_editor_bundle'] );
    }

    public function test_different_builds_retain_complete_recent_files(): void {
        $this->assertTrue( acf_blocks_build_site_editor_bundle( array() ) );
        $first = get_option( 'acf_blocks_editor_bundle' );
        $this->assertTrue( acf_blocks_build_site_editor_bundle( array( 'acf/hero' ) ) );
        $second = get_option( 'acf_blocks_editor_bundle' );
        $this->assertNotSame( $first['path'], $second['path'] );
        $this->assertFileExists( $first['path'] );
        $this->assertFileExists( $second['path'] );
        $this->assertGreaterThan( 0, filesize( $second['path'] ) );
        $this->assertSame( 0644, fileperms( $second['path'] ) & 0777 );
        $this->assertSame( acf_blocks_editor_settings_hash( array( 'acf/hero' ) ), $second['settings_hash'] );
    }

    public function test_identical_bundle_is_not_rewritten_and_cleanup_only_prunes_owned_old_files(): void {
        acf_blocks_build_site_editor_bundle( array() );
        $bundle = get_option( 'acf_blocks_editor_bundle' );
        $mtime = time() - 60;
        touch( $bundle['path'], $mtime );
        $other = dirname( $bundle['path'] ) . '/editor-blocks-custom.css';
        $owned = dirname( $bundle['path'] ) . '/editor-blocks-0123456789abcdef.css';
        foreach ( array( $other, $owned ) as $file ) {
            file_put_contents( $file, 'old' );
            touch( $file, time() - 172800 );
        }
        $this->assertTrue( acf_blocks_build_site_editor_bundle( array() ) );
        clearstatcache();
        $this->assertSame( $mtime, filemtime( $bundle['path'] ) );
        $this->assertFileExists( $other );
        $this->assertFileDoesNotExist( $owned );
    }
}
