# ACF Pros & Cons Block — LLM Prompt

Create a two-column pros and cons comparison block with customizable colors and ordering.

## Block Info

- **Block Name:** `acf/pros-cons`
- **Description:** Display a two-column pros and cons comparison block.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pc_show_first` | Show First | select | `positive` or `negative` — which column appears first |
| `field_pc_pros_title` | Pros Title | text | Heading for pros column (default: "Pros") |
| `field_pc_pros_list` | Pros List | wysiwyg | HTML list of pros (use `<ul><li>` format) |
| `field_pc_cons_title` | Cons Title | text | Heading for cons column (default: "Cons") |
| `field_pc_cons_list` | Cons List | wysiwyg | HTML list of cons (use `<ul><li>` format) |
| `field_pc_pos_bg_color` | Pros Background | color_picker | Background color for pros section |
| `field_pc_pos_border_color` | Pros Border | color_picker | Border color for pros section |
| `field_pc_pos_title_color` | Pros Title Color | color_picker | Title text color for pros |
| `field_pc_pos_icon_color` | Pros Icon Color | color_picker | Checkmark icon color |
| `field_pc_neg_bg_color` | Cons Background | color_picker | Background color for cons section |
| `field_pc_neg_border_color` | Cons Border | color_picker | Border color for cons section |
| `field_pc_neg_title_color` | Cons Title Color | color_picker | Title text color for cons |
| `field_pc_neg_icon_color` | Cons Icon Color | color_picker | Cross icon color |

## Field Rules

- All keys use `field_` prefix
- Pros/cons content uses WYSIWYG fields — write as HTML `<ul><li>` lists
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Color fields are all optional; defaults use green for pros and red for cons
- `field_pc_show_first` controls column ordering (which side appears on the left)
- Inline SVG icons are used for checkmarks (pros) and crosses (cons)

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
