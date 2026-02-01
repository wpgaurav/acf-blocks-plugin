# Product Box Block — LLM Instructions

Use `<!-- wp:acf/product-box -->` with a JSON `data` attribute. Fields use the `pb_` prefix. This is an InnerBlocks block.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pb_image` | image (array) | No | Product image from media library. Return format is `array` |
| `pb_image_url` | URL string | No | Direct image URL. Alternative to media library |
| `pb_title` | text | No | Default `"Product Title"` |
| `pb_rating` | number string | No | `"0"` to `"5"`, step `0.5`. Default `"5"` |
| `pb_description` | text/HTML | No | WYSIWYG content (basic toolbar) |
| `pb_buttons` | repeater object | No | Min 0, max 4 CTA buttons |

### Button Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pb_cta_text` | text | Yes | Button label |
| `pb_cta_url` | URL string | Yes | Button link |
| `pb_cta_class` | text | No | Custom CSS class |
| `pb_cta_rel` | text | No | e.g. `"nofollow sponsored"` |

## Common Mistakes

1. **pb_rating** — Step is `0.5`. Valid: `0`, `0.5`, `1`, ..., `5`. Do NOT use `0.1` increments.
2. **pb_buttons** — Maximum 4 buttons allowed.

## Repeater Format

```json
"pb_buttons": {
  "row-0": {"pb_cta_text": "Buy Now", "pb_cta_url": "https://example.com/buy", "pb_cta_rel": "nofollow sponsored"},
  "row-1": {"pb_cta_text": "Read Review", "pb_cta_url": "https://example.com/review", "pb_cta_rel": ""}
}
```

## Example

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"pb_image_url":"https://example.com/images/product.jpg","pb_title":"ScalaHosting Managed VPS","pb_rating":"4.5","pb_description":"<p>Managed VPS hosting with SPanel, SShield security, and dedicated resources.</p>","pb_buttons":{"row-0":{"pb_cta_text":"Visit ScalaHosting","pb_cta_url":"https://example.com/go/scalahosting/","pb_cta_rel":"nofollow sponsored","pb_cta_class":""},"row-1":{"pb_cta_text":"Read Full Review","pb_cta_url":"https://example.com/scalahosting-review/","pb_cta_rel":"","pb_cta_class":""}}}} -->
<!-- wp:paragraph -->
<p>Starting at $14.95/month with free migration and 24/7 support.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/product-box -->
```
