<?php

define( 'ABSPATH', __DIR__ . '/wordpress/' );
define( 'ACF_BLOCKS_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'ACF_BLOCKS_PLUGIN_URL', 'https://example.test/wp-content/plugins/acf-blocks-plugin/' );
define( 'ACF_BLOCKS_VERSION', 'test' );

function add_action() {}
function add_filter() {}
function apply_filters( $hook, $value ) { return $value; }
function sanitize_text_field( $value ) { return trim( (string) $value ); }

require_once dirname( __DIR__ ) . '/includes/functions.php';
