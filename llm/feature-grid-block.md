# Feature Grid Block — LLM Instructions

Use `<!-- wp:acf/feature-grid -->` with a JSON `data` attribute. Fields use the `acf_feature_grid_` or `acf_fg_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_fg_use_innerblocks` | `"1"` or `"0"` | No | Default `"0"`. When `"1"`, uses InnerBlocks instead of ACF fields for content |
| `acf_feature_grid_heading` | text | No | Grid section heading. Hidden when `use_innerblocks` is `"1"` |
| `acf_feature_grid_subheading` | text | No | Subheading text. Hidden when `use_innerblocks` is `"1"` |
| `acf_feature_grid_items` | repeater object | Yes | Feature items |
| `acf_feature_grid_columns` | select | No | `"2"`, `"3"` (default), or `"4"` |
| `acf_feature_grid_layout` | select | No | `"default"` or `"centered"` |
| `acf_fg_cta_button` | link object | No | `{"title":"","url":"","target":""}` |
| `acf_fg_cta_style` | select | No | `"primary"` (default), `"secondary"`, or `"large"` |
| `acf_feature_grid_class` | text | No | Custom CSS class |
| `acf_feature_grid_inline` | text | No | Inline CSS styles |

### Item Sub-fields

| Field Key | Type | Notes |
|---|---|---|
| `acf_feature_icon` | text | Icon (emoji or text) |
| `acf_feature_image` | image (array) | Image object. Return format is `array` |
| `acf_feature_title` | text | **Required**. Feature title |
| `acf_feature_description` | text | Feature description |
| `acf_feature_link` | link object | `{"title":"","url":"","target":""}` |
| `acf_feature_button` | link object | `{"title":"","url":"","target":""}` |
| `acf_feature_button_style` | select | `"primary"` (default), `"secondary"`, `"text"` |

## Repeater Format

```json
"acf_feature_grid_items": {
  "row-0": {
    "acf_feature_icon": "",
    "acf_feature_title": "Fast Performance",
    "acf_feature_description": "Optimized for speed with built-in caching.",
    "acf_feature_button_style": "primary"
  },
  "row-1": {
    "acf_feature_icon": "",
    "acf_feature_title": "Secure by Default",
    "acf_feature_description": "Enterprise-grade security included.",
    "acf_feature_button_style": "primary"
  }
}
```

## Example

```html
<!-- wp:acf/feature-grid {"name":"acf/feature-grid","data":{"acf_fg_use_innerblocks":"0","acf_feature_grid_heading":"Why Choose Us","acf_feature_grid_subheading":"Everything you need to succeed online","acf_feature_grid_items":{"row-0":{"acf_feature_icon":"","acf_feature_title":"Lightning Fast","acf_feature_description":"Optimized servers deliver sub-second load times.","acf_feature_button_style":"primary"},"row-1":{"acf_feature_icon":"","acf_feature_title":"99.9% Uptime","acf_feature_description":"Redundant infrastructure keeps your site online.","acf_feature_button_style":"primary"},"row-2":{"acf_feature_icon":"","acf_feature_title":"24/7 Support","acf_feature_description":"Expert help available around the clock.","acf_feature_button_style":"primary"}},"acf_feature_grid_columns":"3","acf_feature_grid_layout":"default"}} /-->
```
