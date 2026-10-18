<?php
/** Disposable WordPress runtime; all mutable paths and database live in a test sandbox. */
$root = getenv( 'ACFB_TEST_WP_ROOT' );
$sandbox = getenv( 'ACFB_TEST_SANDBOX' );
$socket = getenv( 'ACFB_TEST_MYSQL_SOCKET' );
if ( ! $root || ! $sandbox || ! $socket || ( 0 !== strpos( $sandbox, sys_get_temp_dir() . '/' ) && 0 !== strpos( $sandbox, '/tmp/' ) ) ) {
    throw new RuntimeException( 'Set ACFB_TEST_WP_ROOT, ACFB_TEST_SANDBOX and ACFB_TEST_MYSQL_SOCKET.' );
}
define( 'ABSPATH', rtrim( $root, '/' ) . '/' );
define( 'WP_INSTALLING', true );
define( 'DISABLE_WP_CRON', true );
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'WP_DEBUG', false );
define( 'DB_NAME', getenv( 'ACFB_TEST_DB_NAME' ) ?: 'acfb_storage_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost:' . $socket );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
// WordPress renders connection failures with wp_die(), which can exit 0 in a
// plain PHP process. Fail explicitly before boot so a missing DB cannot look
// like a passing integration run.
mysqli_report( MYSQLI_REPORT_OFF );
$probe = mysqli_init();
if ( ! @mysqli_real_connect( $probe, 'localhost', DB_USER, DB_PASSWORD, DB_NAME, 0, $socket ) ) {
    throw new RuntimeException( 'The isolated integration database is unavailable.' );
}
mysqli_close( $probe );
define( 'WP_CONTENT_DIR', $sandbox . '/content' );
define( 'WP_CONTENT_URL', 'http://acf-storage.invalid/wp-content' );
define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );
define( 'WP_TEMP_DIR', $sandbox . '/tmp' );
define( 'WP_HOME', 'http://acf-storage.invalid' );
define( 'WP_SITEURL', WP_HOME );
define( 'AUTH_KEY', 'disposable-storage-test-key' );
define( 'SECURE_AUTH_KEY', AUTH_KEY );
define( 'LOGGED_IN_KEY', AUTH_KEY );
define( 'NONCE_KEY', AUTH_KEY );
$table_prefix = getenv( 'ACFB_TEST_TABLE_PREFIX' ) ?: 'acfb_safety_';
$_SERVER['HTTP_HOST'] = 'acf-storage.invalid';
$_SERVER['REQUEST_METHOD'] = 'GET';
@mkdir( WP_PLUGIN_DIR, 0755, true );
@mkdir( WPMU_PLUGIN_DIR, 0755, true );
@mkdir( WP_TEMP_DIR, 0755, true );
require ABSPATH . 'wp-settings.php';
add_filter( 'pre_http_request', function () { return new WP_Error( 'offline_test', 'Network disabled by storage integration tests.' ); }, 999 );
add_filter( 'pre_wp_mail', '__return_false' );
if ( ! is_blog_installed() ) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    wp_install( 'Disposable storage tests', 'storage-test', 'storage@example.invalid', false, '', 'disposable-test-password' );
}
wp_set_current_user( 1 );
$plugin_root = getenv( 'ACFB_TEST_PLUGIN_ROOT' ) ?: dirname( __DIR__, 2 );
// Match PHP's resolved __FILE__ in the real plugin entry point, including macOS
// /var -> /private/var aliases when smoke-testing an extracted upload ZIP.
define( 'ACF_BLOCKS_PLUGIN_DIR', ( realpath( $plugin_root ) ?: $plugin_root ) . '/' );
define( 'ACF_BLOCKS_PLUGIN_URL', WP_CONTENT_URL . '/plugins/acf-blocks/' );
define( 'ACF_BLOCKS_VERSION', 'test' );
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/functions.php';
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/block-migrator.php';
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/block-recovery.php';
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/image-localizer.php';
