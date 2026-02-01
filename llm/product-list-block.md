# Product List Block — LLM Instructions

Use `<!-- wp:acf/pl-block -->` with a JSON `data` attribute. Fields use the `pl_block_` prefix. This block has **three parallel repeaters**.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pl_block_rank` | text | No | Rank/position indicator, e.g. `"#1"` |
| `pl_block_icon` | image (array) | No | Product icon/logo. Return format is `array` |
| `pl_block_product_name` | text | Yes | Default `"Product Name"` |
| `pl_block_description` | text/HTML | No | WYSIWYG content (basic toolbar) |
| `pl_block_pricing` | repeater object | No | Pricing tiers |
| `pl_block_coupons` | repeater object | No | Coupon codes |
| `pl_block_buttons` | repeater object | No | CTA buttons |

### Pricing Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pl_block_pricing_title` | text | Yes | e.g. `"Monthly"`, `"Annual"` |
| `pl_block_pricing_amount` | text | Yes | e.g. `"$14.95/mo"` |

### Coupon Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pl_block_coupon_code` | text | Yes | The coupon code |
| `pl_block_coupon_offer` | text | No | Offer description |

### Button Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pl_block_button_text` | text | Yes | Button label |
| `pl_block_button_url` | URL string | Yes | Button link |
| `pl_block_button_rel` | text | No | e.g. `"nofollow sponsored"` |
| `pl_block_button_class` | text | No | Custom CSS class |

## Repeater Format

```json
"pl_block_pricing": {
  "row-0": {"pl_block_pricing_title": "Monthly", "pl_block_pricing_amount": "$29.99/mo"},
  "row-1": {"pl_block_pricing_title": "Annual", "pl_block_pricing_amount": "$19.99/mo"}
},
"pl_block_coupons": {
  "row-0": {"pl_block_coupon_code": "SAVE20", "pl_block_coupon_offer": "20% off first year"}
},
"pl_block_buttons": {
  "row-0": {"pl_block_button_text": "Visit Site", "pl_block_button_url": "https://example.com", "pl_block_button_rel": "nofollow sponsored"}
}
```

## Example

```html
<!-- wp:acf/pl-block {"name":"acf/pl-block","data":{"pl_block_rank":"#1","pl_block_product_name":"ScalaHosting","pl_block_description":"<p>Managed VPS hosting with SPanel and SShield security.</p>","pl_block_pricing":{"row-0":{"pl_block_pricing_title":"Entry Cloud","pl_block_pricing_amount":"$14.95/mo"},"row-1":{"pl_block_pricing_title":"Business Cloud","pl_block_pricing_amount":"$29.95/mo"}},"pl_block_coupons":{"row-0":{"pl_block_coupon_code":"STARTER20","pl_block_coupon_offer":"20% off first invoice"}},"pl_block_buttons":{"row-0":{"pl_block_button_text":"Visit ScalaHosting","pl_block_button_url":"https://example.com/go/scalahosting/","pl_block_button_rel":"nofollow sponsored"}}}} /-->
```
