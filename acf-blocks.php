<?php
/**
 * Plugin Name: ACF Blocks
 * Plugin URI: https://github.com/wpgaurav/acf-blocks-plugin
 * Description: A collection of ACF Pro blocks for the WordPress block editor with automatic field group registration.
 * Version: 1.4.7
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Gaurav Tiwari
 * Author URI: https://gauravtiwari.org
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: acf-blocks
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'ACF_BLOCKS_VERSION', '1.4.7' );
define( 'ACF_BLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_BLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_BLOCKS_PLUGIN_FILE', __FILE__ );

/**
 * Check if ACF Pro is active and meets minimum requirements.
 */
function acf_blocks_check_requirements() {
    if ( ! class_exists( 'ACF' ) && ! function_exists( 'acf' ) ) {
        add_action( 'admin_notices', 'acf_blocks_missing_acf_notice' );
        return false;
    }
    return true;
}

/**
 * Admin notice for missing ACF Pro.
 */
function acf_blocks_missing_acf_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'ACF Blocks requires Advanced Custom Fields Pro to be installed and activated.', 'acf-blocks' ); ?></p>
    </div>
    <?php
}

/**
 * Initialize the plugin.
 */
function acf_blocks_init() {
    if ( ! acf_blocks_check_requirements() ) {
        return;
    }

    // Load the main functions file
    require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/functions.php';

    // Load text domain
    load_plugin_textdomain( 'acf-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'acf_blocks_init' );

/**
 * Activation hook.
 */
function acf_blocks_activate() {
    // Flush rewrite rules on activation
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'acf_blocks_activate' );

/**
 * Deactivation hook.
 */
function acf_blocks_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'acf_blocks_deactivate' );
