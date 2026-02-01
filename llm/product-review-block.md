# Product Review Block — LLM Instructions

Use `<!-- wp:acf/product-review -->` with a JSON `data` attribute. All values are keyed by ACF field keys (prefixed `field_pr_`). Follow these rules strictly.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `field_pr_product_name` | text | Yes | Product/service name |
| `field_pr_show_title` | `"1"` or `"0"` | No | Default `"1"`. Controls display only; name is always used in schema |
| `field_pr_product_image` | number (string) | No | WordPress attachment ID. Use this OR `field_pr_product_image_url`, not both |
| `field_pr_product_image_url` | URL string | No | Direct image URL. Takes priority over `field_pr_product_image` if both set |
| `field_pr_overall_rating` | number string | Yes | `"1"` to `"5"`, step `0.1`. Example: `"4.6"` |
| `field_pr_summary` | text/HTML | Yes | Plain text or basic HTML. Rendered via WYSIWYG |
| `field_pr_features` | repeater object | Yes | See repeater format below |
| `field_pr_pros` | repeater object | Yes | See repeater format below |
| `field_pr_cons` | repeater object | Yes | See repeater format below |
| `field_pr_offer_url` | URL string | No | Full URL including `https://` |
| `field_pr_offer_cta_text` | text | No | Default: `"Get Offer"` |
| `field_pr_link_rel` | text | No | Example: `"nofollow sponsored"` |
| `field_pr_link_target` | `"_blank"` or `"_self"` | No | Default: `"_blank"` |
| `field_pr_offer_price_currency` | select | No | One of: `USD`, `EUR`, `GBP`, `INR`, `CAD`, `AUD` |
| `field_pr_offer_price` | text | No | Numeric string, e.g. `"14.95"` |
| `field_pr_payment_term` | text | No | e.g. `"/month"`, `"/year"`, `"one-time"` |
| `field_pr_price_valid_until` | date string | No | **Must be `Y-m-d` format**: `"2026-12-31"`. NOT `"20261231"` |
| `field_pr_enable_json_ld` | `"1"` or `"0"` | No | Default `"1"`. Enables Product + Review schema |
| `field_pr_author_name` | text | No | Reviewer name for schema |
| `field_pr_date_modified` | date string | No | **Must be `Y-m-d` format**: `"2026-01-02"`. NOT `"20260102"` |
| `field_pr_brand` | text | No | Brand name for schema |
| `field_pr_sku` | text | No | Product SKU for schema |
| `field_pr_availability` | select | No | One of: `InStock`, `OutOfStock`, `PreOrder`, `Discontinued` |

## Repeater Format

Repeaters use `"row-0"`, `"row-1"`, etc. as keys. Zero-indexed, sequential, no gaps.

### Features

```json
"field_pr_features": {
  "row-0": {"field_pr_feature_name": "Performance", "field_pr_feature_rating": "4.5"},
  "row-1": {"field_pr_feature_name": "Support", "field_pr_feature_rating": "5"}
}
```

`field_pr_feature_rating`: `"1"` to `"5"`, **step `0.5` only** (valid: `1`, `1.5`, `2`, ... `5`). Do NOT use `0.25` increments like `4.25` or `4.75`.

### Pros

```json
"field_pr_pros": {
  "row-0": {"field_pr_pro_text": "Great performance."},
  "row-1": {"field_pr_pro_text": "Easy to use."}
}
```

### Cons

```json
"field_pr_cons": {
  "row-0": {"field_pr_con_text": "Limited integrations."}
}
```

## Common Mistakes to Avoid

1. **Date fields** — Always use `Y-m-d` (e.g. `"2026-01-02"`). Never use `Ymd` (`"20260102"`) or other formats.
2. **Feature ratings** — Step is `0.5`. Values like `4.25` or `4.75` are invalid. Round to nearest `0.5`.
3. **Overall rating** — Step is `0.1`. Values like `4.6` or `3.8` are fine here (different from feature ratings).
4. **All values must be strings** — Even numbers and booleans: `"4.5"` not `4.5`, `"1"` not `true`.
5. **Don't omit `field_pr_price_valid_until`** if schema is enabled — Google recommends it for Offer markup.
6. **Repeater rows must be sequential** — `row-0`, `row-1`, `row-2`. No skipping.

## Example

```html
<!-- wp:acf/product-review {"name":"acf/product-review","data":{"field_pr_product_name":"ScalaHosting","field_pr_show_title":"1","field_pr_product_image":"1057243","field_pr_overall_rating":"4.6","field_pr_summary":"ScalaHosting is the managed VPS sweet spot. Not as expensive as Kinsta or WPX, but way more capable than shared hosting. Their proprietary SPanel eliminates cPanel licensing fees, and SShield blocks 99.998% of attacks before they reach your site. I switched from WPX after three years and haven't looked back. Best fit for WordPress users who want VPS power without the sysadmin headaches.","field_pr_features":{"row-0":{"field_pr_feature_name":"Performance and Usability","field_pr_feature_rating":"4.5"},"row-1":{"field_pr_feature_name":"Website Management","field_pr_feature_rating":"4.5"},"row-2":{"field_pr_feature_name":"Security","field_pr_feature_rating":"5"},"row-3":{"field_pr_feature_name":"Pricing","field_pr_feature_rating":"4.5"},"row-4":{"field_pr_feature_name":"Value for Money","field_pr_feature_rating":"5"},"row-5":{"field_pr_feature_name":"Customer Support","field_pr_feature_rating":"5"}},"field_pr_pros":{"row-0":{"field_pr_pro_text":"SPanel replaces cPanel with zero licensing fees. Same functionality, lower cost."},"row-1":{"field_pr_pro_text":"SShield security blocks 99.998% of attacks in real-time."},"row-2":{"field_pr_pro_text":"30-second average response time on live chat support."},"row-3":{"field_pr_pro_text":"Free website migration handled by their team."},"row-4":{"field_pr_pro_text":"Managed VPS with dedicated resources starting at $14.95/month."},"row-5":{"field_pr_pro_text":"30-day money-back guarantee. Risk-free trial."}},"field_pr_cons":{"row-0":{"field_pr_con_text":"Limited data center presence in Asia-Pacific regions."},"row-1":{"field_pr_con_text":"No phone support. Live chat and tickets only."},"row-2":{"field_pr_con_text":"No LiteSpeed caching on lower-tier shared hosting plans."},"row-3":{"field_pr_con_text":"CDN configuration is manual. Cloudways handles this better out of the box."}},"field_pr_offer_url":"https://gauravtiwari.org/go/scalahosting/","field_pr_offer_cta_text":"Try ScalaHosting Risk-Free","field_pr_link_rel":"nofollow sponsored","field_pr_link_target":"_blank","field_pr_offer_price_currency":"USD","field_pr_offer_price":"14.95","field_pr_payment_term":"/month","field_pr_price_valid_until":"2026-12-31","field_pr_enable_json_ld":"1","field_pr_author_name":"Gaurav Tiwari","field_pr_date_modified":"2026-01-02","field_pr_brand":"ScalaHosting","field_pr_sku":"scalahosting-entry-cloud","field_pr_availability":"InStock"}} /-->
```
