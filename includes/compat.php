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
    return $data[ $name ] ?? null;
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
        if ( isset( $data[ $name ] ) && is_array( $data[ $name ] ) ) {
            $rows = array();
            foreach ( $data[ $name ] as $row_key => $row_data ) {
                if ( ! is_array( $row_data ) ) {
                    continue;
                }
                $row = array();
                foreach ( $fields as $sub_name => $type ) {
                    $value = $row_data[ $sub_name ] ?? null;

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
