<?php
/**
 * Store and output block-specific custom CSS in the footer
 */
if ( ! function_exists( 'md_store_block_css' ) ) {
function md_store_block_css($css, $block_id) {
    global $md_stored_block_css;

    if ( ! is_array( $md_stored_block_css ) ) {
        $md_stored_block_css = array();
    }

    if (!empty($css)) {
        // Clean the CSS and add a unique comment
        $css = "/* Custom CSS for block {$block_id} */\n" . $css;
        $md_stored_block_css[$block_id] = $css;
    }

    // Make sure we've set up the footer hook only once
    static $hook_set = false;
    if (!$hook_set) {
        add_action('wp_footer', 'md_output_stored_block_css', 100);
        $hook_set = true;
    }
}
}

/**
 * Output all stored CSS in the footer
 */
if ( ! function_exists( 'md_output_stored_block_css' ) ) {
function md_output_stored_block_css() {
    global $md_stored_block_css;

    if (!empty($md_stored_block_css) && is_array($md_stored_block_css)) {
        echo "\n<style id=\"md-blocks-custom-css\">\n";
        foreach ($md_stored_block_css as $css) {
            echo $css . "\n";
        }
        echo "</style>\n";
    }
}
}
