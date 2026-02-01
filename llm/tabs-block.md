# Tabs Block — LLM Instructions

Use `<!-- wp:acf/tabs -->` with a JSON `data` attribute. Fields use the `acf_tabs_` or `acf_tab_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_tabs_items` | repeater object | Yes | Min 1 tab required |
| `acf_tabs_style` | select | No | `"default"`, `"pills"`, `"underline"`, or `"boxed"` |
| `acf_tabs_class` | text | No | Custom CSS class |
| `acf_tabs_inline` | text | No | Inline CSS styles |

### Tab Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_tab_title` | text | Yes | Tab label |
| `acf_tab_icon` | text | No | Icon (emoji or text) |
| `acf_tab_content` | text/HTML | No | WYSIWYG content (full toolbar, media upload enabled) |

## Repeater Format

```json
"acf_tabs_items": {
  "row-0": {
    "acf_tab_title": "Features",
    "acf_tab_icon": "",
    "acf_tab_content": "<p>Feature details here.</p>"
  },
  "row-1": {
    "acf_tab_title": "Pricing",
    "acf_tab_icon": "",
    "acf_tab_content": "<p>Pricing details here.</p>"
  }
}
```

## Example

```html
<!-- wp:acf/tabs {"name":"acf/tabs","data":{"acf_tabs_items":{"row-0":{"acf_tab_title":"Overview","acf_tab_icon":"","acf_tab_content":"<p>ScalaHosting provides managed VPS hosting with their proprietary SPanel control panel. It offers dedicated resources, free migration, and enterprise-grade security.</p>"},"row-1":{"acf_tab_title":"Pricing","acf_tab_icon":"","acf_tab_content":"<p>Plans start at $14.95/month for Entry Cloud with 2 CPU cores and 4GB RAM. Business Cloud at $29.95/month includes 4 CPU cores and 8GB RAM.</p>"},"row-2":{"acf_tab_title":"Support","acf_tab_icon":"","acf_tab_content":"<p>24/7 live chat with 30-second average response time. Ticket support also available. No phone support.</p>"}},"acf_tabs_style":"default"}} /-->
```
