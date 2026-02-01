# Section Block — LLM Instructions

Use `<!-- wp:acf/section-block -->` with a JSON `data` attribute. Fields use the `acf_section_`, `acf_layout_`, `acf_spacing_`, `acf_bg_`, `acf_text_`, `acf_responsive_`, and `acf_custom_` prefixes. This is an InnerBlocks block — all content goes between the tags.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_section_html_tag` | select | No | Default `"section"`. One of: `div`, `section`, `article`, `aside`, `header`, `footer`, `main`, `custom` |
| `acf_section_custom_tag` | text | No | Custom HTML tag. Only when `html_tag` is `"custom"` |
| `acf_section_id` | text | No | Element ID (without `#`) |
| `acf_section_custom_class` | text | No | Custom CSS classes |
| `acf_layout_class` | text | No | Layout classes, e.g. `"container d-flex justify-content-between"` |
| `acf_spacing_class` | text | No | Spacing classes, e.g. `"py-5 mt-4"` |
| `acf_bg_class` | text | No | Background classes, e.g. `"bg-dark"` |
| `acf_text_class` | text | No | Text classes, e.g. `"text-center"` |
| `acf_responsive_class` | text | No | Responsive classes, e.g. `"d-none d-md-block"` |
| `acf_bg_color` | color string | No | Background color (supports opacity) |
| `acf_bg_image` | image (URL) | No | Background image. Return format is `url` |
| `acf_bg_overlay` | color string | No | Overlay color (supports opacity) |
| `acf_bg_video` | file (URL) | No | Background video (mp4). Return format is `url` |
| `acf_custom_css` | text | No | Custom CSS rules |

## Notes

- This is a layout wrapper — all content is InnerBlocks
- Class fields accept space-separated CSS class names (e.g. Bootstrap/utility classes)
- Background image, overlay, and video can be combined for layered effects

## Example

```html
<!-- wp:acf/section-block {"name":"acf/section-block","data":{"acf_section_html_tag":"section","acf_section_id":"features","acf_section_custom_class":"features-section","acf_spacing_class":"py-5","acf_text_class":"text-center","acf_bg_color":"#f8f9fa"}} -->
<!-- wp:heading -->
<h2>Our Features</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Everything you need to build great websites.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/section-block -->
```
