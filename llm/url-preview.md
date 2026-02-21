# ACF URL Preview Card Block — LLM Prompt

Create a rich URL preview card that displays fetched Open Graph data with optional custom fields, buttons, and multiple layout options.

## Block Info

- **Block Name:** `acf/url-preview`
- **Description:** Fetches Open Graph data from a URL and displays it as a product-like card with optional custom fields.
- **Styles:** None (uses card_style field)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_url_preview_source_url` | Source URL | url | The URL to preview |
| `field_url_preview_title` | Title | text | Override page title |
| `field_url_preview_description` | Description | textarea | Override page description |
| `field_url_preview_image_source` | Image Source | button_group | `external` or `local` |
| `field_url_preview_external_image` | External Image URL | url | Direct image URL (when source is external) |
| `field_url_preview_local_image` | Local Image | image | Media library image (when source is local) |
| `field_url_preview_local_image_size` | Image Size | select | WordPress image size (when local) |
| `field_url_preview_image_alt` | Image Alt Text | text | Alt attribute for image |
| `field_url_preview_custom_fields` | Custom Fields | repeater | Additional metadata fields |
| — `field_url_preview_field_label` | Label | text | Field label (e.g. "Price", "Author") |
| — `field_url_preview_field_value` | Value | text | Field value |
| — `field_url_preview_field_icon` | Icon | select | Icon for the field |
| `field_url_preview_show_button` | Show Button | true_false | `"1"` to display primary button |
| `field_url_preview_button_text` | Button Text | text | Primary button label |
| `field_url_preview_button_url` | Button URL | url | Primary button link |
| `field_url_preview_button_new_tab` | New Tab | true_false | `"1"` to open in new tab |
| `field_url_preview_button_rel` | Nofollow | true_false | `"1"` to add nofollow |
| `field_url_preview_show_secondary_button` | Show Secondary | true_false | `"1"` for secondary button |
| `field_url_preview_secondary_button_text` | Secondary Text | text | Secondary button label |
| `field_url_preview_secondary_button_url` | Secondary URL | url | Secondary button link |
| `field_url_preview_layout` | Layout | button_group | `vertical` or `horizontal` |
| `field_url_preview_card_style` | Card Style | select | default, compact, minimal, featured, dark |
| `field_url_preview_image_position` | Image Position | button_group | `left` or `right` (horizontal only) |
| `field_url_preview_custom_class` | Custom Class | text | Optional CSS class |
| `field_url_preview_custom_inline` | Inline Styles | text | Optional inline CSS |

## Card Styles

| Style | Description |
|---|---|
| `default` | Standard card with border |
| `compact` | Reduced padding/sizing |
| `minimal` | Clean, borderless design |
| `featured` | Emphasized/highlighted card |
| `dark` | Dark background theme |

## Field Rules

- All keys use `field_` prefix
- Repeaters use nested `row-N` objects for custom fields
- Image source toggle determines which image field is used (external URL vs media library)
- Layout determines card orientation (vertical = stacked, horizontal = side-by-side)
- Image position only applies in horizontal layout

## Instructions

1. Enter the source URL for the preview
2. Override title and description if the fetched data needs improvement
3. Choose image source (external URL or local upload)
4. Add custom fields for additional metadata (price, author, date, etc.)
5. Configure primary and optional secondary buttons
6. Choose layout and card style
7. Output the block as a WordPress block comment

## Example — Horizontal featured card

```html
<!-- wp:acf/url-preview {"name":"acf/url-preview","data":{"field_url_preview_source_url":"https://gauravtiwari.org/scalahosting-review/","field_url_preview_title":"ScalaHosting Review 2026: Managed VPS Hosting","field_url_preview_description":"In-depth review of ScalaHosting's managed VPS hosting with SPanel. Performance benchmarks, pricing, and comparison with competitors.","field_url_preview_image_source":"external","field_url_preview_external_image":"https://example.com/scalahosting-review-cover.jpg","field_url_preview_image_alt":"ScalaHosting Review","field_url_preview_custom_fields":{"row-0":{"field_url_preview_field_label":"Rating","field_url_preview_field_value":"4.6/5"},"row-1":{"field_url_preview_field_label":"Starting Price","field_url_preview_field_value":"$14.95/mo"}},"field_url_preview_show_button":"1","field_url_preview_button_text":"Read Full Review","field_url_preview_button_url":"https://gauravtiwari.org/scalahosting-review/","field_url_preview_button_new_tab":"1","field_url_preview_button_rel":"0","field_url_preview_layout":"horizontal","field_url_preview_card_style":"featured","field_url_preview_image_position":"left"}} /-->
```

## Example — Vertical compact card

```html
<!-- wp:acf/url-preview {"name":"acf/url-preview","data":{"field_url_preview_source_url":"https://example.com/product","field_url_preview_title":"Premium WordPress Theme","field_url_preview_description":"A responsive, SEO-optimized WordPress theme built for speed.","field_url_preview_image_source":"external","field_url_preview_external_image":"https://example.com/theme-preview.jpg","field_url_preview_show_button":"1","field_url_preview_button_text":"View Theme","field_url_preview_button_url":"https://example.com/product","field_url_preview_button_new_tab":"1","field_url_preview_button_rel":"1","field_url_preview_layout":"vertical","field_url_preview_card_style":"compact"}} /-->
```
