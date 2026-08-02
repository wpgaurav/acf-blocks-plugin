# ACF Table of Contents Block — LLM Prompt

Create an SEO-optimized table of contents block that auto-generates from page headings. Supports sticky positioning, smooth scrolling, collapsible state, and Schema.org markup.

## Block Info

- **Block Name:** `acf/toc`
- **Description:** Theme-native table of contents with selectable list markers and optional schema markup.
- **Styles:** Default

## Design Notes

- Inherits typography, colors, link treatment, and disclosure styling from the active theme
- Adds a restrained square `currentColor` hairline frame, modest padding, and a divider below the title or collapsible summary
- Uses no background fill, hard-coded color, fixed font, custom hover fill, or decorative list marker
- Ordered and bulleted modes keep their native semantic list markers; plain mode removes markers
- Active-section highlighting uses a minimal relative font-weight change without assigning a font, size, or color
- Collapsible mode uses native `<details>/<summary>` behavior and the theme's disclosure treatment
- Sticky mode adds only the positioning and overflow rules needed for the feature
- No plugin-specific light or dark mode rules are applied
- Headings inside other ACF blocks (`.acf-block` or `data-acf-block` wrappers) are excluded from TOC by default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_toc_title` | Title | text | TOC heading (e.g. "Table of Contents") |
| `field_toc_title_tag` | Title Tag | select | p, h2, h3, h4, h5, h6, div, span |
| `field_toc_heading_levels` | Heading Levels | checkbox | Which heading levels to include: h1, h2, h3, h4, h5, h6 |
| `field_toc_include_acf_headings` | Include ACF Block Headings | true_false | When ON, headings inside ACF blocks are included. Default OFF. |
| `field_toc_list_type` | List Type | select | `ol` (numbered), `ul` (bulleted), `plain` (no markers) |
| `field_toc_collapsible` | Collapsible | true_false | `"1"` to allow expand/collapse |
| `field_toc_collapsed_default` | Start Collapsed | true_false | `"1"` to start in collapsed state |
| `field_toc_sticky` | Sticky | true_false | `"1"` for sticky positioning |
| `field_toc_sticky_offset` | Sticky Offset | number | Offset from top when sticky (in pixels) |
| `field_toc_smooth_scroll` | Smooth Scroll | true_false | `"1"` for smooth scrolling to headings |
| `field_toc_highlight_active` | Highlight Active | true_false | `"1"` to highlight current section |
| `field_toc_custom_class` | Custom Class | text | Optional CSS class for wrapper |
| `field_toc_title_class` | Title Class | text | Optional CSS class for title |
| `field_toc_list_class` | List Class | text | Optional CSS class for list |
| `field_toc_link_class` | Link Class | text | Optional CSS class for links |
| `field_toc_schema` | Enable Schema | true_false | `"1"` for JSON-LD structured data |
| `field_toc_aria_label` | ARIA Label | text | Accessibility label |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Heading levels field is a checkbox (multi-select) — pass as array or comma-separated
- TOC auto-generates from page headings at render time; no manual entries needed
- Collapsible state uses `<details>/<summary>` (native HTML, no JS)
- Schema generates SiteNavigationElement JSON-LD
- Sticky offset is in pixels (accounts for fixed headers)
- `field_toc_include_acf_headings`: default OFF — headings inside product boxes, pros/cons, compare blocks, etc. are excluded from TOC unless explicitly enabled

## Instructions

1. Set a title for the TOC (e.g. "Table of Contents", "In This Article")
2. Choose which heading levels to include (typically h2 and h3)
3. Select list type (numbered, bulleted, or plain)
4. Enable collapsible if the TOC is long
5. Enable smooth scroll for better UX
6. Enable schema for SEO benefits
7. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/toc {"name":"acf/toc","data":{"field_toc_title":"Table of Contents","field_toc_title_tag":"p","field_toc_heading_levels":["h2","h3"],"field_toc_list_type":"ol","field_toc_collapsible":"1","field_toc_collapsed_default":"0","field_toc_smooth_scroll":"1","field_toc_highlight_active":"1","field_toc_schema":"1","field_toc_aria_label":"Article table of contents"}} /-->
```

## Example — Minimal, non-collapsible

```html
<!-- wp:acf/toc {"name":"acf/toc","data":{"field_toc_title":"In This Guide","field_toc_title_tag":"h3","field_toc_heading_levels":["h2"],"field_toc_list_type":"plain","field_toc_collapsible":"0","field_toc_smooth_scroll":"1","field_toc_highlight_active":"0","field_toc_schema":"0"}} /-->
```

## Example — Sticky sidebar TOC

```html
<!-- wp:acf/toc {"name":"acf/toc","data":{"field_toc_title":"On This Page","field_toc_title_tag":"p","field_toc_heading_levels":["h2","h3","h4"],"field_toc_list_type":"ul","field_toc_collapsible":"0","field_toc_sticky":"1","field_toc_sticky_offset":"80","field_toc_smooth_scroll":"1","field_toc_highlight_active":"1","field_toc_schema":"1"}} /-->
```
