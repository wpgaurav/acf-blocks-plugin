# ACF Feature Grid Block — LLM Prompt

Create a feature grid to showcase features, services, or benefits with icons, titles, descriptions, and optional buttons.

## Block Info

- **Block Name:** `acf/feature-grid`
- **Description:** A grid layout to showcase features with icons, titles, descriptions, and buttons. Supports native blocks for header content.
- **Styles:** Default, Card (`is-style-card`), Dark (`is-style-dark`), Minimal (`is-style-minimal`), Bordered (`is-style-bordered`), Gradient Cards (`is-style-gradient-cards`)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_fg_use_innerblocks` | Use InnerBlocks | true_false | `"1"` for WP native blocks header |
| `acf_feature_grid_heading` | Heading | text | Grid section heading (when not using InnerBlocks) |
| `acf_feature_grid_subheading` | Subheading | textarea | Grid section subheading |
| `acf_feature_grid_items` | Feature Items | repeater | List of features |
| — `acf_feature_icon` | Icon | text | Icon class or emoji |
| — `acf_feature_image` | Image | image | Optional feature image |
| — `acf_feature_title` | Title | text | Required. Feature title |
| — `acf_feature_description` | Description | textarea | Feature description |
| — `acf_feature_link` | Link | link | Optional link for the feature card |
| — `acf_feature_button` | Button | link | Optional button for the feature |
| — `acf_feature_button_style` | Button Style | select | primary, secondary, text |
| `acf_feature_grid_columns` | Columns | select | 2, 3, or 4 |
| `acf_feature_grid_layout` | Layout | select | default or centered |
| `acf_fg_cta_button` | Section CTA Button | link | Optional CTA button for the whole section |
| `acf_fg_cta_style` | Section CTA Style | select | primary, secondary, large |
| `acf_feature_grid_class` | Custom Class | text | Optional CSS class |
| `acf_feature_grid_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_` — this block uses a different convention)
- Repeaters use nested `row-N` objects
- Link fields use ACF link format: `{"url":"...","title":"...","target":"..."}`
- Icon field accepts icon class names or emoji characters
- When `acf_fg_use_innerblocks` is enabled, heading/subheading come from InnerBlocks

## Instructions

1. Choose the number of columns (2, 3, or 4)
2. Select an appropriate style variation
3. Add a section heading and subheading
4. Create feature items with icons/images, titles, and descriptions
5. Add buttons to individual features if needed
6. Optionally add a section-level CTA button
7. Output the block as a WordPress block comment

## Example — 3-column card style

```html
<!-- wp:acf/feature-grid {"name":"acf/feature-grid","data":{"acf_fg_use_innerblocks":"0","acf_feature_grid_heading":"Why Choose Us","acf_feature_grid_subheading":"Everything you need to build and scale your online presence","acf_feature_grid_items":{"row-0":{"acf_feature_icon":"⚡","acf_feature_title":"Lightning Fast","acf_feature_description":"99.9% uptime with SSD storage and LiteSpeed caching. Your site loads in under 2 seconds.","acf_feature_button_style":"text"},"row-1":{"acf_feature_icon":"🔒","acf_feature_title":"Enterprise Security","acf_feature_description":"Free SSL, daily malware scanning, and real-time DDoS protection included with every plan.","acf_feature_button_style":"text"},"row-2":{"acf_feature_icon":"🎯","acf_feature_title":"24/7 Expert Support","acf_feature_description":"Our WordPress specialists are available around the clock via live chat, phone, and tickets.","acf_feature_button_style":"text"}},"acf_feature_grid_columns":"3","acf_feature_grid_layout":"centered"},"className":"is-style-card"} /-->
```

## Example — 4-column minimal with buttons

```html
<!-- wp:acf/feature-grid {"name":"acf/feature-grid","data":{"acf_fg_use_innerblocks":"0","acf_feature_grid_heading":"Our Services","acf_feature_grid_items":{"row-0":{"acf_feature_icon":"🎨","acf_feature_title":"Web Design","acf_feature_description":"Custom responsive designs that convert visitors into customers."},"row-1":{"acf_feature_icon":"💻","acf_feature_title":"Development","acf_feature_description":"Clean, performant code built with modern frameworks."},"row-2":{"acf_feature_icon":"📈","acf_feature_title":"SEO","acf_feature_description":"Data-driven optimization that drives organic traffic growth."},"row-3":{"acf_feature_icon":"📊","acf_feature_title":"Analytics","acf_feature_description":"Track, measure, and improve your digital performance."}},"acf_feature_grid_columns":"4","acf_feature_grid_layout":"default"},"className":"is-style-minimal"} /-->
```
