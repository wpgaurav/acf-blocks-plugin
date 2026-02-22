# ACF Section Block — LLM Prompt

Create a container/wrapper block that wraps inner WordPress blocks with customizable HTML tag, classes, background, and styling. Acts as a flexible layout container.

## Block Info

- **Block Name:** `acf/section-block`
- **Description:** A customizable container block that wraps inner blocks.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_section_html_tag` | HTML Tag | select | div, section, article, aside, header, footer, main, custom |
| `field_acf_section_custom_tag` | Custom Tag | text | Custom HTML tag (when "custom" selected) |
| `field_acf_section_id` | Section ID | text | HTML id attribute |
| `field_acf_section_custom_class` | Custom Class | text | Additional CSS classes |
| `field_acf_layout_class` | Layout Class | text | CSS layout utilities (flex, grid, etc.) |
| `field_acf_spacing_class` | Spacing Class | text | Padding/margin utility classes |
| `field_acf_bg_class` | Background Class | text | Background utility classes |
| `field_acf_text_class` | Text Class | text | Typography utility classes |
| `field_acf_responsive_class` | Responsive Class | text | Responsive/breakpoint classes |
| `field_acf_bg_color` | Background Color | color_picker | Solid background color |
| `field_acf_bg_image` | Background Image | image | Background image |
| `field_acf_bg_overlay` | Background Overlay | color_picker | Overlay color on top of bg image |
| `field_acf_bg_video` | Background Video | file | Background video file |
| `field_acf_custom_css` | Custom CSS | textarea | Scoped custom CSS rules |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- This block uses InnerBlocks — content goes inside as nested WordPress blocks
- Multiple class fields allow separation of concerns (layout, spacing, background, text, responsive)
- Background supports: solid color, image with overlay, or video
- Custom CSS is scoped to the block instance
- HTML tag field enables semantic markup (section, article, aside, etc.)
- Class fields accept space-separated CSS class names (Bootstrap/utility classes)

## Instructions

1. Choose the appropriate HTML tag for semantic meaning
2. Set an ID if the section needs an anchor link target
3. Add utility classes for layout, spacing, and styling
4. Set background (color, image, or video) if needed
5. Place inner WordPress blocks inside the section
6. Output the block as a WordPress block comment with inner content

## Example — Section with background color

```html
<!-- wp:acf/section-block {"name":"acf/section-block","data":{"field_acf_section_html_tag":"section","field_acf_section_id":"features","field_acf_section_custom_class":"features-section","field_acf_spacing_class":"py-16 px-4","field_acf_bg_color":"#f8fafc","field_acf_text_class":"text-center"},"mode":"preview"} -->
<!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="has-text-align-center">Our Features</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Everything you need to build amazing websites.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/section-block -->
```

## Example — Article wrapper with layout classes

```html
<!-- wp:acf/section-block {"name":"acf/section-block","data":{"field_acf_section_html_tag":"article","field_acf_layout_class":"max-w-3xl mx-auto","field_acf_spacing_class":"py-8 px-4"},"mode":"preview"} -->
<!-- wp:paragraph -->
<p>Article content goes here using standard WordPress blocks.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/section-block -->
```
