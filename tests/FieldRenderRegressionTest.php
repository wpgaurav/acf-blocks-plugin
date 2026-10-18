<?php

use PHPUnit\Framework\TestCase;

if ( ! function_exists( 'wp_json_encode' ) ) {
    function wp_json_encode( $value, $options = 0, $depth = 512 ) { return json_encode( $value, $options, $depth ); }
}
if ( ! function_exists( 'checked' ) ) {
    function checked( $checked, $current = true, $echo = true ) {
        $result = (string) $checked === (string) $current ? ' checked="checked"' : '';
        if ( $echo ) { echo $result; }
        return $result;
    }
}
if ( ! function_exists( 'esc_js' ) ) {
    function esc_js( $value ) { return addslashes( (string) $value ); }
}

final class FieldRenderRegressionTest extends TestCase {
    public function test_changelog_formats_saved_date_picker_values(): void {
        $html = $this->render( 'changelog-block', 'acf/changelog', array( 'changelog_entries' => array( array(
            'changelog_version' => '2.13.1', 'changelog_date' => '20261018', 'changelog_items' => array(),
        ) ) ) );
        $this->assertStringContainsString( 'October 18, 2026', $html );
        $this->assertStringNotContainsString( '>20261018<', $html );
    }

    public function test_decimal_cast_rejects_non_finite_numbers(): void {
        $this->assertSame( 0.0, acf_blocks_cast_field_value( '1e309', 'number' ) );
        $this->assertSame( 0.0, acf_blocks_cast_field_value( '-1e309', 'number' ) );
        $this->assertSame( 4.5, acf_blocks_cast_field_value( '4.5', 'number' ) );
    }

    public function test_star_renderer_bounds_numeric_input_to_five_stars(): void {
        $this->render( 'product-review', 'acf/product-review', array() );
        $this->assertSame( 5, substr_count( acf_render_star_svg( 1000 ), '<svg' ) );
        $this->assertSame( 5, substr_count( acf_render_star_svg( -1000 ), '<svg' ) );
    }

    private function render( $folder, $name, $data, $extra = array(), $render_post_id = 42 ) {
        $block = array_merge( array( 'name' => $name, 'data' => $data ), $extra );
        $content = '';
        $is_preview = false;
        $post_id = $render_post_id;
        ob_start();
        include dirname( __DIR__ ) . '/blocks/' . $folder . '/' . $folder . '.php';
        return ob_get_clean();
    }

    public function test_nested_changelog_schema_keys_are_normalized_recursively(): void {
        $data = array( 'field_changelog_entries' => array( 'row-0' => array(
            'field_changelog_version' => '3.0',
            'field_changelog_items' => array( 'row-0' => array( 'field_changelog_type' => 'security', 'field_changelog_text' => 'Keep tokens private.' ) ),
        ) ) );
        $html = $this->render( 'changelog-block', 'acf/changelog', $data );
        $this->assertStringContainsString( 'data-type="security"', $html );
        $this->assertStringContainsString( 'Keep tokens private.', $html );
    }

    public function test_flat_nested_changelog_is_normalized(): void {
        $html = $this->render( 'changelog-block', 'acf/changelog', array(
            'changelog_entries' => '1', 'changelog_entries_0_changelog_version' => '3.0',
            'changelog_entries_0_changelog_items' => '1',
            'changelog_entries_0_changelog_items_0_changelog_type' => 'fixed',
            'changelog_entries_0_changelog_items_0_changelog_text' => 'Retain the description.',
        ) );
        $this->assertStringContainsString( 'Retain the description.', $html );
        $this->assertStringContainsString( 'data-type="fixed"', $html );
    }

    /** @dataProvider marketing_repeater_fields */
    public function test_registered_schema_keys_are_used_for_marketing_rows( $block_name, $name, $child_name, $child_key ): void {
        $rows = acf_blocks_get_repeater( $name, array( $child_name ), array( 'name' => $block_name, 'data' => array(
            'field_' . $name => array( 'row-0' => array( $child_key => 'Actual schema key' ) ),
        ) ) );
        $this->assertSame( 'Actual schema key', $rows[0][ $child_name ] );
    }

    public function marketing_repeater_fields(): array {
        return array(
            array( 'acf/pricing', 'acf_pricing_plans', 'plan_name', 'field_acf_plan_name' ),
            array( 'acf/steps', 'acf_steps_items', 'step_title', 'field_acf_step_title' ),
            array( 'acf/trust-bar', 'acf_trust_items', 'trust_title', 'field_acf_trust_title' ),
            array( 'acf/logo-cloud', 'acf_logo_items', 'logo_name', 'field_acf_logo_name' ),
        );
    }

