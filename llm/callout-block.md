# Callout Block — LLM Instructions

Use `<!-- wp:acf/callout -->` with a JSON `data` attribute. Fields use the `callout_` prefix. This is an InnerBlocks block — content goes between the opening and closing tags.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `callout_label` | text | No | Label text displayed on the callout (e.g. "Note", "Warning", "Tip") |
| `callout_label_position` | select | No | `"top"` (default) or `"bottom"` |
| `callout_iconImage` | image (URL) | No | Icon/image URL. Return format is `url` |
| `callout_bgColor` | color string | No | Background color, e.g. `"#fff3cd"` |
| `callout_textColor` | color string | No | Text color, e.g. `"#856404"` |
| `callout_borderColor` | color string | No | Border color, e.g. `"#ffc107"` |

## Notes

- Content is placed via WordPress InnerBlocks (paragraphs, lists, etc. between the block tags)
- All values must be strings
- Color values should be hex strings including the `#`

## Example

```html
<!-- wp:acf/callout {"name":"acf/callout","data":{"callout_label":"Important","callout_label_position":"top","callout_bgColor":"#fff3cd","callout_textColor":"#856404","callout_borderColor":"#ffc107"}} -->
<!-- wp:paragraph -->
<p>This is an important note that readers should pay attention to.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/callout -->
```
