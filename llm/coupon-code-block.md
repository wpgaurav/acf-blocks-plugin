# Coupon Code Block — LLM Instructions

Use `<!-- wp:acf/cb-coupon-code -->` with a JSON `data` attribute. Fields use the `cb_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `cb_offer_details` | text | No | Description of the offer/discount |
| `cb_code` | text | No | The coupon code to display |
| `cb_copy_text` | text | No | Default `"Copy Coupon"`. Button text for copy action |
| `cb_activate_text` | text | No | Default `"Activate Discount"`. Button text for activation link |
| `cb_activate_url` | URL string | No | URL to activate the coupon |

## Notes

- All values must be strings
- If `cb_code` is provided, a copy-to-clipboard button is shown
- If `cb_activate_url` is provided, an activation link button is shown

## Example

```html
<!-- wp:acf/cb-coupon-code {"name":"acf/cb-coupon-code","data":{"cb_offer_details":"Get 50% off your first month of hosting","cb_code":"SAVE50","cb_copy_text":"Copy Coupon","cb_activate_text":"Activate Discount","cb_activate_url":"https://example.com/go/hosting/?coupon=SAVE50"}} /-->
```
