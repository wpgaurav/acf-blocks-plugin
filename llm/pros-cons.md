# ACF Pros & Cons Block — LLM Prompt

Create a two-column pros and cons comparison block with customizable colors and ordering.

## Block Info

- **Block Name:** `acf/pros-cons`
- **Description:** Minimalist two-column pros and cons comparison with border-bottom separators, flush columns with distinct background colors.
- **Styles:** None

## Design Notes

- Columns are flush (no gap) with `border-radius: 10px` and `overflow: hidden`
- Each column has a distinct background color (success-tinted for pros, danger-tinted for cons), mixed from the theme's tokens so it follows light and dark mode
- List items separated by `border-bottom` lines, not margin
- Titles are uppercase, `0.8125rem`, with letter-spacing
- Icons are 14px SVG checkmarks/crosses
- Font size is `0.875rem` (14px)
- Dark mode: with no custom colors set, backgrounds, titles and icons come from theme tokens and switch with the theme. A custom color is used as-is in both modes, so a light custom background stays light in dark mode
- Block outputs `data-acf-block="pros-cons"` (used by TOC filtering)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pc_show_first` | Show First | select | `positive` or `negative` — which column appears first |
| `field_pc_pros_title` | Pros Title | text | Heading for pros column (default: "Pros") |
| `field_pc_pros_list` | Pros List | wysiwyg | HTML list of pros (use `<ul><li>` format) |
| `field_pc_cons_title` | Cons Title | text | Heading for cons column (default: "Cons") |
| `field_pc_cons_list` | Cons List | wysiwyg | HTML list of cons (use `<ul><li>` format) |
| `field_pc_pos_bg_color` | Pros Background | color_picker | Empty by default (theme token) |
| `field_pc_pos_border_color` | Pros Border | color_picker | Empty by default (theme token) |
| `field_pc_pos_title_color` | Pros Title Color | color_picker | Empty by default (theme token) |
| `field_pc_pos_icon_color` | Pros Icon Color | color_picker | Empty by default (theme token) |
| `field_pc_neg_bg_color` | Cons Background | color_picker | Empty by default (theme token) |
| `field_pc_neg_border_color` | Cons Border | color_picker | Empty by default (theme token) |
| `field_pc_neg_title_color` | Cons Title Color | color_picker | Empty by default (theme token) |
| `field_pc_neg_icon_color` | Cons Icon Color | color_picker | Empty by default (theme token) |

## Field Rules

- All keys use `field_` prefix
- Pros/cons content uses WYSIWYG fields — write as HTML `<ul><li>` lists
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Color fields are all optional. Leave them empty unless the user asks for brand colors: empty fields follow the theme, including dark mode
- The pre-2.12.1 defaults (`#f0fdf4`, `#16a34a`, `#166534`, `#fef2f2`, `#dc2626`, `#991b1b`) are treated as empty, so blocks saved with them also follow the theme
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
<!-- wp:acf/pros-cons {"name":"acf/pros-cons","data":{"field_pc_show_first":"negative","field_pc_pros_title":"What We Like","field_pc_pros_list":"<ul>\n<li>Intuitive dashboard interface</li>\n<li>Excellent documentation</li>\n<li>Generous free tier</li>\n</ul>","field_pc_cons_title":"What Could Improve","field_pc_cons_list":"<ul>\n<li>Limited API rate limits on free plan</li>\n<li>No mobile app available yet</li>\n</ul>","field_pc_pos_bg_color":"#ecfdf5","field_pc_pos_border_color":"#10b981","field_pc_neg_bg_color":"#fff1f2","field_pc_neg_border_color":"#ef4444"}} /-->
```
