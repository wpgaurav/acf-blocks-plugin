# ACF Product Review Block — LLM Prompt

Create a product review block in ACF block comment markup for the given product.

## Block Info

- **Block Name:** `acf/product-review`
- **Description:** A product review block with structured schema data for Google rich results.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pr_product_name` | Product Name | text | Required |
| `field_pr_show_title` | Show Title | true_false | `"1"` to display title |
| `field_pr_product_image` | Product Image | image (ID) | Media library image ID |
| `field_pr_product_image_url` | Image URL | url | Direct URL (takes priority over uploaded image) |
| `field_pr_overall_rating` | Overall Rating | number | 1–5, step 0.1 |
| `field_pr_summary` | Summary | wysiwyg | Product review summary text |
| `field_pr_features` | Features | repeater | Feature ratings list |
| — `field_pr_feature_name` | Feature Name | text | Sub-field of features |
| — `field_pr_feature_rating` | Feature Rating | number | 1–5, step 0.5 |
| `field_pr_pros` | Pros | repeater | List of pros |
| — `field_pr_pro_text` | Pro Text | text | Sub-field of pros |
| `field_pr_cons` | Cons | repeater | List of cons |
| — `field_pr_con_text` | Con Text | text | Sub-field of cons |
| `field_pr_offer_url` | Offer URL | url | Link to purchase |
| `field_pr_offer_cta_text` | CTA Button Text | text | Default: "Get Offer" |
| `field_pr_link_rel` | Link Rel | text | e.g. "nofollow sponsored" |
| `field_pr_link_target` | Link Target | select | `_blank` or `_self` |
| `field_pr_offer_price_currency` | Currency | select | USD, EUR, GBP, INR, CAD, AUD |
| `field_pr_offer_price` | Price | text | Numeric price value |
| `field_pr_payment_term` | Payment Term | text | e.g. "/month", "/year" |
| `field_pr_price_valid_until` | Price Valid Until | date_picker | Format: YYYYMMDD |
| `field_pr_enable_json_ld` | Enable Schema | true_false | `"1"` to enable JSON-LD |
| `field_pr_author_name` | Reviewer Name | text | Person reviewing the product |
| `field_pr_date_modified` | Review Last Updated | date_picker | Format: YYYYMMDD |
| `field_pr_brand` | Brand | text | Schema.org Brand name |
| `field_pr_sku` | SKU | text | Product SKU identifier |
| `field_pr_availability` | Availability | select | InStock, OutOfStock, PreOrder, Discontinued |

## Field Rules

- All field keys use the `field_` prefix (e.g. `field_pr_product_name`, NOT `pr_product_name`)
- Repeaters use nested `row-N` objects: `{"row-0":{"field_pr_feature_name":"...","field_pr_feature_rating":"..."}}`
- Ratings support decimals: overall rating step 0.1, feature rating step 0.5
- Image field stores WordPress media library ID; image URL field stores direct URL
- Date fields use YYYYMMDD format in the block comment data

## Instructions

1. Fill in the product name, overall rating, and summary
2. Add 4–6 feature ratings covering key aspects of the product
3. Add 4–6 pros and 3–4 cons with specific, actionable details
4. Include offer URL with affiliate link, CTA text, and pricing info
5. Enable JSON-LD schema and fill in brand, SKU, author, and availability
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/product-review {"name":"acf/product-review","data":{"field_pr_product_name":"ScalaHosting","field_pr_show_title":"1","field_pr_product_image":"1057243","field_pr_overall_rating":"4.6","field_pr_summary":"ScalaHosting is the managed VPS sweet spot. Not as expensive as Kinsta or WPX, but way more capable than shared hosting. Their proprietary SPanel eliminates cPanel licensing fees, and SShield blocks 99.998% of attacks before they reach your site. I switched from WPX after three years and haven't looked back. Best fit for WordPress users who want VPS power without the sysadmin headaches.","field_pr_features":{"row-0":{"field_pr_feature_name":"Performance and Usability","field_pr_feature_rating":"4.25"},"row-1":{"field_pr_feature_name":"Website Management","field_pr_feature_rating":"4.5"},"row-2":{"field_pr_feature_name":"Security","field_pr_feature_rating":"4.75"},"row-3":{"field_pr_feature_name":"Pricing","field_pr_feature_rating":"4.5"},"row-4":{"field_pr_feature_name":"Value for Money","field_pr_feature_rating":"4.75"},"row-5":{"field_pr_feature_name":"Customer Support","field_pr_feature_rating":"5"}},"field_pr_pros":{"row-0":{"field_pr_pro_text":"SPanel replaces cPanel with zero licensing fees. Same functionality, lower cost."},"row-1":{"field_pr_pro_text":"SShield security blocks 99.998% of attacks in real-time."},"row-2":{"field_pr_pro_text":"30-second average response time on live chat support."},"row-3":{"field_pr_pro_text":"Free website migration handled by their team."},"row-4":{"field_pr_pro_text":"Managed VPS with dedicated resources starting at $14.95/month."},"row-5":{"field_pr_pro_text":"30-day money-back guarantee. Risk-free trial."}},"field_pr_cons":{"row-0":{"field_pr_con_text":"Limited data center presence in Asia-Pacific regions."},"row-1":{"field_pr_con_text":"No phone support. Live chat and tickets only."},"row-2":{"field_pr_con_text":"No LiteSpeed caching on lower-tier shared hosting plans."},"row-3":{"field_pr_con_text":"CDN configuration is manual. Cloudways handles this better out of the box."}},"field_pr_offer_url":"https://gauravtiwari.org/go/scalahosting/","field_pr_offer_cta_text":"Try ScalaHosting Risk-Free","field_pr_link_rel":"nofollow sponsored","field_pr_link_target":"_blank","field_pr_offer_price_currency":"USD","field_pr_offer_price":"14.95","field_pr_payment_term":"/month","field_pr_price_valid_until":"","field_pr_enable_json_ld":"1","field_pr_author_name":"Gaurav Tiwari","field_pr_date_modified":"20260102","field_pr_brand":"ScalaHosting","field_pr_sku":"scalahosting-entry-cloud","field_pr_availability":"InStock"}} /-->
```
