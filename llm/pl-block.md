# ACF Product List Block — LLM Prompt

Create a ranked product listing block with icon, pricing tiers, coupon codes, and CTA buttons. Ideal for "best of" lists and product roundups.

## Block Info

- **Block Name:** `acf/pl-block`
- **Description:** Ranked product listing with icon, pricing, coupons, and action buttons.
- **Styles:** None

## Layout

Single-card design with four sections:

1. **Header row:** Rank badge (left) + Product icon/logo (center) + Product name (right, fills remaining space)
2. **Description:** Full-width product description with WYSIWYG formatting
3. **Info grid (2-column):** Pricing tiers (left) + Coupon codes (right) — stacks on mobile
4. **Buttons row:** CTA buttons with primary/secondary/text styles — separated by top border

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pl_block_rank` | Rank | text | Position label (e.g. "1", "#1", "Top Pick") |
| `field_pl_block_icon` | Icon/Logo | image (array) | Empty string `""` when using URL |
| `field_pl_block_image_url` | Image URL | url | Direct URL (takes priority over uploaded image) |
| `field_pl_block_image_width` | Image Width | text | CSS width for icon (e.g. "48px", "80px"). Default: 64px |
| `field_pl_block_product_name` | Product Name | text | Required. Product/service name |
| `field_pl_block_product_url` | Product URL | url | Optional. Links the product name |
| `field_pl_block_title_tag` | Title Heading Level | select | `p` (default), `h2`, `h3`, `h4`, `h5`, `h6` |
| `field_pl_block_description` | Description | wysiwyg | Product description with HTML support |
| `field_pl_block_pricing` | Pricing | repeater | Pricing tiers/plans |
| — `field_pl_block_pricing_title` | Plan Name | text | Required. e.g. "Starter", "Pro" |
| — `field_pl_block_pricing_amount` | Price | text | Required. e.g. "$9.99/mo" |
| `field_pl_block_coupons` | Coupons | repeater | Available coupon codes |
| — `field_pl_block_coupon_code` | Code | text | Required. Coupon code |
| — `field_pl_block_coupon_offer` | Offer Details | text | What the coupon provides |
| `field_pl_block_buttons` | Buttons | repeater | Action buttons (max 4) |
| — `field_pl_block_button_text` | Button Text | text | Required. Button label |
| — `field_pl_block_button_url` | Button URL | url | Required. Button link |
| — `field_pl_block_button_style` | Button Style | select | primary (filled), secondary (outline), text (link) |
| — `field_pl_block_button_rel` | Rel Attribute | text | e.g. "nofollow sponsored" |
| — `field_pl_block_button_class` | CSS Class | text | Optional button class |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Repeaters use nested `row-N` objects
- Rank is typically a number but accepts any text
- Description supports WYSIWYG/HTML content
- `field_pl_block_title_tag`: defaults to `"p"` if omitted. Set to `"h2"` through `"h6"` for heading tags
- `field_pl_block_image_url`: takes priority over uploaded icon. Use empty `""` for `field_pl_block_icon` when using URL
- `field_pl_block_button_style`: defaults to `"primary"` if omitted
- Block outputs `data-acf-block="pl-block"` attribute (used by TOC filtering)
- Optional fields can be omitted entirely

## Instructions

1. Set the product rank for list ordering
2. Add a product icon/logo (upload or URL)
3. Fill in the product name, optional URL, and heading level
4. Write a product description
5. Add pricing tiers with plan names and amounts
6. Add any available coupon codes with offer details
7. Create action buttons with proper affiliate links, styles, and rel attributes
8. Output the block as a WordPress block comment

## Example — Full product listing

```html
<!-- wp:acf/pl-block {"name":"acf/pl-block","data":{"field_pl_block_rank":"1","field_pl_block_image_url":"https://example.com/logo.png","field_pl_block_product_name":"ScalaHosting","field_pl_block_product_url":"https://gauravtiwari.org/go/scalahosting/","field_pl_block_title_tag":"h3","field_pl_block_description":"<p>Managed VPS hosting with SPanel control panel. Best for WordPress users who want dedicated resources without the complexity of unmanaged servers.</p>","field_pl_block_pricing":{"row-0":{"field_pl_block_pricing_title":"Mini","field_pl_block_pricing_amount":"$14.95/mo"},"row-1":{"field_pl_block_pricing_title":"Start","field_pl_block_pricing_amount":"$26.95/mo"},"row-2":{"field_pl_block_pricing_title":"Advanced","field_pl_block_pricing_amount":"$62.95/mo"}},"field_pl_block_coupons":{"row-0":{"field_pl_block_coupon_code":"STARTER50","field_pl_block_coupon_offer":"50% off first 3 months"}},"field_pl_block_buttons":{"row-0":{"field_pl_block_button_text":"Visit ScalaHosting","field_pl_block_button_url":"https://gauravtiwari.org/go/scalahosting/","field_pl_block_button_style":"primary","field_pl_block_button_rel":"nofollow sponsored","field_pl_block_button_class":""},"row-1":{"field_pl_block_button_text":"Read Full Review","field_pl_block_button_url":"https://gauravtiwari.org/scalahosting-review/","field_pl_block_button_style":"secondary","field_pl_block_button_rel":"","field_pl_block_button_class":""}}},"mode":"preview"} /-->
```

## Example — Minimal (no pricing/coupons)

```html
<!-- wp:acf/pl-block {"name":"acf/pl-block","data":{"field_pl_block_rank":"2","field_pl_block_product_name":"Cloudways","field_pl_block_description":"<p>Managed cloud hosting on DigitalOcean, Vultr, or AWS infrastructure.</p>","field_pl_block_buttons":{"row-0":{"field_pl_block_button_text":"Try Cloudways","field_pl_block_button_url":"https://gauravtiwari.org/go/cloudways/","field_pl_block_button_style":"primary","field_pl_block_button_rel":"nofollow sponsored"}}},"mode":"preview"} /-->
```
