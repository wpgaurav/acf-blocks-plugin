# Table of Contents Block — LLM Instructions

Use `<!-- wp:acf/toc -->` with a JSON `data` attribute. Fields use the `toc_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `toc_title` | text | No | Default `"Table of Contents"` |
| `toc_title_tag` | select | No | Default `"p"`. One of: `p`, `h2`, `h3`, `h4`, `h5`, `h6`, `div`, `span` |
| `toc_heading_levels` | checkbox (array) | No | Default `["h2"]`. Array of heading levels to include: `h1`, `h2`, `h3`, `h4`, `h5`, `h6` |
| `toc_list_type` | select | No | Default `"ul"`. `"ol"` (ordered with hierarchy), `"ul"` (unordered with hierarchy), `"plain"` |
| `toc_collapsible` | `"1"` or `"0"` | No | Default `"0"`. Makes TOC collapsible |
| `toc_collapsed_default` | `"1"` or `"0"` | No | Default `"0"`. Start collapsed. Only when `collapsible` is `"1"` |
| `toc_sticky` | `"1"` or `"0"` | No | Default `"0"`. Sticky positioning |
| `toc_sticky_offset` | number string | No | Default `"20"`. Offset in px. Only when `sticky` is `"1"` |
| `toc_smooth_scroll` | `"1"` or `"0"` | No | Default `"1"`. Smooth scroll to headings |
| `toc_highlight_active` | `"1"` or `"0"` | No | Default `"0"`. Highlight current section |
| `toc_custom_class` | text | No | Custom class on wrapper |
| `toc_title_class` | text | No | Custom class on title |
| `toc_list_class` | text | No | Custom class on list |
| `toc_link_class` | text | No | Custom class on links |
| `toc_schema` | `"1"` or `"0"` | No | Default `"1"`. Enable SiteNavigationElement schema |
| `toc_aria_label` | text | No | Default `"Table of Contents"`. Accessibility label |

## Notes

- TOC is auto-generated from headings in the post content — no manual items needed
- `toc_heading_levels` is an array: `["h2","h3"]` to include both h2 and h3
- This block scans the page for headings at render time

## Example

```html
<!-- wp:acf/toc {"name":"acf/toc","data":{"toc_title":"Table of Contents","toc_title_tag":"p","toc_heading_levels":["h2","h3"],"toc_list_type":"ul","toc_collapsible":"1","toc_collapsed_default":"0","toc_smooth_scroll":"1","toc_highlight_active":"1","toc_schema":"1","toc_aria_label":"Table of Contents"}} /-->
```
