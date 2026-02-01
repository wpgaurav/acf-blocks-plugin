# Hero Block — LLM Instructions

Use `<!-- wp:acf/hero -->` with a JSON `data` attribute. Fields use the `acf_hero_` prefix. This is an InnerBlocks block.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_hero_headline` | text | No | Default `"Your Headline Here"` |
| `acf_hero_subheadline` | text/HTML | No | WYSIWYG content (basic toolbar) |
| `acf_hero_image` | image (array) | No | WordPress media library image. Return format is `array` |
| `acf_hero_image_url` | URL string | No | Direct image URL. Alternative to media library image |
| `acf_hero_cta_text` | text | No | Default `"Get Started"`. Button text |
| `acf_hero_cta_url` | URL string | No | Button link URL |
| `acf_hero_cta_style` | select | No | `"primary"` (default), `"secondary"`, or `"outline"` |
| `acf_hero_class` | text | No | Custom CSS class |
| `acf_hero_inline` | text | No | Inline CSS styles |

## Notes

- Use `acf_hero_image` (attachment ID) OR `acf_hero_image_url` (direct URL), not both
- Supports InnerBlocks for additional content between the opening and closing tags

## Example

```html
<!-- wp:acf/hero {"name":"acf/hero","data":{"acf_hero_headline":"Build Faster WordPress Sites","acf_hero_subheadline":"<p>The all-in-one toolkit for developers who want performance without complexity.</p>","acf_hero_image_url":"https://example.com/images/hero-banner.jpg","acf_hero_cta_text":"Start Free Trial","acf_hero_cta_url":"https://example.com/signup","acf_hero_cta_style":"primary"}} -->
<!-- wp:paragraph -->
<p>Trusted by 10,000+ developers worldwide.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/hero -->
```
