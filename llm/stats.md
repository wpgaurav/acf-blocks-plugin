# ACF Stats/Counter Block — LLM Prompt

Create a statistics counter block with animated numbers to showcase metrics and achievements.

## Block Info

- **Block Name:** `acf/stats`
- **Description:** A stats block with animated counters to showcase numbers and achievements.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_stats_items` | Stats Items | repeater | List of stat counters |
| — `acf_stat_number` | Number | number | Required. The statistic value |
| — `acf_stat_label` | Label | text | Description below the number |
| — `acf_stat_prefix` | Prefix | text | Text before number (e.g. "$", "#") |
| — `acf_stat_suffix` | Suffix | text | Text after number (e.g. "%", "+", "k") |
| — `acf_stat_icon` | Icon | text | Icon class or emoji |
| `acf_stats_layout` | Layout | select | `horizontal`, `vertical`, `grid` |
| `acf_stats_enable_animation` | Animate | true_false | `"1"` for count-up animation |
| `acf_stats_class` | Custom Class | text | Optional CSS class |
| `acf_stats_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Repeaters use nested `row-N` objects
- Animation triggers when the block scrolls into view
- Prefix/suffix combine with number: `$` + `10` + `M` → `$10M`
- Layout options: `horizontal` (side by side), `vertical` (stacked), `grid` (responsive grid)

## Instructions

1. Add stat items with numbers, labels, and optional prefixes/suffixes
2. Use icons or emojis for visual enhancement
3. Choose a layout that fits the page design
4. Enable animation for engaging count-up effect
5. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/stats {"name":"acf/stats","data":{"acf_stats_items":{"row-0":{"acf_stat_number":"10000","acf_stat_label":"Happy Customers","acf_stat_prefix":"","acf_stat_suffix":"+","acf_stat_icon":"😊"},"row-1":{"acf_stat_number":"99.9","acf_stat_label":"Uptime Guarantee","acf_stat_prefix":"","acf_stat_suffix":"%","acf_stat_icon":"⚡"},"row-2":{"acf_stat_number":"24","acf_stat_label":"Support Available","acf_stat_prefix":"","acf_stat_suffix":"/7","acf_stat_icon":"🎯"},"row-3":{"acf_stat_number":"50","acf_stat_label":"Data Centers","acf_stat_prefix":"","acf_stat_suffix":"+","acf_stat_icon":"🌍"}},"acf_stats_layout":"horizontal","acf_stats_enable_animation":"1"}} /-->
```

## Example — Revenue stats with prefixes

```html
<!-- wp:acf/stats {"name":"acf/stats","data":{"acf_stats_items":{"row-0":{"acf_stat_number":"2.5","acf_stat_label":"Annual Revenue","acf_stat_prefix":"$","acf_stat_suffix":"M","acf_stat_icon":""},"row-1":{"acf_stat_number":"150","acf_stat_label":"Team Members","acf_stat_prefix":"","acf_stat_suffix":"","acf_stat_icon":""},"row-2":{"acf_stat_number":"35","acf_stat_label":"Countries Served","acf_stat_prefix":"","acf_stat_suffix":"+","acf_stat_icon":""}},"acf_stats_layout":"grid","acf_stats_enable_animation":"1"}} /-->
```
