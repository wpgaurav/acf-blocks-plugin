# ACF Pricing Block — LLM Prompt

Create a pricing section with one or more plans.

## Block Info

- **Block Name:** `acf/pricing`
- **Description:** Plan cards with price, feature lines, and a signup button. One plan can be highlighted.
- **Category:** ACF Marketing
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_pricing_heading` | Heading | text | Optional |
| `field_acf_pricing_heading_tag` | Heading Tag | select | `h2` (default), `h3`, `h1`, `p` |
| `field_acf_pricing_intro` | Introduction | textarea | Optional |
| `field_acf_pricing_columns` | Columns | select | `2`, `3` (default), `4` |
| `field_acf_pricing_plans` | Plans | repeater | See sub-fields |
| `field_acf_pricing_new_tab` | New tab | true/false | `0` or `1`. Applies to every plan button |

Plan sub-fields: `plan_name`, `plan_badge`, `plan_price`, `plan_period`, `plan_text`, `plan_features` (one feature per line), `plan_button_text`, `plan_button_url`, `plan_highlight` (`"1"` to feature that plan).

## Field Rules

- Field keys use the `field_` prefix.
- **Single-line JSON.** No literal newlines inside the block comment. Use `\n` inside `plan_features`.
- Repeaters use `row-N` objects.
- Every field is optional. An empty plan button is omitted.

## Example

```html
<!-- wp:acf/pricing {"name":"acf/pricing","data":{"field_acf_pricing_heading":"Simple pricing","field_acf_pricing_columns":"3","field_acf_pricing_plans":{"row-0":{"plan_name":"Starter","plan_price":"$0","plan_period":"/month","plan_features":"1 project\nCommunity support","plan_button_text":"Create account","plan_button_url":"https://example.com/start"},"row-1":{"plan_name":"Pro","plan_badge":"Most popular","plan_price":"$29","plan_period":"/month","plan_features":"Unlimited projects\nPriority support","plan_button_text":"Start Pro","plan_button_url":"https://example.com/pro","plan_highlight":"1"}}},"mode":"preview"} /-->
```
