# ACF Product List Block — LLM Prompt

Create a product listing block with rank, icon, product name, description, pricing table, coupon codes, and action buttons. Ideal for "best of" lists and product roundups.

## Block Info

- **Block Name:** `acf/pl-block`
- **Description:** A product block with rank, icon, name, description, pricing, coupons, and offer buttons.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pl_block_rank` | Rank | text | Position number (e.g. "1", "#1") |
| `field_pl_block_icon` | Icon/Logo | image | Product logo or icon image |
| `field_pl_block_product_name` | Product Name | text | Required. Product/service name |
| `field_pl_block_description` | Description | wysiwyg | Product description with HTML support |
| `field_pl_block_pricing` | Pricing | repeater | Pricing tiers/plans |
| — `field_pl_block_pricing_title` | Plan Name | text | Required. e.g. "Starter", "Pro" |
| — `field_pl_block_pricing_amount` | Price | text | Required. e.g. "$9.99/mo" |
| `field_pl_block_coupons` | Coupons | repeater | Available coupon codes |
| — `field_pl_block_coupon_code` | Code | text | Required. Coupon code |
| — `field_pl_block_coupon_offer` | Offer Details | text | What the coupon provides |
| `field_pl_block_buttons` | Buttons | repeater | Action buttons |
| — `field_pl_block_button_text` | Button Text | text | Required. Button label |
| — `field_pl_block_button_url` | Button URL | url | Required. Button link |
| — `field_pl_block_button_rel` | Rel Attribute | text | e.g. "nofollow sponsored" |
| — `field_pl_block_button_class` | CSS Class | text | Optional button class |

## Field Rules

- All keys use `field_` prefix
- Repeaters use nested `row-N` objects
- Rank is typically a number but accepts any text
- Description supports WYSIWYG/HTML content
- Multiple pricing tiers can be listed for the same product
- Multiple coupons can be added per product

## Instructions

1. Set the product rank for list ordering
2. Add a product icon/logo (media library image)
3. Fill in the product name and description
4. Add pricing tiers with plan names and amounts
5. Add any available coupon codes with offer details
6. Create action buttons with proper affiliate links and rel attributes
7. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/pl-block {"name":"acf/pl-block","data":{"field_pl_block_rank":"1","field_pl_block_product_name":"ScalaHosting","field_pl_block_description":"<p>Managed VPS hosting with SPanel control panel. Best for WordPress users who want dedicated resources without the complexity of unmanaged servers.</p>","field_pl_block_pricing":{"row-0":{"field_pl_block_pricing_title":"Mini","field_pl_block_pricing_amount":"$14.95/mo"},"row-1":{"field_pl_block_pricing_title":"Start","field_pl_block_pricing_amount":"$26.95/mo"},"row-2":{"field_pl_block_pricing_title":"Advanced","field_pl_block_pricing_amount":"$62.95/mo"}},"field_pl_block_coupons":{"row-0":{"field_pl_block_coupon_code":"STARTER50","field_pl_block_coupon_offer":"50% off first 3 months"}},"field_pl_block_buttons":{"row-0":{"field_pl_block_button_text":"Visit ScalaHosting","field_pl_block_button_url":"https://gauravtiwari.org/go/scalahosting/","field_pl_block_button_rel":"nofollow sponsored","field_pl_block_button_class":""},"row-1":{"field_pl_block_button_text":"Read Full Review","field_pl_block_button_url":"https://gauravtiwari.org/scalahosting-review/","field_pl_block_button_rel":"","field_pl_block_button_class":""}}}} /-->
```
