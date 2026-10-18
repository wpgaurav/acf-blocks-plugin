<?php
// Separate process: simulate metadata discovered without a generated manifest.
define( 'ABSPATH', __DIR__ );
function acf_blocks_get_block_metadata_cache( $include_disabled = false ) {
    $root = dirname( __DIR__, 2 ) . '/blocks/';
    return array(
        array( 'metadata' => array( 'name' => 'acf/pricing' ), 'folder' => $root . 'pricing-block/', 'field_groups' => null ),
        array( 'metadata' => array( 'name' => 'acf/email-form' ), 'folder' => $root . 'email-form/', 'field_groups' => null ),
    );
}
function get_field( $name ) {
    if ( 'acf_pricing_plans' === $name ) {
        return array( array( 'field_acf_plan_name' => 'Native fallback' ) );
    }
    return null;
}
function apply_filters( $name, $value ) { return $value; }
function add_filter() {}
require dirname( __DIR__, 2 ) . '/includes/compat.php';
echo json_encode( array(
    'rows' => acf_blocks_get_repeater( 'acf_pricing_plans', array( 'plan_name' ), array( 'name' => 'acf/pricing', 'data' => array(
        'field_acf_pricing_plans' => array( array( 'field_acf_plan_name' => 'Source schema' ) ),
    ) ) ),
    'group' => acf_blocks_get_field( 'form_attributes', array( 'name' => 'acf/email-form', 'data' => array(
        'field_form_attributes' => array( 'field_form_attr_class' => 'source-schema-class' ),
    ) ) ),
    'native' => acf_blocks_get_repeater( 'acf_pricing_plans', array( 'plan_name' ), array( 'name' => 'acf/pricing' ) ),
) );
