# ACF Logo Cloud Block — LLM Prompt

Create a row of customer or press logos.

## Block Info

- **Block Name:** `acf/logo-cloud`
- **Description:** Logos with an optional heading. A missing image falls back to the name as text.
- **Category:** ACF Marketing
- **Styles:** `is-style-grayscale` (default has no extra class)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_logo_heading` | Heading | text | Optional, for example “Trusted by” |
| `field_acf_logo_items` | Logos | repeater | See sub-fields |
| `field_acf_logo_new_tab` | New tab | true/false | `0` or `1` |

Logo sub-fields: `logo_image` (attachment ID), `logo_image_url` (used when there is no attachment), `logo_name` (alt text and text fallback), `logo_link`.

## Field Rules

- Field keys use the `field_` prefix.
- **Single-line JSON.**
- Repeaters use `row-N` objects.
- Every field is optional.

## Example

```html
<!-- wp:acf/logo-cloud {"name":"acf/logo-cloud","data":{"field_acf_logo_heading":"Trusted by","field_acf_logo_items":{"row-0":{"logo_name":"Northwind","logo_image_url":"https://example.com/northwind.svg"},"row-1":{"logo_name":"Contoso"}}},"className":"is-style-grayscale","mode":"preview"} /-->
```
