<?php
/**
 * Generate the cached block and field-group manifest.
 *
 * Usage:
 *   php tools/generate-manifest.php
 *   php tools/generate-manifest.php --check
 */

$root       = dirname( __DIR__ );
$blocks_dir = $root . '/blocks';
$target     = $root . '/includes/generated-block-manifest.php';
$check      = in_array( '--check', $argv, true );
$manifest   = array();
$folders    = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

sort( $folders, SORT_STRING );

foreach ( $folders as $folder ) {
    $block_file = $folder . '/block.json';
    if ( ! is_file( $block_file ) ) {
        continue;
    }

    $metadata = json_decode( (string) file_get_contents( $block_file ), true );
    if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
        fwrite( STDERR, "Invalid block metadata: {$block_file}\n" );
        exit( 1 );
    }

    $field_groups = array();
    foreach ( glob( $folder . '/*.json' ) as $json_file ) {
        if ( 'block.json' === basename( $json_file ) ) {
            continue;
        }

        $data = json_decode( (string) file_get_contents( $json_file ), true );
        if ( ! is_array( $data ) ) {
            fwrite( STDERR, "Invalid field data: {$json_file}\n" );
            exit( 1 );
        }

        if ( isset( $data['key'], $data['fields'] ) ) {
            $data = array( $data );
        }

        foreach ( $data as $group ) {
            if ( is_array( $group ) && isset( $group['key'], $group['fields'] ) ) {
                $field_groups[] = $group;
            }
        }
    }

    $manifest[ basename( $folder ) ] = array(
        'metadata'     => $metadata,
        'field_groups' => $field_groups,
    );
}

$output  = "<?php\n";
$output .= "/**\n * Generated file. Run php tools/generate-manifest.php after block metadata changes.\n */\n";
$output .= "return " . var_export( $manifest, true ) . ";\n";

if ( $check ) {
    $current = is_file( $target ) ? (string) file_get_contents( $target ) : '';
    if ( $current !== $output ) {
        fwrite( STDERR, "Generated manifest is out of date.\n" );
        exit( 1 );
    }
    echo "Generated manifest is current.\n";
    exit( 0 );
}

file_put_contents( $target, $output );
echo 'Generated ' . count( $manifest ) . " block definitions.\n";
