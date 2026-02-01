# Testimonial Block — LLM Instructions

Use `<!-- wp:acf/testimonial -->` with a JSON `data` attribute. Fields use the `acf_testimonial_` prefix. This is an InnerBlocks block.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_testimonial_quote` | text/HTML | No | WYSIWYG content (basic toolbar). The testimonial quote |
| `acf_testimonial_author_name` | text | No | Default `"John Doe"` |
| `acf_testimonial_author_title` | text | No | Default `"CEO, Company Name"` |
| `acf_testimonial_author_image` | image (array) | No | Author photo from media library. Return format is `array` |
| `acf_testimonial_author_image_url` | URL string | No | Direct photo URL. Alternative to media library |
| `acf_testimonial_rating` | number string | No | `"1"` to `"5"`, **step `1` (whole numbers only)**. Star rating |
| `acf_testimonial_class` | text | No | Custom CSS class |
| `acf_testimonial_inline` | text | No | Inline CSS styles |

## Common Mistakes

1. **acf_testimonial_rating** — Step is `1`. Only whole numbers: `"1"`, `"2"`, `"3"`, `"4"`, `"5"`. No decimals.
2. **Author image** — Use `acf_testimonial_author_image` (attachment) OR `acf_testimonial_author_image_url` (URL), not both.

## Example

```html
<!-- wp:acf/testimonial {"name":"acf/testimonial","data":{"acf_testimonial_quote":"<p>ScalaHosting transformed our web infrastructure. The migration was seamless and our site loads twice as fast now. Their support team responds in under a minute.</p>","acf_testimonial_author_name":"Sarah Chen","acf_testimonial_author_title":"CTO, TechStartup Inc.","acf_testimonial_author_image_url":"https://example.com/images/sarah.jpg","acf_testimonial_rating":"5"}} -->
<!-- wp:paragraph -->
<p>We've been customers for over two years and couldn't be happier.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/testimonial -->
```
