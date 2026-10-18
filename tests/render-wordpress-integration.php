<?php
/**
 * Optional saved-block smoke tests against real WordPress and ACF Pro/SCF.
 * Uses the same disposable database/runtime as storage-wordpress-integration.php.
 * Also set ACFB_TEST_ACF_PLUGIN to the installed plugin's acf.php entry point.
 */
require __DIR__ . '/fixtures/storage-wordpress-bootstrap.php';
$acf_plugin = getenv( 'ACFB_TEST_ACF_PLUGIN' );
if ( ! $acf_plugin || ! is_readable( $acf_plugin ) ) {
    throw new RuntimeException( 'Set ACFB_TEST_ACF_PLUGIN to an ACF Pro or Secure Custom Fields acf.php.' );
}
require_once $acf_plugin;
require_once ACF_BLOCKS_PLUGIN_DIR . 'includes/compat.php';
acf_init();
if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'acf/pricing' ) ) {
    acf_blocks_load_blocks();
}
$checks = array();
$created = array();
function acfb_render_check( $condition, $name ) {
    global $checks;
    $checks[ $name ] = (bool) $condition;
}
function acfb_render_block( $name, $data = array() ) {
    return render_block( array(
        'blockName' => $name,
        'attrs' => array( 'name' => $name, 'id' => 'block_saved_render_test', 'data' => $data ),
        'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array(),
    ) );
}
try {
    $ours = array_filter( array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ), function ( $name ) { return 0 === strpos( $name, 'acf/' ); } );
    acfb_render_check( 33 === count( $ours ), 'all 33 ACF blocks register with real SCF' );
    $toc_type = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/toc' );
    $versioned = ! empty( $toc_type->view_script_handles );
    foreach ( $toc_type->view_script_handles as $handle ) {
        $script = wp_scripts()->registered[ $handle ] ?? null;
        $versioned = $versioned && $script && ACF_BLOCKS_VERSION === $script->ver;
    }
    acfb_render_check( $versioned, 'TOC runtime uses the plugin version despite old block metadata' );
    $html = acfb_render_block( 'acf/pricing', array( 'acf_pricing_plans' => 1, 'acf_pricing_plans_0_plan_name' => 'Free', 'acf_pricing_plans_0_plan_price' => '0' ) );
    acfb_render_check( false !== strpos( $html, '>Free<' ) && false !== strpos( $html, 'acf-pricing__amount">0<' ), 'saved flat pricing renders a zero amount' );
    $html = acfb_render_block( 'acf/cta', array( 'acf_cta_button_2_text' => 'Secondary only', 'acf_cta_button_2_url' => 'https://example.invalid/details' ) );
    acfb_render_check( false !== strpos( $html, 'Secondary only' ), 'secondary-only CTA renders through ACF callback' );
    $html = acfb_render_block( 'acf/product-review', array( 'features' => 1, 'features_0_feature_name' => 'Decimal feature', 'features_0_feature_rating' => '4.5' ) );
    acfb_render_check( false !== strpos( $html, 'Rating: 4.5 out of 5' ), 'half-star rating survives real ACF setup' );
    $html = acfb_render_block( 'acf/compare', array( 'comp_columns_data' => 1, 'comp_columns_data_0_comp_title' => 'Legacy', 'comp_columns_data_0_comp_list_class' => '<ul><li>Preserved legacy feature</li></ul>' ) );
    acfb_render_check( false !== strpos( $html, 'Preserved legacy feature' ), 'saved legacy comparison list remains visible' );
    $html = acfb_render_block( 'acf/changelog', array( 'changelog_entries' => 1, 'changelog_entries_0_changelog_version' => '3.0', 'changelog_entries_0_changelog_items' => 1, 'changelog_entries_0_changelog_items_0_changelog_type' => 'fixed', 'changelog_entries_0_changelog_items_0_changelog_text' => 'Nested real render' ) );
    acfb_render_check( false !== strpos( $html, 'Nested real render' ) && false !== strpos( $html, 'data-type="fixed"' ), 'nested changelog text and type survive saved blocks' );

    $form = array( 'blockName' => 'acf/email-form', 'attrs' => array( 'name' => 'acf/email-form', 'data' => array(
        'form_type' => 'webhook', 'webhook_url' => 'https://hooks.example.invalid/submit', 'webhook_auth_headers' => "Authorization: Bearer private-integration-token\nX-API-Key: second-test-key",
        'hidden_fields' => 1, 'hidden_fields_0_field_name' => 'list_id', 'hidden_fields_0_field_value' => 'newsletter', 'email_field_attributes_id' => 'saved-email',
    ) ), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
    $id = wp_insert_post( array( 'post_title' => 'Saved form smoke', 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => wp_slash( serialize_block( $form ) ) ), true );
    if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
    $created[] = $id;
    $GLOBALS['post'] = get_post( $id );
    setup_postdata( $GLOBALS['post'] );
    $html = do_blocks( get_post_field( 'post_content', $id ) );
    acfb_render_check( false === strpos( $html, 'private-integration-token' ) && false === strpos( $html, 'hooks.example.invalid' ) && false !== strpos( $html, 'formRef' ), 'public form contains a reference without destination or secrets' );
    acfb_render_check( false !== strpos( $html, 'name="list_id"' ) && false !== strpos( $html, 'value="newsletter"' ) && false !== strpos( $html, 'id="saved-email"' ), 'saved hidden fields and custom input IDs render with SCF' );
    preg_match( '/window\.acfEmailFormConfigs\[.*?\] = (.*?);/', $html, $match );
    $config = json_decode( $match[1] ?? '', true );
    $ref = $config['formRef'] ?? '';
    acfb_render_check( '' !== $ref && null !== acf_email_form_resolve_reference( $ref ), 'rendered signed reference resolves current saved settings' );
    wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) );
    acfb_render_check( null === acf_email_form_resolve_reference( $ref ), 'making source private revokes cached signed form' );

    acf_star_rating_install_tables();
    $example = '</script><script>alert(1)</script>';
    foreach ( array(
        'acf/product-review' => array( 'product_name' => $example, 'enable_json_ld' => 1 ),
        'acf/star-rating' => array( 'md_sr_enable_schema' => 1, 'md_sr_schema_name' => $example, 'md_sr_initial_count' => 1, 'md_sr_initial_rating' => 4 ),
    ) as $name => $data ) {
        $html = acfb_render_block( $name, $data );
        preg_match( '#<script type="application/ld\+json">\s*(.*?)\s*</script>#s', $html, $schema_match );
        $schema = json_decode( $schema_match[1] ?? '', true );
        acfb_render_check( is_array( $schema ) && ( $schema['name'] ?? '' ) === $example && 1 === substr_count( $html, '<script' ), $name . ' schema keeps script examples inert and valid' );
    }

    $toc = array( 'blockName' => 'acf/toc', 'attrs' => array( 'name' => 'acf/toc', 'data' => array( 'toc_heading_levels' => array( 'h2' ), 'toc_schema' => 1 ) ), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() );
    // Resolve the stable registered TOC name, which has a legacy long name.
    foreach ( $ours as $name ) { if ( false !== strpos( $name, 'table-of-contents' ) ) { $toc['blockName'] = $name; $toc['attrs']['name'] = $name; } }
    $content = serialize_block( $toc ) . "\n<!-- wp:html --><h2 id=\"custom-anchor\">Custom HTML</h2><!-- /wp:html -->\n<!-- wp:heading --><h2 class=\"wp-block-heading\">Chapter</h2><!-- /wp:heading -->";
    wp_update_post( array( 'ID' => $id, 'post_status' => 'publish', 'post_content' => wp_slash( $content ) ) );
    $GLOBALS['post'] = get_post( $id );
    setup_postdata( $GLOBALS['post'] );
    $html = apply_filters( 'the_content', $content );
    acfb_render_check( false !== strpos( $html, 'href="#custom-anchor"' ) && false !== strpos( $html, 'id="custom-anchor"' ), 'TOC includes rendered HTML and preserves custom anchors' );
    acfb_render_check( false !== strpos( $html, 'href="#chapter"' ) && 1 === substr_count( $html, 'id="chapter"' ), 'TOC gives core heading one matching unique anchor' );
} finally {
    wp_set_current_user( 1 );
    foreach ( $created as $id ) { wp_delete_post( $id, true ); }
    wp_reset_postdata();
}
echo wp_json_encode( $checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
exit( count( $checks ) < 15 || in_array( false, $checks, true ) ? 1 : 0 );
