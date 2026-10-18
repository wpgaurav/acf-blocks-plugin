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
 * Find a field's exact registered schema, including its real ACF key.
 *
 * Sub-field keys do not always equal "field_" plus their name. Read the same
 * metadata used for registration instead of guessing additional prefixes.
 *
 * @param string $name Field name.
 * @param array  $block Render block attributes.
 * @return array
 */
function acf_blocks_get_field_schema( $name, $block = array() ) {
    static $schemas = null;
    if ( null === $schemas ) {
        $schemas = array();
        if ( function_exists( 'acf_blocks_get_block_metadata_cache' ) ) {
            $definitions = acf_blocks_get_block_metadata_cache( true );
        } else {
            // Keep the compatibility helpers usable independently of the loader.
            $manifest_file = __DIR__ . '/generated-block-manifest.php';
            $definitions = is_readable( $manifest_file ) ? require $manifest_file : array();
        }
        foreach ( $definitions as $definition ) {
            $fields = array();
            $field_groups = $definition['field_groups'] ?? null;
            if ( ! is_array( $field_groups ) && ! empty( $definition['folder'] ) ) {
                // The loader marks unavailable manifest data with null. Decode
                // source schemas once, including multiple groups in one file.
                $field_groups = array();
                foreach ( (array) glob( rtrim( $definition['folder'], '/\\' ) . '/*.json' ) as $json_file ) {
                    if ( 'block.json' === basename( $json_file ) ) {
                        continue;
                    }
                    $groups = json_decode( (string) file_get_contents( $json_file ), true );
                    if ( isset( $groups['key'], $groups['fields'] ) ) {
                        $groups = array( $groups );
                    }
                    foreach ( (array) $groups as $group ) {
                        if ( is_array( $group ) && isset( $group['key'], $group['fields'] ) ) {
                            $field_groups[] = $group;
                        }
                    }
                }
            }
            foreach ( (array) $field_groups as $group ) {
                foreach ( $group['fields'] ?? array() as $field ) {
                    if ( ! empty( $field['name'] ) ) {
                        $fields[ $field['name'] ] = $field;
                    }
                }
            }
            $schemas[ $definition['metadata']['name'] ] = $fields;
        }
    }
    $block_name = $block['name'] ?? $block['blockName'] ?? '';
    if ( isset( $schemas[ $block_name ][ $name ] ) ) {
        return $schemas[ $block_name ][ $name ];
    }
    // Older callers do not pass the block name. Top-level field names are a
    // reliable fallback; child schemas are always resolved within the parent.
    foreach ( $schemas as $fields ) {
        if ( isset( $fields[ $name ] ) ) {
            return $fields[ $name ];
        }
    }
    return array( 'name' => $name, 'type' => 'text' );
}

/**
 * Read structured or flattened ACF data recursively using a field schema.
 *
 * @param array $schema Field definition.
 * @param array $data Raw data at this level, or the full flat data array.
 * @param bool  $found Whether this field (including flat children) exists.
 * @param array $prefixes Flat parent prefixes; empty for nested row data.
 * @return mixed
 */