    public function test_canonical_nested_and_flat_keys_remain_supported(): void {
        foreach ( array(
            array( 'acf_pricing_plans' => array( array( 'plan_name' => 'Canonical row' ) ) ),
            array( 'acf_pricing_plans' => 1, 'acf_pricing_plans_0_plan_name' => 'Canonical row' ),
            array( 'field_acf_pricing_plans' => 1, 'field_acf_pricing_plans_0_field_acf_plan_name' => 'Canonical row' ),
        ) as $data ) {
            $rows = acf_blocks_get_repeater( 'acf_pricing_plans', array( 'plan_name' ), array( 'name' => 'acf/pricing', 'data' => $data ) );
            $this->assertSame( 'Canonical row', $rows[0]['plan_name'] );
        }
    }

    public function test_schema_aliases_survive_missing_generated_manifest(): void {
        $result = shell_exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __DIR__ . '/fixtures/compat-schema-fallback.php' ) );
        $data = json_decode( $result, true );
        $this->assertIsArray( $data, $result );
        $this->assertSame( 'Source schema', $data['rows'][0]['plan_name'] );
        $this->assertSame( 'source-schema-class', $data['group']['class'] );
        $this->assertSame( 'Native fallback', $data['native'][0]['plan_name'] );
    }

    public function test_corrupt_repeater_count_is_bounded_by_payload(): void {
        $rows = acf_blocks_get_repeater( 'features', array( 'feature_name' ), array( 'data' => array( 'features' => '999999999999999999999999999999999' ) ) );
        $this->assertLessThanOrEqual( 1, count( $rows ) );
    }

    private function editor_repeater_fields(): array {
        return array(
            array( 'name' => 'title', 'type' => 'text' ),
            array( 'name' => 'items', 'type' => 'repeater', 'sub_fields' => array( array( 'name' => 'text', 'type' => 'text' ) ) ),
        );
    }

    public function test_editor_rebuild_does_not_revive_explicit_zero_nested_rows(): void {
        $rows = acf_blocks_rebuild_repeater_rows( 'entries', 1, $this->editor_repeater_fields(), array(
            'entries' => 1, 'entries_0_title' => 'Current', 'entries_0_items' => '0', 'entries_0_items_0_text' => 'Deleted',
        ) );
        $this->assertSame( array(), $rows['row-0']['items'] );
    }

    public function test_editor_rebuild_respects_explicit_outer_zero(): void {
        $rows = acf_blocks_rebuild_repeater_rows( 'entries', 3, $this->editor_repeater_fields(), array(
            'entries' => '0', 'entries_0_title' => 'Deleted',
        ) );
        $this->assertSame( array(), $rows );
    }

    public function test_editor_rebuild_counts_are_bounded_by_materialized_rows(): void {
        $rows = acf_blocks_rebuild_repeater_rows( 'entries', 5000, $this->editor_repeater_fields(), array(
            'entries' => 5000, 'entries_0_title' => 'Current', 'entries_0_items' => 1000, 'entries_0_items_0_text' => 'Current item',
        ) );
        $this->assertCount( 1, $rows );
        $this->assertCount( 1, $rows['row-0']['items'] );
        $this->assertSame( 'Current item', $rows['row-0']['items']['row-0']['text'] );
    }

    public function test_editor_rebuild_ignores_malformed_or_unknown_row_keys(): void {
        $rows = acf_blocks_rebuild_repeater_rows( 'entries', 5000, $this->editor_repeater_fields(), array(
            'entries' => 5000, 'entries_0_title' => 'Current', 'entries_01_title' => 'Leading zero',
            'entries_-1_title' => 'Negative', 'entries_1_unknown' => 'Unknown field', 'entries_2_title_suffix' => 'Malformed suffix',
            'entries_999999999999999999999999999_title' => 'Overflow',
        ) );
        $this->assertSame( array( 'row-0' ), array_keys( $rows ) );
    }

    public function test_editor_rebuild_infers_only_absent_nested_counts(): void {
        $rows = acf_blocks_rebuild_repeater_rows( 'entries', 1, $this->editor_repeater_fields(), array(
            'entries' => 1, 'entries_0_title' => 'Current', 'entries_0_items_0_text' => 'Inferred item',
        ) );
        $this->assertSame( 'Inferred item', $rows['row-0']['items']['row-0']['text'] );
    }

    public function test_groups_use_exact_child_schema_keys_and_flat_subfields(): void {
        $fields = array( 'id', 'class', 'inline_css' );
        $nested = acf_blocks_get_group( 'form_attributes', $fields, array( 'name' => 'acf/email-form', 'data' => array(
            'field_form_attributes' => array( 'field_form_attr_id' => 'signup', 'field_form_attr_class' => 'compact' ),
        ) ) );
        $this->assertSame( 'signup', $nested['id'] );
        $this->assertSame( 'compact', $nested['class'] );
        $flat = acf_blocks_get_group( 'email_field_attributes', $fields, array( 'name' => 'acf/email-form', 'data' => array(
            'email_field_attributes_id' => 'email', 'email_field_attributes_class' => 'wide',
        ) ) );
        $this->assertSame( 'email', $flat['id'] );
        $this->assertSame( 'wide', $flat['class'] );
    }

    public function test_group_inside_flat_repeater_retains_attributes(): void {
        $rows = acf_blocks_get_repeater( 'hidden_fields', array( 'field_name', 'field_value', 'attributes' ), array(
            'name' => 'acf/email-form', 'data' => array(
                'hidden_fields' => 1, 'hidden_fields_0_field_name' => 'list', 'hidden_fields_0_field_value' => 'newsletter',
                'hidden_fields_0_attributes_id' => 'list-id', 'hidden_fields_0_attributes_class' => 'hidden-field',
            ),
        ) );
        $this->assertSame( 'newsletter', $rows[0]['field_value'] );
        $this->assertSame( 'list-id', $rows[0]['attributes']['id'] );
        $this->assertSame( 'hidden-field', $rows[0]['attributes']['class'] );
    }

    public function test_explicit_empty_repeater_does_not_infer_stale_rows(): void {
        $rows = acf_blocks_get_repeater( 'features', array( 'feature_name' ), array( 'data' => array(
            'features' => 0, 'features_0_feature_name' => 'Deleted row',
        ) ) );
        $this->assertSame( array(), $rows );
    }

    public function test_decimal_number_cast_is_distinct_from_integer(): void {
        $this->assertSame( 4.5, acf_blocks_cast_field_value( '4.5', 'number' ) );
        $this->assertSame( 4, acf_blocks_cast_field_value( '4.5', 'int' ) );
        $rows = acf_blocks_get_repeater( 'features', array( 'feature_rating' => 'number' ), array( 'data' => array(
            'features' => array( array( 'feature_rating' => '4.5' ) ),
        ) ) );
        $this->assertSame( 4.5, $rows[0]['feature_rating'] );
    }

    public function test_compare_legacy_list_fallback_applies_per_row(): void {
        $html = $this->render( 'compare-block', 'acf/compare', array( 'comp_columns_data' => array(
            array( 'comp_title' => 'Legacy', 'comp_list_class' => '<ul><li>Legacy feature</li></ul>' ),
            array( 'comp_title' => 'Modern', 'comp_list_content' => '<ul><li>Modern feature</li></ul>' ),
        ) ) );
        $this->assertStringContainsString( 'Legacy feature', $html );
        $this->assertStringContainsString( 'Modern feature', $html );
    }

    public function test_compare_legacy_nested_repeater_normalizes_field_keys(): void {
        $html = $this->render( 'compare-block', 'acf/compare', array( 'field_comp_columns_data' => array( 'row-0' => array(
            'field_comp_title' => 'Legacy nested', 'field_comp_repeater_list' => array( 'row-0' => array( 'field_comp_list_item' => 'Nested legacy feature' ) ),
        ) ) ) );
        $this->assertStringContainsString( 'Nested legacy feature', $html );
    }

    public function test_secondary_only_cta_renders(): void {
        $html = $this->render( 'cta-block', 'acf/cta', array( 'acf_cta_button_2_text' => 'Read details', 'acf_cta_button_2_url' => 'https://example.test/details' ) );
        $this->assertStringContainsString( 'Read details', $html );
        $this->assertStringContainsString( 'href="https://example.test/details"', $html );
        $this->assertStringNotContainsString( '<InnerBlocks', $html );
    }

    public function test_zero_price_is_shown(): void {
        $html = $this->render( 'pricing-block', 'acf/pricing', array( 'acf_pricing_plans' => array( array( 'plan_name' => 'Free', 'plan_price' => '0' ) ) ) );
        $this->assertStringContainsString( 'class="acf-pricing__amount">0</span>', $html );
    }

    public function test_checklist_post_context_separates_plain_permalink_pages(): void {
        $data = array( 'checklist_interactive' => '1', 'checklist_items' => array( array( 'checklist_item_text' => 'Same task' ) ) );
        $first = $this->render( 'checklist-block', 'acf/checklist', $data, array( 'id' => 'same-id' ), 42 );
        $second = $this->render( 'checklist-block', 'acf/checklist', $data, array( 'id' => 'same-id' ), 84 );
        preg_match( '/data-checklist-key="([^"]+)"/', $first, $one );
        preg_match( '/data-checklist-key="([^"]+)"/', $second, $two );
        $this->assertNotSame( $one[1], $two[1] );
    }

    public function test_checklist_has_stable_persistence_identity_separate_from_dom_id(): void {
        $data = array( 'checklist_interactive' => '1', 'checklist_items' => array( array( 'checklist_item_text' => 'Same task' ) ) );
        $first = $this->render( 'checklist-block', 'acf/checklist', $data, array( 'id' => 'block_persisted' ) );
        $second = $this->render( 'checklist-block', 'acf/checklist', $data, array( 'id' => 'block_persisted' ) );
        $this->assertMatchesRegularExpression( '/data-checklist-key="[^"]+"/', $first );
        preg_match( '/data-checklist-key="([^"]+)"/', $first, $one );
        preg_match( '/data-checklist-key="([^"]+)"/', $second, $two );
        $this->assertSame( $one[1], $two[1] );
        $this->assertNotSame( $first, $second, 'DOM checkbox IDs remain unique.' );
    }
}
