# ACF Trust Bar Block — LLM Prompt

Create a short row of guarantees under an offer.

## Block Info

- **Block Name:** `acf/trust-bar`
- **Description:** Icon, title, and one line of detail for each promise.
- **Category:** ACF Marketing
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_trust_items` | Promises | repeater | See sub-fields |

Promise sub-fields: `trust_icon` (emoji or icon class), `trust_title`, `trust_text`.

## Field Rules

- Field keys use the `field_` prefix.
- **Single-line JSON.**
- Repeaters use `row-N` objects.
- Every field is optional. A row with no icon, title, or detail is skipped.

## Example

```html
<!-- wp:acf/trust-bar {"name":"acf/trust-bar","data":{"field_acf_trust_items":{"row-0":{"trust_icon":"↩","trust_title":"30-day returns","trust_text":"No questions asked."},"row-1":{"trust_icon":"🔒","trust_title":"Secure checkout","trust_text":"Encrypted payment."}}},"mode":"preview"} /-->
```