function acf_blocks_read_field_data( $schema, $data, &$found, $prefixes = array( '' ) ) {
    $found = false;
    $name = $schema['name'];
    $aliases = array_unique( array_filter( array( $name, $schema['key'] ?? '', 'field_' . $name ) ) );
    $keys = array();
    foreach ( $prefixes as $prefix ) {
        foreach ( $aliases as $alias ) {
            $keys[] = '' === $prefix ? $alias : $prefix . '_' . $alias;
        }
    }
    $value = null;
    foreach ( $keys as $key ) {
        if ( array_key_exists( $key, $data ) ) {
            $found = true;
            $value = $data[ $key ];
            break;
        }
    }
    $type = $schema['type'] ?? 'text';
    if ( 'group' !== $type && 'repeater' !== $type ) {
        return acf_blocks_cast_field_value( $value, $type );
    }
    $children = $schema['sub_fields'] ?? array();
    if ( 'group' === $type ) {
        $group = array();
        foreach ( $children as $child ) {
            $child_found = false;
            $group[ $child['name'] ] = is_array( $value )
                ? acf_blocks_read_field_data( $child, $value, $child_found )
                : acf_blocks_read_field_data( $child, $data, $child_found, $keys );
            $found = $found || $child_found;
        }
        return $group;
    }
    $rows = array();
    if ( is_array( $value ) ) {
        foreach ( $value as $row_data ) {
            if ( ! is_array( $row_data ) ) {
                continue;
            }
            $row = array();
            foreach ( $children as $child ) {
                $child_found = false;
                $row[ $child['name'] ] = acf_blocks_read_field_data( $child, $row_data, $child_found );
            }
            $rows[] = $row;
        }
        return $rows;
    }
    $indexes = array();
    if ( $found ) {
        // An explicit zero is authoritative even when stale indexed keys remain.
        // A valid flat row needs stored data. Bound corrupt counts by the
        // actual payload size before allocating indexes, without a site limit.
        $count = min( count( $data ), max( 0, (int) $value ) );
        $indexes = $count > 0 ? range( 0, $count - 1 ) : array();
    } else {
        foreach ( array_keys( $data ) as $data_key ) {
            foreach ( $keys as $key ) {
                if ( preg_match( '/^' . preg_quote( $key, '/' ) . '_([0-9]+)_/', (string) $data_key, $match ) ) {
                    $indexes[ (int) $match[1] ] = (int) $match[1];
                    $found = true;
                }
            }
        }
        sort( $indexes, SORT_NUMERIC );
    }
    foreach ( $indexes as $index ) {
        $row = array();
        $row_prefixes = array();
        foreach ( $keys as $key ) {
            $row_prefixes[] = $key . '_' . $index;
        }
        foreach ( $children as $child ) {
            $child_found = false;
            $row[ $child['name'] ] = acf_blocks_read_field_data( $child, $data, $child_found, $row_prefixes );
        }
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Combine requested field types with exact registered child keys and schemas.
 *
 * Nested overrides use array( 'type' => 'group'|'repeater', 'sub_fields' => ... ).
 *
 * @param array $schema Parent schema.
 * @param array $sub_names Requested field names/types.
 * @return array
 */
function acf_blocks_requested_field_schema( $schema, $sub_names ) {
    $registered = array();
    foreach ( $schema['sub_fields'] ?? array() as $field ) {
        $registered[ $field['name'] ] = $field;
    }
    $children = array();
    foreach ( $sub_names as $key => $type ) {
        $name = is_int( $key ) ? $type : $key;
        $type = is_int( $key ) ? 'text' : $type;
        $child = $registered[ $name ] ?? array( 'name' => $name, 'type' => 'text' );
        if ( is_array( $type ) ) {
            $child['type'] = $type['type'] ?? 'group';
            $child = acf_blocks_requested_field_schema( $child, $type['sub_fields'] ?? array() );
        } elseif ( 'text' !== $type || ! in_array( $child['type'], array( 'group', 'repeater' ), true ) ) {
            $child['type'] = $type;
        }
        $children[] = $child;
    }
    $schema['sub_fields'] = $children;
    return $schema;
}

/**
 * Get a field value from an ACF block, with schema-aware block data fallback.
 *
 * @param string $name Field name.
 * @param array  $block Render block attributes.
 * @return mixed
 */
function acf_blocks_get_field( $name, $block = array() ) {
    $schema = acf_blocks_get_field_schema( $name, $block );
    if ( ! in_array( $schema['type'], array( 'group', 'repeater' ), true ) ) {
        // Scalar get_field callers historically receive the raw stored value.
        $schema['type'] = 'text';
    }
    $found = false;
    $value = acf_blocks_read_field_data( $schema, (array) ( $block['data'] ?? array() ), $found );
    if ( ! $found ) {
        $native = get_field( $name );
        $value = false !== $native && null !== $native && '' !== $native ? $native : null;
        if ( null !== $value && in_array( $schema['type'], array( 'group', 'repeater' ), true ) ) {
            $value = acf_blocks_read_field_data( $schema, array( $name => $value ), $found );
        }
    }
    return apply_filters( 'acf_blocks_field_value', $value, $name, $block );
}

/**
 * Get repeater rows with normalized field names at every nested level.
 *
 * @param string $name Repeater name.
 * @param array  $sub_names Child names or name => type overrides.
 * @param array  $block Render block attributes.
 * @return array
 */
function acf_blocks_get_repeater( $name, $sub_names, $block = array() ) {
    $schema = acf_blocks_requested_field_schema( acf_blocks_get_field_schema( $name, $block ), $sub_names );
    $schema['type'] = 'repeater';
    $found = false;
    $rows = acf_blocks_read_field_data( $schema, (array) ( $block['data'] ?? array() ), $found );
    if ( ! $found ) {
        $native = get_field( $name );
        if ( is_array( $native ) ) {
            $rows = acf_blocks_read_field_data( $schema, array( $name => $native ), $found );
        }
    }
    return $rows;
}

/**
 * Get a group from nested or flat data, using registered sub-field keys.
 *
 * @param string $name Group name.
 * @param array  $sub_names Child names or name => type overrides.
 * @param array  $block Render block attributes.
 * @return array
 */
function acf_blocks_get_group( $name, $sub_names, $block = array() ) {
    $schema = acf_blocks_requested_field_schema( acf_blocks_get_field_schema( $name, $block ), $sub_names );
    $schema['type'] = 'group';
    $found = false;
    $group = acf_blocks_read_field_data( $schema, (array) ( $block['data'] ?? array() ), $found );
    if ( ! $found ) {
        $native = get_field( $name );
        if ( is_array( $native ) ) {
            $group = acf_blocks_read_field_data( $schema, array( $name => $native ), $found );
        }
    }
    return $group;
}

/**
 * Cast a field value to the appropriate type for repeater sub-fields.
 *
 * @param mixed  $value The raw value.
 * @param string $type  The field type: text, image, image_url, link, bool, int/number.
 * @return mixed The cast value.
 */
function acf_blocks_cast_field_value( $value, $type ) {
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
                $unserialized = maybe_unserialize( $value );
                if ( is_array( $unserialized ) ) {
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
            // ACF stores true/false as the strings "1" and "0". A plain
            // (bool) cast treats "0" as true.
            $value = acf_blocks_is_on( $value );
            break;

        case 'int':
            $value = $value !== null ? intval( $value ) : 0;
            break;

        case 'number':
            $value = is_numeric( $value ) ? (float) $value : 0.0;
            $value = is_finite( $value ) ? $value : 0.0;
            break;
    }

    return $value;
}

/**
 * Resolve an ACF image field value to a URL and alt text.
 *
 * The compat layer reads raw values from $block['data'], which stores image
 * fields as numeric attachment IDs. Native ACF returns an array with 'ID',
 * 'url', 'alt', 'sizes'. This helper handles both cases plus direct URLs.
 *
 * @param mixed  $image     ACF image value (array, numeric ID, or URL string).
 * @param string $fallback_alt Fallback alt text if none found.
 * @param string $size      WordPress image size to retrieve. Default 'medium'.
 * @return array{src: string, alt: string}
 */
function acf_blocks_resolve_image( $image, $fallback_alt = '', $size = 'medium' ) {
    $result = [ 'src' => '', 'alt' => $fallback_alt, 'srcset' => '', 'sizes' => '' ];

    if ( empty( $image ) ) {
        return $result;
    }

    // Case 1: ACF array format (native get_field with return_format=array)
    if ( is_array( $image ) && ! empty( $image['url'] ) ) {
        $result['src'] = $image['url'];
        if ( ! empty( $image['alt'] ) ) {
            $result['alt'] = $image['alt'];
        }
        $attachment_id = ! empty( $image['ID'] ) ? (int) $image['ID'] : 0;
        // Try requested size if available
        if ( ! empty( $image['sizes'][ $size ] ) ) {
            $result['src'] = $image['sizes'][ $size ];
        } elseif ( $attachment_id ) {
            $sized = wp_get_attachment_image_src( $attachment_id, $size );
            if ( $sized ) {
                $result['src'] = $sized[0];
            }
        }
        // Generate srcset/sizes from attachment ID
        if ( $attachment_id ) {
            $result = acf_blocks_attach_srcset( $result, $attachment_id, $size );
        }
        return $result;
    }

    // Case 2: Raw numeric attachment ID (compat layer reads from $block['data'])
    if ( is_numeric( $image ) ) {
        $attachment_id = (int) $image;
        $sized = wp_get_attachment_image_src( $attachment_id, $size );
        if ( $sized ) {
            $result['src'] = $sized[0];
        } else {
            $full = wp_get_attachment_url( $attachment_id );
            if ( $full ) {
                $result['src'] = $full;
            }
        }
        $img_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
        if ( $img_alt ) {
            $result['alt'] = $img_alt;
        }
        // Generate srcset/sizes from attachment ID
        $result = acf_blocks_attach_srcset( $result, $attachment_id, $size );
        return $result;
    }

    // Case 3: Direct URL string
    if ( is_string( $image ) && filter_var( $image, FILTER_VALIDATE_URL ) ) {
        $result['src'] = $image;
        // Try to find an attachment ID for same-domain URLs (e.g. localized images).
        $attachment_id = acf_blocks_url_to_attachment_id( $image );
        if ( $attachment_id ) {
            $sized = wp_get_attachment_image_src( $attachment_id, $size );
            if ( $sized ) {
                $result['src'] = $sized[0];
            }
            $img_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
            if ( $img_alt && empty( $fallback_alt ) ) {
                $result['alt'] = $img_alt;
            }
            $result = acf_blocks_attach_srcset( $result, $attachment_id, $size );
        }
        return $result;
    }

    return $result;
}

/**
 * Attach srcset and sizes attributes to an image result array.
 *
 * Uses WordPress core functions to generate responsive image attributes
 * from an attachment ID and requested size.
 *
 * @param array  $result        Image result array with 'src', 'alt' keys.
 * @param int    $attachment_id WordPress attachment ID.
 * @param string $size          WordPress image size name.
 * @return array Modified result with 'srcset' and 'sizes' populated.
 */
function acf_blocks_attach_srcset( $result, $attachment_id, $size ) {
    if ( $attachment_id < 1 ) {
        return $result;
    }

    $srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
    if ( $srcset ) {
        $result['srcset'] = $srcset;
        $sizes = wp_get_attachment_image_sizes( $attachment_id, $size );
        if ( $sizes ) {
            $result['sizes'] = $sizes;
        }
    }

    return $result;
}

/**
 * Try to find a WordPress attachment ID from a same-domain image URL.
 *
 * Uses attachment_url_to_postid() which looks up the _wp_attached_file meta.
 * Only runs the lookup for URLs that belong to the current site to avoid
 * unnecessary database queries for external images.
 *
 * @param string $url The image URL to look up.
 * @return int Attachment ID, or 0 if not found or external URL.
 */
function acf_blocks_url_to_attachment_id( $url ) {
    static $cache = array();

    if ( isset( $cache[ $url ] ) ) {
        return $cache[ $url ];
    }

    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $url_host  = wp_parse_url( $url, PHP_URL_HOST );

    if ( ! $url_host || ! $site_host ) {
        $cache[ $url ] = 0;
        return 0;
    }

    // Only look up same-domain or subdomain URLs.
    $site_bare = preg_replace( '/^www\./i', '', $site_host );
    $url_bare  = preg_replace( '/^www\./i', '', $url_host );

    if ( $url_bare !== $site_bare && ! acf_blocks_str_ends_with( $url_bare, '.' . $site_bare ) ) {
        $cache[ $url ] = 0;
        return 0;
    }

    $cache[ $url ] = (int) attachment_url_to_postid( $url );
    return $cache[ $url ];
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
    $nested = acf_blocks_resolve_nested_key( $parent_key, $data );
    if ( is_array( $nested ) ) {
        $data = array( $parent_key => $nested );
    }
    return acf_blocks_get_repeater( $parent_key, $sub_names, array( 'data' => $data ) );
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
 * Match a flat field suffix against declared fields, including nested rows.
 *
 * @param string $suffix Candidate suffix after a row index.
 * @param array  $fields Registered child field schemas.
 * @return bool
 */
function acf_blocks_flat_suffix_matches_fields( $suffix, $fields ) {
    foreach ( $fields as $field ) {
        $aliases = array_unique( array_filter( array( $field['name'] ?? '', $field['key'] ?? '' ) ) );
        foreach ( $aliases as $alias ) {
            if ( $suffix === $alias ) {
                return true;
            }
            if ( 'repeater' === ( $field['type'] ?? '' ) && preg_match( '/^' . preg_quote( $alias, '/' ) . '_(0|[1-9][0-9]*)_(.+)$/D', $suffix, $match ) ) {
                if ( (string) (int) $match[1] === $match[1] && acf_blocks_flat_suffix_matches_fields( $match[2], $field['sub_fields'] ?? array() ) ) {
                    return true;
                }
            }
        }
    }
    return false;
}

/**
 * Rebuild flat repeater rows into nested row-N format for the editor.
 *
 * Only materialized rows with declared field keys are reconstructed. Stored
 * counts constrain those rows and never drive allocations or loop bounds.
 *
 * @param string   $prefix Full repeater prefix.
 * @param int|null $count Row count, or null to infer only an absent count.
 * @param array    $sub_fields Registered child field schemas.
 * @param array    $data Flat block data.
 * @return array
 */
function acf_blocks_rebuild_repeater_rows( $prefix, $count, $sub_fields, $data ) {
    if ( array_key_exists( $prefix, $data ) ) {
        $count = $data[ $prefix ];
    }
    if ( null !== $count ) {
        if ( ! is_int( $count ) && ( ! is_string( $count ) || ! preg_match( '/^(?:0|[1-9][0-9]*)$/D', $count ) ) ) {
            return array();
        }
        $count = max( 0, (int) $count );
        if ( 0 === $count ) {
            return array();
        }
    }

    $indexes = array();
    foreach ( array_keys( $data ) as $key ) {
        if ( ! preg_match( '/^' . preg_quote( $prefix, '/' ) . '_(0|[1-9][0-9]*)_(.+)$/D', (string) $key, $match ) ) {
            continue;
        }
        $index = (int) $match[1];
        if ( (string) $index !== $match[1] || ( null !== $count && $index >= $count ) ) {
            continue;
        }
        if ( acf_blocks_flat_suffix_matches_fields( $match[2], $sub_fields ) ) {
            $indexes[ $index ] = $index;
        }
    }
    sort( $indexes, SORT_NUMERIC );

    $nested = array();
    foreach ( $indexes as $index ) {
        $row = array();
        foreach ( $sub_fields as $sub_field ) {
            $sub_name = $sub_field['name'];
            $flat_key = $prefix . '_' . $index . '_' . $sub_name;
            // The schema key can differ from the name, also in imported data.
            if ( ! array_key_exists( $flat_key, $data ) && ! empty( $sub_field['key'] ) ) {
                $schema_key = $prefix . '_' . $index . '_' . $sub_field['key'];
                if ( array_key_exists( $schema_key, $data ) ) {
                    $flat_key = $schema_key;
                }
            }
            if ( 'repeater' === ( $sub_field['type'] ?? '' ) && isset( $sub_field['sub_fields'] ) ) {
                $sub_count = array_key_exists( $flat_key, $data ) ? $data[ $flat_key ] : null;
                $row[ $sub_name ] = acf_blocks_rebuild_repeater_rows( $flat_key, $sub_count, $sub_field['sub_fields'], $data );
            } else {
                $row[ $sub_name ] = $data[ $flat_key ] ?? '';
            }
        }
        $nested[ 'row-' . $index ] = $row;
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
    // Use the cached block metadata to avoid redundant glob/JSON reads.
    $blocks = function_exists( 'acf_blocks_get_block_metadata_cache' )
        ? acf_blocks_get_block_metadata_cache()
        : array();

    foreach ( $blocks as $block_info ) {
        if ( ( $block_info['metadata']['name'] ?? '' ) !== $block_name ) {
            continue;
        }

        $groups = $block_info['field_groups'] ?? array();
        $group  = reset( $groups );
        if ( ! is_array( $group ) ) {
            $data_file = $block_info['folder'] . 'block-data.json';
            if ( ! file_exists( $data_file ) ) {
                return array();
            }
            $group = json_decode( file_get_contents( $data_file ), true );
        }
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

/**
 * Accept a CSS color that is safe to place inside a style attribute.
 *
 * Color pickers store hex, including 4- and 8-digit forms when opacity is on,
 * or rgb()/hsl() functions. Anything else — a named color, a variable, or a
 * value that could add another declaration — is dropped.
 *
 * @param mixed $color Raw color.
 * @return string Sanitized color, or ''.
 */
function acf_blocks_sanitize_css_color( $color ) {
    $color = trim( (string) $color );
    if ( '' === $color || strlen( $color ) > 80 ) {
        return '';
    }
    if ( preg_match( '/[;{}\\\\<>"\']|url\s*\(|expression\s*\(/i', $color ) ) {
        return '';
    }
    if ( preg_match( '/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color ) ) {
        return strtolower( $color );
    }

    $lower = strtolower( $color );
    if ( 'transparent' === $lower || 'currentcolor' === $lower ) {
        return $lower;
    }

    $num   = '(?:0|[1-9]\d{0,2})(?:\.\d+)?%?';
    // 1 and 1.0 are both fully opaque. The second form is what opacity pickers emit.
    $alpha = '(?:(?:0|1)(?:\.\d+)?|0?\.\d+|\d{1,3}%)';
    $hue   = '(?:0|[1-9]\d{0,2})(?:\.\d+)?(?:deg|rad|turn|grad)?';
    $pct   = '(?:0|[1-9]\d{0,2})(?:\.\d+)?%';
    $forms = array(
        '/^rgba?\(\s*' . $num . '(?:\s*,\s*' . $num . '){2}(?:\s*,\s*' . $alpha . ')?\s*\)$/',
        '/^rgba?\(\s*' . $num . '(?:\s+' . $num . '){2}(?:\s*\/\s*' . $alpha . ')?\s*\)$/',
        '/^hsla?\(\s*' . $hue . '\s*,\s*' . $pct . '\s*,\s*' . $pct . '(?:\s*,\s*' . $alpha . ')?\s*\)$/',
        '/^hsla?\(\s*' . $hue . '\s+' . $pct . '\s+' . $pct . '(?:\s*\/\s*' . $alpha . ')?\s*\)$/',
    );
    foreach ( $forms as $form ) {
        if ( preg_match( $form, $lower ) ) {
            return preg_replace( '/\s+/', ' ', $lower );
        }
    }

    return '';
}

/**
 * Keep a single HTML tag name and refuse raw-text or script-bearing tags.
 *
 * @param mixed  $tag      Requested tag.
 * @param string $fallback Tag used when the request is not a safe name.
 * @return string Safe tag name.
 */
function acf_blocks_sanitize_html_tag( $tag, $fallback = 'div' ) {
    $denied = array(
        'script', 'style', 'iframe', 'object', 'embed', 'link', 'meta', 'base',
        'form', 'svg', 'math', 'textarea', 'title', 'noscript', 'template',
    );
    $clean = acf_blocks_match_html_tag( $tag, $denied );
    if ( '' !== $clean ) {
        return $clean;
    }
    $clean = acf_blocks_match_html_tag( $fallback, $denied );
    return '' !== $clean ? $clean : 'div';
}

/**
 * @param mixed $tag    Candidate tag.
 * @param array $denied Tag names that must never be emitted.
 * @return string
 */
function acf_blocks_match_html_tag( $tag, $denied ) {
    $tag = strtolower( trim( (string) $tag ) );
    if ( ! preg_match( '/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/', $tag ) ) {
        return '';
    }
    if ( in_array( $tag, $denied, true ) ) {
        return '';
    }
    return $tag;
}

/**
 * Read an ACF true/false value. The string "0" is off.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function acf_blocks_is_on( $value ) {
    return true === $value || 1 === $value || '1' === $value;
}

/**
 * Limit a button style to the variants the stylesheets define.
 *
 * @param mixed $style Raw style value.
 * @return string primary, secondary, outline, or ''.
 */
function acf_blocks_button_variant( $style ) {
    $style = strtolower( trim( (string) $style ) );
    return in_array( $style, array( 'primary', 'secondary', 'outline' ), true ) ? $style : '';
}

/**
 * target and rel for an optional new-tab link. Empty when the switch is off,
 * so older blocks keep the attributes they already had (none).
 *
 * @param mixed $new_tab True when the link should open in a new tab.
 * @return string Leading space plus attributes, or ''.
 */
function acf_blocks_link_target_attrs( $new_tab ) {
    if ( ! (bool) (int) $new_tab ) {
        return '';
    }
    return ' target="_blank" rel="noopener noreferrer"';
}

/**
 * Checklist colors. The accent default stays so existing lists keep their
 * green checks. The light panel that older versions wrote inline (#f9fafb)
 * is omitted: an inline custom property beats the stylesheet, and that
 * near-white surface stayed light in dark mode.
 *
 * @param mixed $accent     Accent color.
 * @param mixed $background Background color.
 * @return string Custom properties, always ending with ';'.
 */
function acf_blocks_checklist_style( $accent, $background ) {
    $accent = acf_blocks_sanitize_css_color( $accent );
    if ( '' === $accent ) {
        $accent = '#16a34a';
    }
    $parts = array( '--checklist-accent:' . $accent );

    $background = acf_blocks_sanitize_css_color( $background );
    if ( '' !== $background && '#f9fafb' !== $background ) {
        $parts[] = '--checklist-bg:' . $background;
    }

    return implode( ';', $parts ) . ';';
}

/**
 * Non-empty lines from a textarea, in order.
 *
 * @param mixed $value Raw text.
 * @return array
 */
function acf_blocks_lines( $value ) {
    $lines = preg_split( '/\r\n|\r|\n/', (string) $value );
    if ( ! is_array( $lines ) ) {
        return array();
    }
    $out = array();
    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( '' !== $line ) {
            $out[] = $line;
        }
    }
    return $out;
}

/**
 * Return $value when it is one of $allowed, otherwise $default.
 *
 * @param mixed  $value   Raw value.
 * @param array  $allowed Allowed values.
 * @param string $default Fallback.
 * @return string
 */
function acf_blocks_choice( $value, $allowed, $default ) {
    $value = (string) $value;
    return in_array( $value, $allowed, true ) ? $value : $default;
}
