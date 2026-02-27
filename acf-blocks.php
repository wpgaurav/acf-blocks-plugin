<?php
/**
 * Plugin Name: ACF Blocks
 * Plugin URI: https://github.com/wpgaurav/acf-blocks-plugin
 * Description: A collection of ACF Pro blocks for the WordPress block editor with automatic field group registration.
 * Version: 2.2.7
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
define( 'ACF_BLOCKS_VERSION', '2.2.7' );
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
 * Admin notice for missing ACF Pro / SCF.
 */
function acf_blocks_missing_acf_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'ACF Blocks requires Advanced Custom Fields Pro or Secure Custom Fields (SCF) to be installed and activated.', 'acf-blocks' ); ?></p>
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
    require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/compat.php';
    require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/image-localizer.php';

    // Load text domain
    load_plugin_textdomain( 'acf-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'acf_blocks_init' );

// License Manager (loads independently of ACF requirement)
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/license-manager.php';
$acf_blocks_license = new ACF_Blocks_License_Manager( __FILE__ );
$acf_blocks_license->hook();

/**
 * Check if ACF Pro or SCF is installed (not necessarily active).
 *
 * @return bool
 */
function acf_blocks_has_acf_or_scf_installed() {
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    foreach ( $plugins as $plugin_file => $plugin_data ) {
        // ACF Pro
        if ( strpos( $plugin_file, 'advanced-custom-fields-pro/' ) === 0 ) {
            return true;
        }
        // SCF (Secure Custom Fields)
        if ( strpos( $plugin_file, 'secure-custom-fields/' ) === 0 ) {
            return true;
        }
    }
    return false;
}

/**
 * Install and activate SCF (Secure Custom Fields) from WordPress.org.
 */
function acf_blocks_install_scf() {
    if ( acf_blocks_has_acf_or_scf_installed() ) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';

    $api = plugins_api( 'plugin_information', array(
        'slug'   => 'secure-custom-fields',
        'fields' => array( 'sections' => false ),
    ) );

    if ( is_wp_error( $api ) ) {
        return;
    }

    $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
    $result   = $upgrader->install( $api->download_link );

    if ( ! is_wp_error( $result ) && $result ) {
        activate_plugin( 'secure-custom-fields/secure-custom-fields.php' );
    }
}

/**
 * Activation hook.
 */
function acf_blocks_activate() {
    acf_blocks_install_scf();
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
