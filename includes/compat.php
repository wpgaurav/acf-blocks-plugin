<?php
/**
 * ACF Blocks Compatibility Layer for ACF 6.7+
 *
 * ACF 6.7+ changed how acf_setup_meta() processes flat block data,
 * causing get_field() to return false/empty for fields in block render
 * templates. This provides drop-in helper functions that read directly
 * from $block['data'] when get_field() fails.
 *
 * @package ACF_Blocks
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get a field value from an ACF block, with $block['data'] fallback.
 *
 * @param string $name  The field name.
 * @param array  $block The block array passed to render templates.
 * @return mixed The field value or null.
 */
function acf_blocks_get_field( $name, $block = array() ) {
    // Try native get_field first (works in older ACF versions).
    $value = get_field( $name );
    if ( $value !== false && $value !== null && $value !== '' ) {
        return $value;
    }

    // Fallback: read directly from block data.
    $data = $block['data'] ?? array();
    // Try bare field name first, then field_ prefixed key.
    // Review CPTs store data with field_ prefix (e.g. field_pc_pros_list)
    // while post CPTs use bare names (e.g. pc_pros_list).
    return $data[ $name ] ?? $data[ 'field_' . $name ] ?? null;
}

/**
 * Get a repeater field as a structured array from an ACF block.
 *
 * @param string $name       The repeater field name.
 * @param array  $sub_names  Array of sub-field names (strings) or associative
 *                           array of name => type for special handling.
 *                           Supported types: 'text' (default), 'image', 'link',
 *                           'bool', 'repeater' (with nested sub-field names).
 * @param array  $block      The block array passed to render templates.
 * @return array Array of rows, each row is an associative array.
 */
function acf_blocks_get_repeater( $name, $sub_names, $block = array() ) {
    // Try native get_field first.
    $value = get_field( $name );
    if ( ! empty( $value ) && is_array( $value ) ) {
        return $value;
    }

    // Fallback: parse from flat block data.
    $data = $block['data'] ?? array();
    if ( empty( $data ) ) {
        return array();
    }

    // Normalize sub_names to associative array with types.
    $fields = array();
    foreach ( $sub_names as $key => $val ) {
        if ( is_int( $key ) ) {
            // Simple string: field name with default 'text' type.
            $fields[ $val ] = 'text';
        } else {
            // Associative: name => type.
            $fields[ $key ] = $val;
        }
    }

    // Determine row count.
    $count = isset( $data[ $name ] ) && ! is_array( $data[ $name ] ) ? intval( $data[ $name ] ) : 0;
    if ( $count < 1 ) {
        // Try counting by scanning keys.
        $count = 0;
        $field_names_list = array_keys( $fields );
        while ( isset( $data[ $name . '_' . $count . '_' . $field_names_list[0] ] ) ) {
            $count++;
        }
    }

    if ( $count < 1 ) {
        // Fallback: parse nested row format (row-0, row-1, etc.)
        // ACF 6.7+ may store repeater data as nested arrays in block comments.
        // Try bare name first, then field_ prefix (review CPTs use field_ prefix).
        $repeater_data = null;
        if ( isset( $data[ $name ] ) && is_array( $data[ $name ] ) ) {
            $repeater_data = $data[ $name ];
        } elseif ( isset( $data[ 'field_' . $name ] ) && is_array( $data[ 'field_' . $name ] ) ) {
            $repeater_data = $data[ 'field_' . $name ];
        }
        if ( is_array( $repeater_data ) ) {
            $rows = array();
            foreach ( $repeater_data as $row_key => $row_data ) {
                if ( ! is_array( $row_data ) ) {
                    continue;
                }
                $row = array();
                foreach ( $fields as $sub_name => $type ) {
                    // Try bare sub-field name, then field_ prefixed.
                    $value = $row_data[ $sub_name ] ?? $row_data[ 'field_' . $sub_name ] ?? null;

                    switch ( $type ) {
                        case 'image':
                            if ( $value && is_numeric( $value ) ) {
                                $img = wp_get_attachment_image_src( intval( $value ), 'full' );
                                if ( $img ) {
                                    $value = array(
                                        'ID'  => intval( $value ),
                                        'url' => $img[0],
                                        'alt' => get_post_meta( intval( $value ), '_wp_attachment_image_alt', true ),
                                    );
                                }
                            }
                            break;

                        case 'image_url':
                            if ( $value && is_numeric( $value ) ) {
                                $img   = wp_get_attachment_image_src( intval( $value ), 'full' );
                                $value = $img ? $img[0] : '';
                            }
                            break;

                        case 'link':
                            if ( $value && is_string( $value ) ) {
                                $unserialized = @unserialize( $value );
                                if ( false !== $unserialized ) {
                                    $value = $unserialized;
                                }
                            }
                            if ( is_array( $value ) ) {
                                $value = wp_parse_args( $value, array(
                                    'title'  => '',
                                    'url'    => '',
                                    'target' => '',
                                ) );
                            }
                            break;

                        case 'bool':
                            $value = (bool) $value;
                            break;

                        case 'int':
                        case 'number':
                            $value = $value !== null ? intval( $value ) : 0;
                            break;
                    }

                    $row[ $sub_name ] = $value;
                }
                $rows[] = $row;
            }
            return $rows;
        }

        return array();
    }

    $rows = array();
    for ( $i = 0; $i < $count; $i++ ) {
        $row = array();
        foreach ( $fields as $sub_name => $type ) {
            $key   = $name . '_' . $i . '_' . $sub_name;
            $value = $data[ $key ] ?? null;

            switch ( $type ) {
                case 'image':
                    if ( $value && is_numeric( $value ) ) {
                        $img = wp_get_attachment_image_src( intval( $value ), 'full' );
                        if ( $img ) {
                            $value = array(
                                'ID'  => intval( $value ),
                                'url' => $img[0],
                                'alt' => get_post_meta( intval( $value ), '_wp_attachment_image_alt', true ),
                            );
                        }
                    }
                    break;

                case 'image_url':
                    // Image field with return_format = url.
                    if ( $value && is_numeric( $value ) ) {
                        $img = wp_get_attachment_image_src( intval( $value ), 'full' );
                        $value = $img ? $img[0] : '';
                    }
                    break;

                case 'link':
                    if ( $value && is_string( $value ) ) {
                        $unserialized = @unserialize( $value );
                        if ( false !== $unserialized ) {
                            $value = $unserialized;
                        }
                    }
                    if ( is_array( $value ) ) {
                        $value = wp_parse_args( $value, array(
                            'title'  => '',
                            'url'    => '',
                            'target' => '',
                        ) );
                    }
                    break;

                case 'bool':
                    $value = (bool) $value;
                    break;

                case 'int':
                case 'number':
                    $value = $value !== null ? intval( $value ) : 0;
                    break;
            }

            $row[ $sub_name ] = $value;
        }
        $rows[] = $row;
    }

    return $rows;
}

