<?php
/**
 * Callout Block Template.
 *
 * @param array $block The block settings and attributes.
 */

// Get ACF fields
$title = get_field('callout_title');
$text = get_field('callout_text');
$icon = get_field('callout_icon');
$iconImage = get_field('callout_iconImage');
$buttonText = get_field('callout_buttonText');
$url = get_field('callout_url');
$href = !empty($url) ? $url : '#';
$align = $block['align'] ?? '';
$alignment = get_field('callout_alignment');
$marginBottom = get_field('callout_marginBottom');
$padding = get_field('callout_padding');
$shadow = get_field('callout_shadow');
$className = $block['className'] ?? '';
$borderColor = get_field('callout_borderColor');
$bgColor = get_field('callout_bgColor');
$bgColorClass = get_field('callout_bgColorClass');
$bgImage = get_field('callout_bgImage');
$iconColor = get_field('callout_iconColor');
$textColor = get_field('callout_textColor');
$textColorClass = get_field('callout_textColorClass');
$buttonColor = get_field('callout_buttonColor');
$buttonColorClass = get_field('callout_buttonColorClass');
$buttonTextColor = get_field('callout_buttonTextColor');
$buttonTextColorClass = get_field('callout_buttonTextColorClass');

// Custom class fields for each element
$calloutCustomClass = get_field('callout_custom_class');
$iconCustomClass = get_field('callout_icon_custom_class');
$titleCustomClass = get_field('callout_title_custom_class');
$textCustomClass = get_field('callout_text_custom_class');
$buttonCustomClass = get_field('callout_button_custom_class');

// Process values
$bg_color = empty($bgColorClass) && !empty($bgColor) ? 'background-color: ' . esc_attr($bgColor) . '; ' : '';
$border_color = !empty($borderColor) ? 'border-color: ' . esc_attr($borderColor) . ';' : '';
$bg_image = !empty($bgImage) ? ' background-image: url(\'' . esc_url($bgImage) . '\');' : '';
$has_textColor = !empty($textColor) && empty($textColorClass) ? true : false;
$has_buttonColor = !empty($buttonColor) && empty($buttonColorClass) ? true : false;
$has_buttonTextColor = !empty($buttonTextColor) && empty($buttonTextColorClass) ? true : false;
$style = $has_textColor || $bgColor || $borderColor || $bgImage ? ' style="' . $bg_color . $bg_image . $border_color . ($has_textColor ? ' color: ' . esc_attr($textColor) . ';' : '') . '"' : '';

$classes = $button_classes = array();
$classes[] = 'callout';
$classes[] = !empty($align) ? "align{$align}" : '';
$classes[] = !empty($bgColorClass) ? $bgColorClass : '';
$classes[] = !empty($textColorClass) ? $textColorClass : '';
$classes[] = !empty($marginBottom) ? $marginBottom : 'mb-double';
$classes[] = !empty($padding) ? $padding : 'block-mid';
if (!empty($className))
    $classes[] = $className;
if (!empty($icon) || !empty($iconImage))
    $classes[] = 'has-icon';
if (!empty($alignment))
    $classes[] = 'text-' . esc_attr($alignment);
if (!empty($shadow))
    $classes[] = 'shadow-small';
if (!empty($bgImage))
    $classes[] = 'image-overlay';
$classes[] = 'mt-double';
// Add custom class to main element
if (!empty($calloutCustomClass))
    $classes[] = esc_attr($calloutCustomClass);
$classes = join(' ', $classes);

if (!empty($buttonTextColorClass))
    $button_classes[] = $buttonTextColorClass;
$button_classes[] = !empty($buttonColorClass) ? $buttonColorClass : '';
// Add custom class to button
if (!empty($buttonCustomClass))
    $button_classes[] = esc_attr($buttonCustomClass);
$button_classes = join(' ', $button_classes);
?>

<div class="<?php echo esc_attr($classes); ?>"<?php echo $style; ?>>
    <?php if (!empty($icon)) : ?>
        <div class="callout-icon icon mb-single <?php echo !empty($iconCustomClass) ? esc_attr($iconCustomClass) : ''; ?>"<?php echo !empty($iconColor) ? ' style="background-color: ' . esc_attr($iconColor) . ';"' : ''; ?>>
            <i class="<?php echo esc_attr($icon); ?>"></i>
        </div>
    <?php elseif (!empty($iconImage)) : ?>
        <div class="callout-icon image mb-single <?php echo !empty($iconCustomClass) ? esc_attr($iconCustomClass) : ''; ?>">
            <img src="<?php echo esc_url($iconImage); ?>" height="100" width="100" alt="<?php echo esc_attr($title); ?>" />
        </div>
    <?php endif; ?>
    
    <?php if ($title) : ?>
        <p class="callout-title med-title mb-single <?php echo !empty($titleCustomClass) ? esc_attr($titleCustomClass) : ''; ?>"><?php echo esc_html($title); ?></p>
    <?php endif; ?>
    
    <?php if ($text) : ?>
        <div class="callout-text mb-single <?php echo !empty($textCustomClass) ? esc_attr($textCustomClass) : ''; ?>">
            <?php echo wpautop($text); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($buttonText)) :
        $button_bg = $has_buttonColor ? 'background-color: ' . esc_attr($buttonColor) . ';' : '';
        $button_color = $has_buttonTextColor ? ' color: ' . esc_attr($buttonTextColor) . ';' : '';
        $button_style = $has_buttonColor || $has_buttonTextColor ? ' style="' . $button_bg . $button_color . '"' : '';
    ?>
        <p class="callout-action">
            <a href="<?php echo esc_url($href); ?>" class="callout-button button button-arrow <?php echo esc_attr($button_classes); ?>"<?php echo $button_style; ?>><?php echo esc_html($buttonText); ?></a>
        </p>
    <?php endif; ?>
</div>