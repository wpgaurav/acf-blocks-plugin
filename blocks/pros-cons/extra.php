<?php
/**
 * Pros & Cons Block — Extra functionality.
 *
 * Pure helpers for the pros & cons template.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Light colors that versions before 2.12.1 used as the color-field defaults.
 *
 * A block that carries one of these never chose it: the field shipped with it.
 * Treating them as "not set" lets those blocks follow the theme into dark mode.
 *
 * @return string[] Lowercase hex values.
 */
function acf_pros_cons_legacy_colors() {
    return array( '#fef2f2', '#dc2626', '#991b1b', '#f0fdf4', '#16a34a', '#166534' );
}

/**
 * Build the inline custom properties for the colors an editor actually chose.
 *
 * Empty fields and the old light defaults are skipped, so the stylesheet's
 * token fallbacks apply. Those fallbacks mix the theme's background, text,
 * success and danger colors, which switch with dark mode. A hard-coded light
 * background did not, and left light text on a near-white column.
 *
 * @param array $colors Map of custom property name => color value.
 * @return string Declarations for a style attribute, or '' when nothing is custom.
 */
function acf_pros_cons_style_vars( array $colors ) {
    $legacy = acf_pros_cons_legacy_colors();
    $vars   = '';

    foreach ( $colors as $property => $value ) {
        $value = strtolower( trim( (string) $value ) );

        if ( '' === $value || in_array( $value, $legacy, true ) ) {
            continue;
        }

        $vars .= $property . ':' . $value . ';';
    }

    return $vars;
}