/**
 * Get a nested repeater from flat block data.
 *
 * Used when a repeater contains another repeater as a sub-field.
 *
 * @param string $parent_key  Full parent key prefix (e.g., "repeater_0_nested").
 * @param array  $sub_names   Sub-field names/types for the nested repeater.
 * @param array  $data        The flat block data array.
 * @return array Array of nested rows.
 */
function acf_blocks_get_nested_repeater( $parent_key, $sub_names, $data ) {
    // Normalize sub_names.
    $fields = array();
    foreach ( $sub_names as $key => $val ) {
        if ( is_int( $key ) ) {
            $fields[ $val ] = 'text';
        } else {
            $fields[ $key ] = $val;
        }
    }

    $field_names_list = array_keys( $fields );
    $count = isset( $data[ $parent_key ] ) && ! is_array( $data[ $parent_key ] ) ? intval( $data[ $parent_key ] ) : 0;
    if ( $count < 1 ) {
        $count = 0;
        while ( isset( $data[ $parent_key . '_' . $count . '_' . $field_names_list[0] ] ) ) {
            $count++;
        }
    }

    if ( $count < 1 ) {
        // Fallback: parse nested row format.
        $nested = isset( $data[ $parent_key ] ) && is_array( $data[ $parent_key ] )
            ? $data[ $parent_key ]
            : null;

        // If flat key doesn't exist, resolve through nested data structure.
        // E.g. "repeater_0_sub_repeater" navigates to
        // $data['repeater']['row-0']['sub_repeater'].
        if ( null === $nested ) {
            $nested = acf_blocks_resolve_nested_key( $parent_key, $data );
        }

        if ( is_array( $nested ) ) {
            $rows = array();
            foreach ( $nested as $row_key => $row_data ) {
                if ( ! is_array( $row_data ) ) {
                    continue;
                }
                $row = array();
                foreach ( $fields as $sub_name => $type ) {
                    $row[ $sub_name ] = $row_data[ $sub_name ] ?? null;
                }
                $rows[] = $row;
            }
            return $rows;
        }

        return array();
    }

    $rows = array();
    for ( $i = 0; $i < $count; $i++ ) {
        $row = array();
        foreach ( $fields as $sub_name => $type ) {
            $row[ $sub_name ] = $data[ $parent_key . '_' . $i . '_' . $sub_name ] ?? null;
        }
        $rows[] = $row;
    }

    return $rows;
}

