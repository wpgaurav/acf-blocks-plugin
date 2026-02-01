# Compare Block — LLM Instructions

Use `<!-- wp:acf/compare -->` with a JSON `data` attribute. Fields use the `comp_` prefix. This block has **nested repeaters**.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `comp_columns` | number string | No | Default `"2"`. Number of comparison columns |
| `comp_columns_data` | repeater object | Yes | Column data. Contains nested `comp_repeater_list` repeater |
| `comp_cta_text` | text | No | CTA button text |
| `comp_cta_url` | URL string | No | CTA button URL |
| `comp_cta_url_rel_tag` | text | No | Rel attribute, e.g. `"nofollow sponsored"` |

### Column Sub-fields

| Field Key | Type | Notes |
|---|---|---|
| `comp_title` | text | Column heading |
| `comp_title_bg` | color string | Header background color |
| `comp_title_color` | color string | Header text color |
| `comp_text` | text | Description text below header |
| `comp_repeater_list` | nested repeater | List items for this column |
| `comp_list_class` | text | Custom class for the list |
| `comp_column_style` | text | Inline CSS for the column |

### List Item Sub-fields (nested)

| Field Key | Type | Notes |
|---|---|---|
| `comp_list_item` | text | A single list item |

## Repeater Format (Nested)

```json
"comp_columns_data": {
  "row-0": {
    "comp_title": "Free Plan",
    "comp_title_bg": "#f0f0f0",
    "comp_title_color": "#333333",
    "comp_text": "Best for personal use",
    "comp_repeater_list": {
      "row-0": {"comp_list_item": "1 website"},
      "row-1": {"comp_list_item": "5GB storage"},
      "row-2": {"comp_list_item": "Community support"}
    }
  },
  "row-1": {
    "comp_title": "Pro Plan",
    "comp_title_bg": "#0073aa",
    "comp_title_color": "#ffffff",
    "comp_text": "Best for businesses",
    "comp_repeater_list": {
      "row-0": {"comp_list_item": "Unlimited websites"},
      "row-1": {"comp_list_item": "50GB storage"},
      "row-2": {"comp_list_item": "Priority support"}
    }
  }
}
```

## Example

```html
<!-- wp:acf/compare {"name":"acf/compare","data":{"comp_columns":"2","comp_columns_data":{"row-0":{"comp_title":"Basic","comp_title_bg":"#f0f0f0","comp_title_color":"#333333","comp_text":"For personal sites","comp_repeater_list":{"row-0":{"comp_list_item":"1 website"},"row-1":{"comp_list_item":"10GB storage"},"row-2":{"comp_list_item":"Email support"}}},"row-1":{"comp_title":"Pro","comp_title_bg":"#0073aa","comp_title_color":"#ffffff","comp_text":"For businesses","comp_repeater_list":{"row-0":{"comp_list_item":"Unlimited websites"},"row-1":{"comp_list_item":"100GB storage"},"row-2":{"comp_list_item":"Priority support"}}}},"comp_cta_text":"Compare All Plans","comp_cta_url":"https://example.com/pricing","comp_cta_url_rel_tag":"nofollow"}} /-->
```
