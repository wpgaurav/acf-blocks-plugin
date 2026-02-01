# Call to Action Block — LLM Instructions

Use `<!-- wp:acf/cta -->` with a JSON `data` attribute. Fields use the `acf_cta_` prefix. This is an InnerBlocks block.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_cta_heading` | text | No | Default `"Ready to Get Started?"` |
| `acf_cta_description` | text/HTML | No | WYSIWYG content (basic toolbar). Default `"Take the next step..."` |
| `acf_cta_button_text` | text | No | Default `"Get Started"` |
| `acf_cta_button_url` | URL string | No | Button link URL |
| `acf_cta_button_style` | select | No | `"primary"` (default), `"secondary"`, or `"outline"` |
| `acf_cta_background_color` | color string | No | Background color, e.g. `"#1a1a2e"` |
| `acf_cta_text_color` | color string | No | Text color, e.g. `"#ffffff"` |
| `acf_cta_class` | text | No | Custom CSS class |
| `acf_cta_inline` | text | No | Inline CSS styles |

## Example

```html
<!-- wp:acf/cta {"name":"acf/cta","data":{"acf_cta_heading":"Start Your Free Trial","acf_cta_description":"<p>No credit card required. Get full access for 14 days.</p>","acf_cta_button_text":"Try It Free","acf_cta_button_url":"https://example.com/signup","acf_cta_button_style":"primary","acf_cta_background_color":"#1a1a2e","acf_cta_text_color":"#ffffff"}} -->
<!-- wp:paragraph -->
<p>Join thousands of happy customers already using our platform.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/cta -->
```
