<?php
/**
 * ACF Blocks Compatibility Layer
 *
 * Fixes repeater field data resolution in ACF 6.7+ block rendering.
 *
 * Problem: ACF stores block data in a flat format (repeater_0_subfield => value)
 * but acf_setup_meta() in ACF 6.7+ doesn't reconstruct repeater arrays from
 * this flat format when field keys don't use the standard "field_" prefix.
 * This causes get_field() to return false for all repeater fields in blocks.
 *
 * Solution: Hook into acf/pre_load_value to intercept repeater field loading
 * during block rendering, and parse the structured data directly from the
 * raw block attributes stored in $block['data'].
 *
 * @package ACF_Blocks
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Global store for raw block data before ACF processes it.
 *
 * @var array<string, array>
 */
global $acf_blocks_raw_data;
$acf_blocks_raw_data = array();

/**
 * Capture raw block data before ACF's acf_setup_meta() processes it.
 *
 * @param array $block The block settings and attributes.
 * @return array Unmodified block array.
 */
function acf_blocks_capture_block_data( $block ) {
    global $acf_blocks_raw_data;

    $id = $block['id'] ?? '';
    if ( $id && ! empty( $block['data'] ) ) {
        $acf_blocks_raw_data[ $id ] = $block['data'];
    }

    return $block;
}
add_filter( 'acf/pre_render_block', 'acf_blocks_capture_block_data', 5 );

/**
 * Fix get_field() for repeater fields in ACF block context.
 *
 * Intercepts value loading for repeater fields when rendering blocks
 * and reconstructs the structured array from flat block data.
 *
 * @param mixed      $value   The pre-loaded value (null by default).
 * @param int|string $post_id The post ID or block ID string.
 * @param array      $field   The ACF field array.
 * @return mixed Reconstructed repeater array or original value.
 */
function acf_blocks_fix_repeater_loading( $value, $post_id, $field ) {
    // Only fix repeater fields in block context.
    if ( $field['type'] !== 'repeater' ) {
        return $value;
    }

    if ( ! is_string( $post_id ) || strpos( $post_id, 'block_' ) !== 0 ) {
        return $value;
    }

    global $acf_blocks_raw_data;
    $data = isset( $acf_blocks_raw_data[ $post_id ] ) ? $acf_blocks_raw_data[ $post_id ] : null;

    // Fall back to acf_get_meta if raw data wasn't captured.
    if ( empty( $data ) ) {
        $data = acf_get_meta( $post_id );
    }

    if ( empty( $data ) || ! is_array( $data ) ) {
        return $value;
    }

    $sub_fields = $field['sub_fields'] ?? array();
    if ( empty( $sub_fields ) ) {
        return $value;
    }

    $result = acf_blocks_parse_repeater_from_flat( $data, $field['name'], $sub_fields );

    return ( $result !== false ) ? $result : $value;
}
add_filter( 'acf/pre_load_value', 'acf_blocks_fix_repeater_loading', 10, 3 );

/**
 * Parse flat ACF block data into a structured repeater array.
 *
 * Converts flat format:
 *   repeater_0_subfield => "value"
 *   repeater_1_subfield => "value"
 *   repeater            => 2
 *
 * Into structured array:
 *   [ ['subfield' => 'value'], ['subfield' => 'value'] ]
 *
 * Handles nested repeaters, image fields, link fields, and true/false fields.
 *
 * @param array  $data       The flat block data array.
 * @param string $field_name The repeater field name.
 * @param array  $sub_fields Array of ACF sub-field definitions.
 * @return array|false Structured repeater data or false if no rows found.
 */
function acf_blocks_parse_repeater_from_flat( $data, $field_name, $sub_fields ) {
    // Determine row count from the stored count value or by scanning keys.
    $count = isset( $data[ $field_name ] ) ? intval( $data[ $field_name ] ) : 0;

    if ( $count < 1 ) {
        $count = acf_blocks_count_repeater_rows( $data, $field_name, $sub_fields );
    }

    if ( $count < 1 ) {
        return false;
    }

    $rows = array();

    for ( $i = 0; $i < $count; $i++ ) {
        $row = array();

        foreach ( $sub_fields as $sf ) {
            $key   = $field_name . '_' . $i . '_' . $sf['name'];
            $value = isset( $data[ $key ] ) ? $data[ $key ] : null;
            $value = acf_blocks_format_sub_field_value( $value, $sf, $data, $key );

            $row[ $sf['name'] ] = $value;
        }

        $rows[] = $row;
    }

    return $rows;
}

/**
 * Count repeater rows by scanning data keys for sub-field entries.
 *
 * @param array  $data       The flat block data.
 * @param string $field_name The repeater field name.
 * @param array  $sub_fields The sub-field definitions.
 * @return int Number of rows found.
 */
function acf_blocks_count_repeater_rows( $data, $field_name, $sub_fields ) {
    $count = 0;

    while ( true ) {
        $found = false;

        foreach ( $sub_fields as $sf ) {
            if ( isset( $data[ $field_name . '_' . $count . '_' . $sf['name'] ] ) ) {
                $found = true;
                break;
            }
        }

        if ( ! $found ) {
            break;
        }

        $count++;
    }

    return $count;
}

/**
 * Format a sub-field value based on its ACF field type.
 *
 * @param mixed  $value      The raw value.
 * @param array  $sf         The sub-field definition.
 * @param array  $data       The full flat data array (for nested repeaters).
 * @param string $parent_key The parent key prefix (for nested repeaters).
 * @return mixed The formatted value.
 */
function acf_blocks_format_sub_field_value( $value, $sf, $data, $parent_key ) {
    switch ( $sf['type'] ) {
        case 'image':
            if ( $value && is_numeric( $value ) ) {
                $image = wp_get_attachment_image_src( intval( $value ), 'full' );
                if ( $image ) {
                    $value = array(
                        'ID'  => intval( $value ),
                        'url' => $image[0],
                        'alt' => get_post_meta( intval( $value ), '_wp_attachment_image_alt', true ),
                    );
                }
            }
            break;

        case 'link':
            if ( $value && is_string( $value ) ) {
                $unserialized = @unserialize( $value );
                if ( false !== $unserialized ) {
                    $value = $unserialized;
                }
            }
            // Ensure link arrays have required keys.
            if ( is_array( $value ) ) {
                $value = wp_parse_args( $value, array(
                    'title'  => '',
                    'url'    => '',
                    'target' => '',
                ) );
            }
            break;

        case 'true_false':
            $value = (bool) $value;
            break;

        case 'repeater':
            // Handle nested repeaters.
            $nested_subs = $sf['sub_fields'] ?? array();
            if ( ! empty( $nested_subs ) ) {
                $nested_field = $parent_key; // The full flat key prefix for this nested repeater.
                // For nested repeaters, the parent key IS the prefix (e.g., repeater_0_nested).
                $value = acf_blocks_parse_repeater_from_flat( $data, $parent_key, $nested_subs );
                if ( false === $value ) {
                    $value = array();
                }
            }
            break;
    }

    return $value;
}
