# Accordion Block — LLM Instructions

Use `<!-- wp:acf/accordion -->` with a JSON `data` attribute. Fields use the `acf_accord_` or `acf_accordion_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_accord_enable_faq_schema` | `"1"` or `"0"` | No | Default `"0"`. Outputs FAQPage JSON-LD schema |
| `acf_accord_groups` | repeater object | Yes | Accordion items. See repeater format below |
| `acf_accordion_class` | text | No | Custom CSS class |
| `acf_accordion_inline` | text | No | Inline CSS styles |

## Repeater Format

```json
"acf_accord_groups": {
  "row-0": {
    "acf_accord_group_title": "Question or title text",
    "acf_accord_group_content": "<p>Answer or content HTML.</p>"
  },
  "row-1": {
    "acf_accord_group_title": "Another question",
    "acf_accord_group_content": "<p>Another answer.</p>"
  }
}
```

- `acf_accord_group_title`: Plain text or basic HTML
- `acf_accord_group_content`: HTML content (rendered via WYSIWYG). Wrap in `<p>` tags

## Notes

- Uses native `<details>`/`<summary>` HTML elements — no JavaScript required
- First item renders with `open` attribute by default
- Supports style variations via block className: `is-style-card`, `is-style-dark`, `is-style-minimal`, `is-style-bordered`
- When FAQ schema is enabled, all titles and content are included in FAQPage JSON-LD

## Example

```html
<!-- wp:acf/accordion {"name":"acf/accordion","data":{"acf_accord_enable_faq_schema":"1","acf_accord_groups":{"row-0":{"acf_accord_group_title":"What is this product?","acf_accord_group_content":"<p>A brief description of the product and its main features.</p>"},"row-1":{"acf_accord_group_title":"How much does it cost?","acf_accord_group_content":"<p>Pricing starts at $9.99/month with a free trial available.</p>"},"row-2":{"acf_accord_group_title":"Is there a refund policy?","acf_accord_group_content":"<p>Yes, 30-day money-back guarantee on all plans.</p>"}}}} /-->
```
