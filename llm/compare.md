# ACF Compare Block — LLM Prompt

Create a multi-column comparison block for comparing products, plans, or features side by side.

## Block Info

- **Block Name:** `acf/compare`
- **Description:** Minimalist side-by-side comparison with styled column cards, feature lists, and a shared CTA button.
- **Styles:** Default

## Design Notes

- Container has `border: 1px solid rgba(0,0,0,0.08)`, `border-radius: 12px`, `padding: 1.5rem`
- Columns have subtle `#fafafa` background, low-opacity borders, `border-radius: 10px`
- Feature list items separated by `border-bottom` with CSS `✓` checkmarks
- Column hover adds subtle shadow lift
- CTA button centered below all columns, defaults to brand red
- 2-4 columns with responsive breakpoints (stacks on mobile)
- Dark mode: full support via CSS custom properties
- Block outputs `data-acf-block="compare"` (used by TOC filtering)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_comp_columns` | Number of Columns | number | 2-4 columns |
| `field_comp_columns_data` | Columns | repeater | One entry per column |
| — `field_comp_title` | Title | text | Product/plan name |
| — `field_comp_title_bg` | Title Background | color_picker | Header background color |
| — `field_comp_title_color` | Title Text Color | color_picker | Header text color |
| — `field_comp_text` | Subtitle | text | Short description (e.g. "Best for performance") |
| — `field_comp_list_content` | Features List | wysiwyg | HTML list of features (use `<ul><li>` format) |
| — `field_comp_column_style` | Custom CSS | text | Optional inline CSS for this column |
| `field_comp_cta_text` | CTA Button Text | text | Shared call-to-action text |
| `field_comp_cta_url` | CTA Button URL | url | Shared CTA link |
| `field_comp_cta_url_rel_tag` | CTA Rel Attribute | text | e.g. "nofollow sponsored" |
| `field_comp_cta_bg` | CTA Button Color | color_picker | Background color for CTA button |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Feature lists use WYSIWYG field (`field_comp_list_content`) — write as HTML `<ul><li>` lists
- Each column can have its own header color scheme
- CTA button is shared across all columns (appears at bottom)
- `field_comp_columns` should match the number of `row-N` entries
- **Backward compat:** Old blocks using nested repeater (`field_comp_repeater_list`) still render correctly

## Instructions

1. Set the number of columns (typically 2–4)
2. Fill in each column with title, subtitle, and feature list (as WYSIWYG HTML)
3. Use color to highlight the recommended/best option
4. Add CTA button if linking to a purchase/signup page
5. Output the block as a WordPress block comment

## Example — 3-column product comparison

```html
<!-- wp:acf/compare {"name":"acf/compare","data":{"field_comp_columns":"3","field_comp_columns_data":{"row-0":{"field_comp_title":"GenerateBlocks","field_comp_title_bg":"#7c3aed","field_comp_title_color":"#ffffff","field_comp_text":"Best for performance","field_comp_list_content":"<ul>\n<li>Lightweight, block-native</li>\n<li>From $39/year</li>\n<li>Minimal DOM output</li>\n<li>Full site editing</li>\n<li>Global styles system</li>\n<li>Best for speed-focused sites</li>\n</ul>","field_comp_column_style":""},"row-1":{"field_comp_title":"Elementor","field_comp_title_bg":"#f3f4f6","field_comp_title_color":"#111827","field_comp_text":"Most popular overall","field_comp_list_content":"<ul>\n<li>Visual drag-and-drop</li>\n<li>From $59/year</li>\n<li>100+ widgets</li>\n<li>Theme builder + popups</li>\n<li>Massive template library</li>\n<li>Best for design-heavy sites</li>\n</ul>","field_comp_column_style":""},"row-2":{"field_comp_title":"Beaver Builder","field_comp_title_bg":"#f3f4f6","field_comp_title_color":"#111827","field_comp_text":"Best for agencies","field_comp_list_content":"<ul>\n<li>Clean, reliable output</li>\n<li>From $99/year</li>\n<li>White-label support</li>\n<li>Developer-friendly</li>\n<li>Stable update history</li>\n<li>Best for client projects</li>\n</ul>","field_comp_column_style":""}},"field_comp_cta_text":"Try GenerateBlocks","field_comp_cta_url":"https://example.com/go/generateblocks/","field_comp_cta_url_rel_tag":"nofollow sponsored","field_comp_cta_bg":"#b91c1c"}} /-->
```

## Example — 2-column plan comparison

```html
<!-- wp:acf/compare {"name":"acf/compare","data":{"field_comp_columns":"2","field_comp_columns_data":{"row-0":{"field_comp_title":"Free","field_comp_title_bg":"#f3f4f6","field_comp_title_color":"#111827","field_comp_text":"For personal projects","field_comp_list_content":"<ul>\n<li>1 Website</li>\n<li>10 GB Storage</li>\n<li>Free SSL</li>\n<li>Community support</li>\n</ul>","field_comp_column_style":""},"row-1":{"field_comp_title":"Pro","field_comp_title_bg":"#2563eb","field_comp_title_color":"#ffffff","field_comp_text":"For growing businesses","field_comp_list_content":"<ul>\n<li>Unlimited websites</li>\n<li>50 GB Storage</li>\n<li>Free SSL + CDN</li>\n<li>Priority support</li>\n</ul>","field_comp_column_style":""}},"field_comp_cta_text":"Get Started","field_comp_cta_url":"https://example.com/pricing","field_comp_cta_url_rel_tag":"nofollow"}} /-->
```