/**
 * Reconstruct flat repeater keys into nested row-N format for the block editor.
 *
 * When content is saved via the REST API, ACF stores nested repeater data as
 * flat indexed keys (e.g. repeater_0_field, repeater_0_nested_0_subfield).
 * The PHP compat helpers parse this fine for frontend rendering, but ACF's
 * JavaScript in the block editor expects nested row-N objects. This filter
 * detects flat repeater keys in block data and rebuilds the nested structure
 * so the editor fields populate correctly.
 *
 * Works generically for any ACF block — scans all registered field groups
 * for repeater fields rather than hard-coding block names.
 *
 * @since 2.1.4
 */
add_filter( 'render_block_data', 'acf_blocks_reconstruct_nested_repeaters' );

/**
 * Filter callback: convert flat repeater keys to nested row-N format.
 *
 * @param array $parsed_block The parsed block data.
 * @return array Modified block data with reconstructed nested repeaters.
 */
function acf_blocks_reconstruct_nested_repeaters( $parsed_block ) {
    if ( empty( $parsed_block['blockName'] ) || strpos( $parsed_block['blockName'], 'acf/' ) !== 0 ) {
        return $parsed_block;
    }

    if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return $parsed_block;
    }

    $data = $parsed_block['attrs']['data'] ?? array();
    if ( empty( $data ) || ! is_array( $data ) ) {
        return $parsed_block;
    }

    $repeaters = acf_blocks_find_repeater_fields( $parsed_block['blockName'] );
    if ( empty( $repeaters ) ) {
        return $parsed_block;
    }

    $changed = false;

    foreach ( $repeaters as $repeater ) {
        $field_name = $repeater['name'];

        if ( ! isset( $data[ $field_name ] ) || is_array( $data[ $field_name ] ) ) {
            continue;
        }

        $count = intval( $data[ $field_name ] );
        if ( $count < 1 ) {
            continue;
        }

        $sub_fields = $repeater['sub_fields'] ?? array();
        if ( empty( $sub_fields ) ) {
            continue;
        }

        $nested = acf_blocks_rebuild_repeater_rows( $field_name, $count, $sub_fields, $data );
        if ( ! empty( $nested ) ) {
            $data[ $field_name ] = $nested;
            $changed = true;
        }
    }

    if ( $changed ) {
        $parsed_block['attrs']['data'] = $data;
    }

    return $parsed_block;
}

/**
 * Rebuild flat repeater rows into nested row-N format.
 *
 * @param string $prefix     The repeater field name prefix.
 * @param int    $count      Number of rows.
 * @param array  $sub_fields Sub-field definitions from the field group.
 * @param array  $data       The flat block data.
 * @return array Nested row-N structure.
 */
function acf_blocks_rebuild_repeater_rows( $prefix, $count, $sub_fields, $data ) {
    $nested = array();

    for ( $i = 0; $i < $count; $i++ ) {
        $row = array();

        foreach ( $sub_fields as $sub_field ) {
            $sub_name = $sub_field['name'];
            $flat_key = $prefix . '_' . $i . '_' . $sub_name;

            if ( $sub_field['type'] === 'repeater' && isset( $sub_field['sub_fields'] ) ) {
                $sub_count_key = $flat_key;
                $sub_count = isset( $data[ $sub_count_key ] ) && ! is_array( $data[ $sub_count_key ] )
                    ? intval( $data[ $sub_count_key ] )
                    : 0;

                if ( $sub_count < 1 ) {
                    $sub_count = 0;
                    $first_sub = $sub_field['sub_fields'][0]['name'] ?? '';
                    if ( $first_sub ) {
                        while ( isset( $data[ $flat_key . '_' . $sub_count . '_' . $first_sub ] ) ) {
                            $sub_count++;
                        }
                    }
                }

                if ( $sub_count > 0 ) {
                    $row[ $sub_name ] = acf_blocks_rebuild_repeater_rows(
                        $flat_key,
                        $sub_count,
                        $sub_field['sub_fields'],
                        $data
                    );
                } else {
                    $row[ $sub_name ] = array();
                }
            } else {
                $row[ $sub_name ] = $data[ $flat_key ] ?? '';
            }
        }

        $nested[ 'row-' . $i ] = $row;
    }

    return $nested;
}

/**
 * Find repeater fields (with sub_fields) for a given ACF block name.
 *
 * Scans registered ACF field groups and returns repeater definitions
 * that contain nested repeaters. Results are cached per request.
 *
 * @param string $block_name The block name (e.g. "acf/compare").
 * @return array Array of repeater field definitions with nested repeaters.
 */
