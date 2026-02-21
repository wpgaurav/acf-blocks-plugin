# ACF Coupon Code Block — LLM Prompt

Create a coupon code display block with copy-to-clipboard functionality and an activation button.

## Block Info

- **Block Name:** `acf/cb-coupon-code`
- **Description:** A coupon code block with offer details, copyable coupon code, and discount activation button.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_cb_offer_details` | Offer Details | text | Description of the offer/discount |
| `field_cb_code` | Coupon Code | text | The actual coupon code to copy |
| `field_cb_copy_text` | Copy Button Text | text | Text shown on copy button |
| `field_cb_activate_text` | Activate Button Text | text | Text for the activation link |
| `field_cb_activate_url` | Activate URL | url | Link to activate the coupon |

## Field Rules

- All keys use `field_` prefix
- Copy button triggers clipboard API with visual feedback
- Coupon code is displayed in a highlighted, copyable format
- Activate button links to the merchant page where the coupon applies

## Instructions

1. Write clear offer details describing the discount
2. Enter the exact coupon code
3. Set copy button text (e.g. "Copy Code")
4. Set activation button text and URL
5. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/cb-coupon-code {"name":"acf/cb-coupon-code","data":{"field_cb_offer_details":"Get 50% off your first 3 months of ScalaHosting managed VPS","field_cb_code":"STARTER50","field_cb_copy_text":"Copy Code","field_cb_activate_text":"Activate Discount","field_cb_activate_url":"https://gauravtiwari.org/go/scalahosting/"}} /-->
```

## Example — Free shipping coupon

```html
<!-- wp:acf/cb-coupon-code {"name":"acf/cb-coupon-code","data":{"field_cb_offer_details":"Free shipping on all orders over $50 — Limited time offer","field_cb_code":"SHIPFREE","field_cb_copy_text":"Copy","field_cb_activate_text":"Shop Now","field_cb_activate_url":"https://example.com/shop"}} /-->
```
