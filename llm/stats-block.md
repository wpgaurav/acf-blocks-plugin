# Stats/Counter Block — LLM Instructions

Use `<!-- wp:acf/stats -->` with a JSON `data` attribute. Fields use the `acf_stats_` or `acf_stat_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_stats_items` | repeater object | Yes | Stat items |
| `acf_stats_layout` | select | No | `"horizontal"` (default), `"vertical"`, or `"grid"` |
| `acf_stats_enable_animation` | `"1"` or `"0"` | No | Default `"1"`. Animate counting up |
| `acf_stats_class` | text | No | Custom CSS class |
| `acf_stats_inline` | text | No | Inline CSS styles |

### Item Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_stat_number` | number string | Yes | The stat number |
| `acf_stat_label` | text | No | Label below the number |
| `acf_stat_prefix` | text | No | Prefix before number, e.g. `"$"` |
| `acf_stat_suffix` | text | No | Suffix after number, e.g. `"+"`, `"%"` |
| `acf_stat_icon` | text | No | Icon (emoji or text) |

## Repeater Format

```json
"acf_stats_items": {
  "row-0": {"acf_stat_number": "10000", "acf_stat_label": "Customers", "acf_stat_prefix": "", "acf_stat_suffix": "+", "acf_stat_icon": ""},
  "row-1": {"acf_stat_number": "99.9", "acf_stat_label": "Uptime", "acf_stat_prefix": "", "acf_stat_suffix": "%", "acf_stat_icon": ""},
  "row-2": {"acf_stat_number": "24", "acf_stat_label": "Support", "acf_stat_prefix": "", "acf_stat_suffix": "/7", "acf_stat_icon": ""}
}
```

## Example

```html
<!-- wp:acf/stats {"name":"acf/stats","data":{"acf_stats_items":{"row-0":{"acf_stat_number":"10000","acf_stat_label":"Happy Customers","acf_stat_prefix":"","acf_stat_suffix":"+","acf_stat_icon":""},"row-1":{"acf_stat_number":"99.9","acf_stat_label":"Uptime Guarantee","acf_stat_prefix":"","acf_stat_suffix":"%","acf_stat_icon":""},"row-2":{"acf_stat_number":"30","acf_stat_label":"Avg Response Time","acf_stat_prefix":"","acf_stat_suffix":"s","acf_stat_icon":""},"row-3":{"acf_stat_number":"50","acf_stat_label":"Data Centers","acf_stat_prefix":"","acf_stat_suffix":"+","acf_stat_icon":""}},"acf_stats_layout":"horizontal","acf_stats_enable_animation":"1"}} /-->
```
