# Pros & Cons Block — LLM Instructions

Use `<!-- wp:acf/pros-cons -->` with a JSON `data` attribute. Fields use the `pc_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pc_show_first` | select | No | `"negative"` (default) or `"positive"`. Which section shows first |
| `pc_cons_title` | text | No | Default `"Cons"` |
| `pc_cons_list` | text/HTML | Yes | WYSIWYG content (basic toolbar). Use `<ul><li>` lists |
| `pc_pros_title` | text | No | Default `"Pros"` |
| `pc_pros_list` | text/HTML | Yes | WYSIWYG content (basic toolbar). Use `<ul><li>` lists |
| `pc_neg_bg_color` | color string | No | Default `"#fef2f2"`. Cons background |
| `pc_neg_border_color` | color string | No | Default `"#dc2626"`. Cons border |
| `pc_neg_title_color` | color string | No | Default `"#dc2626"`. Cons title color |
| `pc_neg_icon_color` | color string | No | Default `"#dc2626"`. Cons icon color |
| `pc_pos_bg_color` | color string | No | Default `"#f0fdf4"`. Pros background |
| `pc_pos_border_color` | color string | No | Default `"#16a34a"`. Pros border |
| `pc_pos_title_color` | color string | No | Default `"#16a34a"`. Pros title color |
| `pc_pos_icon_color` | color string | No | Default `"#16a34a"`. Pros icon color |

## Notes

- Pros and cons content should use HTML list markup: `<ul><li>Item</li></ul>`
- No repeaters — content is entered as WYSIWYG HTML
- Color fields are optional; defaults provide a standard green/red theme

## Example

```html
<!-- wp:acf/pros-cons {"name":"acf/pros-cons","data":{"pc_show_first":"positive","pc_pros_title":"Pros","pc_pros_list":"<ul>\n<li>SPanel eliminates cPanel licensing fees</li>\n<li>SShield blocks 99.998% of attacks</li>\n<li>Managed VPS with dedicated resources</li>\n<li>Free website migration</li>\n</ul>","pc_cons_title":"Cons","pc_cons_list":"<ul>\n<li>Limited Asia-Pacific data centers</li>\n<li>No phone support</li>\n<li>CDN setup is manual</li>\n</ul>"}} /-->
```
