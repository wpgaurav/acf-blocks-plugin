# ACF Opinion Box Block — LLM Prompt

Create an opinion/quote box block with author metadata. Uses InnerBlocks for the quote content with an author avatar, name, and designation.

## Block Info

- **Block Name:** `acf/opinion-box`
- **Description:** A custom opinion box block for sharing thoughts.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_ob_avatar` | Avatar | image (array) | Author photo from media library |
| `field_ob_avatar_url` | Avatar URL | url | Direct avatar URL (alternative) |
| `field_ob_name` | Name | text | Author/speaker name |
| `field_ob_designation` | Designation | text | Title, role, or credentials |
| `field_ob_citation` | Citation | text | Source or citation text |

## Field Rules

- All keys use `field_` prefix
- This block uses InnerBlocks for the quote/opinion content
- Avatar supports both media library upload and direct URL
- Designation is displayed below the name (e.g. "CEO, Company Name")
- Citation appears as a source reference

## Instructions

1. Write the opinion/quote as InnerBlocks content (paragraphs, lists, etc.)
2. Add the author's avatar, name, and designation
3. Include a citation if the opinion is from a published source
4. Output the block as a WordPress block comment with inner content

## Example

```html
<!-- wp:acf/opinion-box {"name":"acf/opinion-box","data":{"field_ob_avatar_url":"https://example.com/gaurav-avatar.jpg","field_ob_name":"Gaurav Tiwari","field_ob_designation":"Founder, GauravTiwari.org","field_ob_citation":"Based on 3 years of hands-on testing"},"mode":"preview"} -->
<!-- wp:paragraph -->
<p>After testing over 20 hosting providers, ScalaHosting consistently delivers the best balance of performance, security, and price. Their SPanel eliminates the need for expensive cPanel licenses, and the managed VPS setup means you don't need a sysadmin background to run a high-performance server.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/opinion-box -->
```

## Example — Short opinion

```html
<!-- wp:acf/opinion-box {"name":"acf/opinion-box","data":{"field_ob_name":"Jane Smith","field_ob_designation":"WordPress Developer"},"mode":"preview"} -->
<!-- wp:paragraph -->
<p>The best investment I made for my freelance business was switching from shared hosting to a managed VPS. The performance difference was immediately noticeable.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/opinion-box -->
```
