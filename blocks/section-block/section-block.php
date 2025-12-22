<?php
/**
 * Section Block Template.
 *
 * @param   array  $block     The block settings and attributes.
 * @param   string $content   The inner blocks content.
 * @param   bool   $is_preview True during AJAX preview.
 * @param   int    $post_id   The post ID where the block is rendered.
 */

// Get basic structure fields
$html_tag     = get_field('acf_section_html_tag') ?: 'section';
$custom_tag   = get_field('acf_section_custom_tag');
$section_id   = get_field('acf_section_id');
$custom_css   = get_field('acf_custom_css');

// Get utility classes instead of individual styling options
$layout_class = get_field('acf_layout_class') ?: '';
$spacing_class = get_field('acf_spacing_class') ?: '';
$bg_class = get_field('acf_bg_class') ?: '';
$text_class = get_field('acf_text_class') ?: '';
$responsive_class = get_field('acf_responsive_class') ?: '';

// Get custom classes
$custom_class = get_field('acf_section_custom_class');

// Get background fields (keeping these as they're important for visual design)
$bg_color  = get_field('acf_bg_color');
$bg_image  = get_field('acf_bg_image');
$bg_overlay = get_field('acf_bg_overlay');
$bg_video  = get_field('acf_bg_video');

// Determine the final HTML tag
$tag = ($html_tag === 'custom' && !empty($custom_tag)) ? $custom_tag : $html_tag;

// Build classes array from utility classes
$classes = array();

if ($layout_class) {
    $classes = array_merge($classes, explode(' ', $layout_class));
}

if ($spacing_class) {
    $classes = array_merge($classes, explode(' ', $spacing_class));
}

if ($bg_class) {
    $classes = array_merge($classes, explode(' ', $bg_class));
}

if ($text_class) {
    $classes = array_merge($classes, explode(' ', $text_class));
}

if ($responsive_class) {
    $classes = array_merge($classes, explode(' ', $responsive_class));
}

// Add custom class
if ($custom_class) {
    $classes = array_merge($classes, explode(' ', $custom_class));
}

// Build inline styles
$styles = array();

// Add background styles
if ($bg_color) {
    $styles[] = 'background-color:' . $bg_color;
}

if ($bg_image) {
    $styles[] = 'background-image: url(' . esc_url($bg_image) . ')';
    $styles[] = 'background-size: cover';
    $styles[] = 'background-position: center';
}

// Combine classes and styles
$class_attr = !empty($classes) ? ' class="' . esc_attr(implode(' ', $classes)) . '"' : '';
$style_attr = !empty($styles) ? ' style="' . esc_attr(implode('; ', $styles)) . '"' : '';
$id_attr = !empty($section_id) ? ' id="' . esc_attr($section_id) . '"' : '';

// Store custom CSS if provided (will be output in footer)
if (!empty($custom_css) && function_exists('md_store_block_css')) {
    md_store_block_css($custom_css, $block['id']);
}

// Create a wrapper if we need background video or overlay
$has_overlay = !empty($bg_overlay);
$has_video = !empty($bg_video);
$needs_wrapper = $has_overlay || $has_video;
?>

<?php if ($needs_wrapper): ?>
<<?php echo esc_attr($tag); ?><?php echo $id_attr . $class_attr . $style_attr; ?>>
    <?php if ($has_video): ?>
    <div class="section-bg-video">
        <video autoplay muted loop playsinline>
            <source src="<?php echo esc_url($bg_video); ?>" type="video/mp4">
        </video>
    </div>
    <?php endif; ?>
    
    <?php if ($has_overlay): ?>
    <div class="section-bg-overlay" style="background-color: <?php echo esc_attr($bg_overlay); ?>"></div>
    <?php endif; ?>
    
    <div class="section-content">
        <InnerBlocks templateLock="false" />
    </div>
</<?php echo esc_attr($tag); ?>>
<?php else: ?>
<<?php echo esc_attr($tag); ?><?php echo $id_attr . $class_attr . $style_attr; ?>>
    <InnerBlocks templateLock="false" />
</<?php echo esc_attr($tag); ?>>
<?php endif; ?>