function acf_blocks_find_repeater_fields( $block_name ) {
    static $cache = array();

    if ( isset( $cache[ $block_name ] ) ) {
        return $cache[ $block_name ];
    }

    $cache[ $block_name ] = array();

    if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
        return acf_blocks_find_repeater_fields_from_json( $block_name );
    }

    $groups = acf_get_field_groups( array(
        'block' => $block_name,
    ) );

    foreach ( $groups as $group ) {
        $fields = acf_get_fields( $group['key'] );
        if ( ! is_array( $fields ) ) {
            continue;
        }
        foreach ( $fields as $field ) {
            if ( $field['type'] === 'repeater' && acf_blocks_has_nested_repeater( $field ) ) {
                $cache[ $block_name ][] = $field;
            }
        }
    }

    return $cache[ $block_name ];
}

/**
 * Fallback: find repeater fields from block-data.json files.
 *
 * @param string $block_name The block name (e.g. "acf/compare").
 * @return array Array of repeater field definitions.
 */
function acf_blocks_find_repeater_fields_from_json( $block_name ) {
    $slug = str_replace( 'acf/', '', $block_name );
    $blocks_dir = ACF_BLOCKS_PLUGIN_DIR . 'blocks/';

    $dirs = glob( $blocks_dir . '*', GLOB_ONLYDIR );
    foreach ( $dirs as $dir ) {
        $block_json = $dir . '/block.json';
        if ( ! file_exists( $block_json ) ) {
            continue;
        }
        $meta = json_decode( file_get_contents( $block_json ), true );
        $name = $meta['name'] ?? '';
        if ( $name !== $block_name ) {
            continue;
        }

        $data_file = $dir . '/block-data.json';
        if ( ! file_exists( $data_file ) ) {
            return array();
        }
        $group = json_decode( file_get_contents( $data_file ), true );
        $fields = $group['fields'] ?? array();
        $repeaters = array();
        foreach ( $fields as $field ) {
            if ( ( $field['type'] ?? '' ) === 'repeater' && acf_blocks_json_has_nested_repeater( $field ) ) {
                $repeaters[] = $field;
            }
        }
        return $repeaters;
    }

    return array();
}

/**
 * Check if an ACF field definition (from acf_get_fields) has a nested repeater.
 *
 * @param array $field ACF field array.
 * @return bool
 */
function acf_blocks_has_nested_repeater( $field ) {
    if ( empty( $field['sub_fields'] ) || ! is_array( $field['sub_fields'] ) ) {
        return false;
    }
    foreach ( $field['sub_fields'] as $sub ) {
        if ( $sub['type'] === 'repeater' ) {
            return true;
        }
    }
    return false;
}

/**
 * Check if a JSON field definition has a nested repeater.
 *
 * @param array $field Field definition from block-data.json.
 * @return bool
 */
function acf_blocks_json_has_nested_repeater( $field ) {
    $sub_fields = $field['sub_fields'] ?? array();
    foreach ( $sub_fields as $sub ) {
        if ( ( $sub['type'] ?? '' ) === 'repeater' ) {
            return true;
        }
    }
    return false;
}

/**
 * Resolve a flat ACF key through nested block data.
 *
 * Flat keys like "repeater_0_sub_repeater" are navigated through
 * nested data where numeric indices map to "row-N" keys.
 *
 * @param string $flat_key The flat key to resolve.
 * @param array  $data     The nested data array.
 * @return mixed The resolved value or null.
 */
function acf_blocks_resolve_nested_key( $flat_key, $data ) {
    $parts = explode( '_', $flat_key );
    return acf_blocks_resolve_nested_parts( $parts, 0, $data );
}

/**
 * Recursively navigate nested data matching flat key parts.
 *
 * Tries progressively longer key segments (longest first) at each level
 * to handle field names containing underscores.
 *
 * @param array $parts Key segments split by underscore.
 * @param int   $start Starting index in parts array.
 * @param mixed $data  Current data node.
 * @return mixed Resolved value or null.
 */
function acf_blocks_resolve_nested_parts( $parts, $start, $data ) {
    if ( $start >= count( $parts ) ) {
        return $data;
    }

    if ( ! is_array( $data ) ) {
        return null;
    }

    $len = count( $parts );

    // Try key segments from longest to shortest for best match.
    for ( $end = $len; $end > $start; $end-- ) {
        $segment = implode( '_', array_slice( $parts, $start, $end - $start ) );

        if ( array_key_exists( $segment, $data ) ) {
            $result = acf_blocks_resolve_nested_parts( $parts, $end, $data[ $segment ] );
            if ( null !== $result ) {
                return $result;
            }
        }

        // Numeric segment maps to "row-N" in nested format.
        if ( $end === $start + 1 && is_numeric( $segment ) ) {
            $row_key = 'row-' . $segment;
            if ( array_key_exists( $row_key, $data ) ) {
                $result = acf_blocks_resolve_nested_parts( $parts, $end, $data[ $row_key ] );
                if ( null !== $result ) {
                    return $result;
                }
            }
        }
    }

    return null;
}
