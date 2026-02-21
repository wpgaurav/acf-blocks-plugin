# ACF Testimonial Block — LLM Prompt

Create a testimonial/quote block with author image, name, title, and optional star rating. Supports InnerBlocks for flexible quote content.

## Block Info

- **Block Name:** `acf/testimonial`
- **Description:** A customizable testimonial block with core blocks for quote content, plus author info and rating.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_testimonial_quote` | Quote | wysiwyg | Legacy quote text (InnerBlocks preferred) |
| `acf_testimonial_author_name` | Author Name | text | Person giving the testimonial |
| `acf_testimonial_author_title` | Author Title | text | Role, company, or credentials |
| `acf_testimonial_author_image` | Author Image | image (array) | Author photo from media library |
| `acf_testimonial_author_image_url` | Author Image URL | url | Direct photo URL (alternative) |
| `acf_testimonial_rating` | Rating | number | 1–5 star rating |
| `acf_testimonial_class` | Custom Class | text | Optional CSS class |
| `acf_testimonial_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- This block supports InnerBlocks — quote content goes as nested WordPress blocks
- ACF quote field serves as legacy fallback when InnerBlocks are empty
- Rating is optional; omit or set to 0 to hide stars
- Author image supports both media library and direct URL

## Instructions

1. Write the testimonial quote as InnerBlocks content (or use the legacy quote field)
2. Add the author's name and title/role
3. Include an author photo for credibility
4. Add a star rating if applicable
5. Output the block as a WordPress block comment

## Example — With InnerBlocks

```html
<!-- wp:acf/testimonial {"name":"acf/testimonial","data":{"acf_testimonial_author_name":"Sarah Johnson","acf_testimonial_author_title":"Marketing Director, TechCorp","acf_testimonial_author_image_url":"https://example.com/sarah-photo.jpg","acf_testimonial_rating":"5"},"mode":"preview"} -->
<!-- wp:paragraph -->
<p>Switching to ScalaHosting was the best decision we made this year. Our site speed improved by 40% and we haven't had a single minute of downtime. The support team is incredibly responsive.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/testimonial -->
```

## Example — Legacy field, no rating

```html
<!-- wp:acf/testimonial {"name":"acf/testimonial","data":{"acf_testimonial_quote":"This hosting platform has transformed how we manage our client sites. The control panel is intuitive and the performance is outstanding.","acf_testimonial_author_name":"Mike Chen","acf_testimonial_author_title":"Freelance Web Developer","acf_testimonial_author_image_url":"https://example.com/mike-photo.jpg"}} /-->
```
