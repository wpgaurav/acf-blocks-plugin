# ACF Compare Block — LLM Prompt

Create a multi-column comparison block for comparing products, plans, or features side by side.

## Block Info

- **Block Name:** `acf/compare`
- **Description:** A customizable compare card block.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_comp_columns` | Number of Columns | number | How many items to compare |
| `field_comp_columns_data` | Column Data | repeater | One entry per column |
| — `field_comp_title` | Column Title | text | Product/plan name |
| — `field_comp_title_bg` | Title Background | color_picker | Header background color |
| — `field_comp_title_color` | Title Text Color | color_picker | Header text color |
| — `field_comp_text` | Description | textarea | Column description text |
| — `field_comp_repeater_list` | Feature List | repeater (nested) | List of features/specs |
| —— `field_comp_list_item` | List Item | text | Individual feature text |
| — `field_comp_list_class` | List CSS Class | text | Optional class for styling |
| — `field_comp_column_style` | Column Inline Style | text | Optional inline CSS |
| `field_comp_cta_text` | CTA Button Text | text | Shared call-to-action text |
| `field_comp_cta_url` | CTA Button URL | url | Shared CTA link |
| `field_comp_cta_url_rel_tag` | CTA Rel Attribute | text | e.g. "nofollow sponsored" |

## Field Rules

- All keys use `field_` prefix
- Two-level nested repeaters: `field_comp_columns_data` → `field_comp_repeater_list`
- Each column can have its own header color scheme
- CTA button is shared across all columns (appears at bottom)
- Use checkmarks (✓) and crosses (✗) in list items for feature comparison
- `field_comp_columns` should match the number of `row-N` entries

## Instructions

1. Set the number of columns (typically 2–4)
2. Fill in each column with title, description, and feature list
3. Use color to highlight the recommended/best option
4. Use ✓/✗ or descriptive text for feature comparisons
5. Add CTA button if linking to a purchase/signup page
6. Output the block as a WordPress block comment

## Example — 3-column plan comparison

```html
<!-- wp:acf/compare {"name":"acf/compare","data":{"field_comp_columns":"3","field_comp_columns_data":{"row-0":{"field_comp_title":"Basic","field_comp_title_bg":"#f3f4f6","field_comp_title_color":"#111827","field_comp_text":"For personal blogs and small sites","field_comp_repeater_list":{"row-0":{"field_comp_list_item":"1 Website"},"row-1":{"field_comp_list_item":"10 GB Storage"},"row-2":{"field_comp_list_item":"Free SSL"},"row-3":{"field_comp_list_item":"✗ CDN Included"},"row-4":{"field_comp_list_item":"✗ Priority Support"}},"field_comp_list_class":"","field_comp_column_style":""},"row-1":{"field_comp_title":"Pro","field_comp_title_bg":"#2563eb","field_comp_title_color":"#ffffff","field_comp_text":"Most popular for growing businesses","field_comp_repeater_list":{"row-0":{"field_comp_list_item":"Unlimited Websites"},"row-1":{"field_comp_list_item":"50 GB Storage"},"row-2":{"field_comp_list_item":"Free SSL"},"row-3":{"field_comp_list_item":"✓ CDN Included"},"row-4":{"field_comp_list_item":"✓ Priority Support"}},"field_comp_list_class":"","field_comp_column_style":""},"row-2":{"field_comp_title":"Enterprise","field_comp_title_bg":"#f3f4f6","field_comp_title_color":"#111827","field_comp_text":"For high-traffic sites and agencies","field_comp_repeater_list":{"row-0":{"field_comp_list_item":"Unlimited Websites"},"row-1":{"field_comp_list_item":"200 GB Storage"},"row-2":{"field_comp_list_item":"Free SSL + Wildcard"},"row-3":{"field_comp_list_item":"✓ CDN + WAF"},"row-4":{"field_comp_list_item":"✓ Dedicated Support"}},"field_comp_list_class":"","field_comp_column_style":""}},"field_comp_cta_text":"Get Started","field_comp_cta_url":"https://example.com/pricing","field_comp_cta_url_rel_tag":"nofollow"}} /-->
```
