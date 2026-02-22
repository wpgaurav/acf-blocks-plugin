# ACF Pros & Cons Block — LLM Prompt

Create a two-column pros and cons comparison block with customizable colors and ordering.

## Block Info

- **Block Name:** `acf/pros-cons`
- **Description:** Minimalist two-column pros and cons comparison with border-bottom separators, flush columns with distinct background colors.
- **Styles:** None

## Design Notes

- Columns are flush (no gap) with `border-radius: 10px` and `overflow: hidden`
- Each column has a distinct background color (green-tinted for pros, red-tinted for cons)
- List items separated by `border-bottom` lines, not margin
- Titles are uppercase, `0.8125rem`, with letter-spacing
- Icons are 14px SVG checkmarks/crosses
- Font size is `0.875rem` (14px)
- Dark mode: separator borders use `rgba(255,255,255,0.08)`
- Block outputs `data-acf-block="pros-cons"` (used by TOC filtering)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pc_show_first` | Show First | select | `positive` or `negative` — which column appears first |
| `field_pc_pros_title` | Pros Title | text | Heading for pros column (default: "Pros") |
| `field_pc_pros_list` | Pros List | wysiwyg | HTML list of pros (use `<ul><li>` format) |
| `field_pc_cons_title` | Cons Title | text | Heading for cons column (default: "Cons") |
| `field_pc_cons_list` | Cons List | wysiwyg | HTML list of cons (use `<ul><li>` format) |
| `field_pc_pos_bg_color` | Pros Background | color_picker | Default: `#f0fdf4` |
| `field_pc_pos_border_color` | Pros Border | color_picker | Default: `#16a34a` |
| `field_pc_pos_title_color` | Pros Title Color | color_picker | Default: `#166534` |
| `field_pc_pos_icon_color` | Pros Icon Color | color_picker | Default: `#16a34a` |
| `field_pc_neg_bg_color` | Cons Background | color_picker | Default: `#fef2f2` |
| `field_pc_neg_border_color` | Cons Border | color_picker | Default: `#dc2626` |
| `field_pc_neg_title_color` | Cons Title Color | color_picker | Default: `#991b1b` |
| `field_pc_neg_icon_color` | Cons Icon Color | color_picker | Default: `#dc2626` |

## Field Rules

- All keys use `field_` prefix
- Pros/cons content uses WYSIWYG fields — write as HTML `<ul><li>` lists
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Color fields are all optional; defaults use green for pros and red for cons
- `field_pc_show_first` controls column ordering (which side appears on the left)
- Inline SVG icons (14px) are auto-injected for checkmarks (pros) and crosses (cons)

## Instructions

1. Write pros as an HTML unordered list
2. Write cons as an HTML unordered list
3. Optionally customize the column titles
4. Choose which column appears first (pros or cons)
5. Optionally set custom colors
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/pros-cons {"name":"acf/pros-cons","data":{"field_pc_show_first":"positive","field_pc_pros_title":"Pros","field_pc_pros_list":"<ul>\n<li>SPanel eliminates cPanel licensing fees</li>\n<li>SShield blocks 99.998% of attacks in real-time</li>\n<li>30-second average response time on support</li>\n<li>Free website migration by their team</li>\n<li>Managed VPS starting at $14.95/month</li>\n</ul>","field_pc_cons_title":"Cons","field_pc_cons_list":"<ul>\n<li>Limited data centers in Asia-Pacific</li>\n<li>No phone support available</li>\n<li>No LiteSpeed on lower-tier plans</li>\n</ul>"}} /-->
```

## Example — Custom colors, cons first

```html
<!-- wp:acf/pros-cons {"name":"acf/pros-cons","data":{"field_pc_show_first":"negative","field_pc_pros_title":"What We Like","field_pc_pros_list":"<ul>\n<li>Intuitive dashboard interface</li>\n<li>Excellent documentation</li>\n<li>Generous free tier</li>\n</ul>","field_pc_cons_title":"What Could Improve","field_pc_cons_list":"<ul>\n<li>Limited API rate limits on free plan</li>\n<li>No mobile app available yet</li>\n</ul>","field_pc_pos_bg_color":"#ecfdf5","field_pc_pos_border_color":"#10b981","field_pc_neg_bg_color":"#fef2f2","field_pc_neg_border_color":"#ef4444"}} /-->
```
