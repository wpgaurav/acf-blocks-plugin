# ACF Hero Block — LLM Prompt

Create a hero section block with headline, subheadline, image, and call-to-action button. Supports InnerBlocks for modern WordPress editing.

## Block Info

- **Block Name:** `acf/hero`
- **Description:** A customizable hero block with image and core blocks for headline, subheadline, and CTA.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_hero_headline` | Headline | text | Main hero headline |
| `acf_hero_headline_tag` | Heading Tag | select | h1, h2, h3, h4, h5, h6, p, span |
| `acf_hero_subheadline` | Subheadline | wysiwyg | Supporting text below headline |
| `acf_hero_image` | Image | image (array) | Hero background/feature image |
| `acf_hero_image_url` | Image URL | url | Direct image URL (takes priority) |
| `acf_hero_cta_text` | CTA Button Text | text | Call-to-action button label |
| `acf_hero_cta_url` | CTA Button URL | url | Button link |
| `acf_hero_cta_style` | CTA Style | select | primary, secondary, outline |
| `acf_hero_class` | Custom Class | text | Optional CSS class |
| `acf_hero_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- This block supports InnerBlocks for modern usage; ACF fields serve as legacy fallback
- Image field supports both media library upload and direct URL
- Direct image URL takes priority over uploaded image

## Instructions

1. Write a compelling headline that grabs attention
2. Add supporting subheadline text
3. Include a hero image (upload or URL)
4. Set a clear call-to-action button
5. Choose the appropriate heading tag (h1 for landing pages, h2 for sections)
6. Output the block as a WordPress block comment

## Example — Legacy ACF fields

```html
<!-- wp:acf/hero {"name":"acf/hero","data":{"acf_hero_headline":"Build Faster WordPress Sites","acf_hero_headline_tag":"h1","acf_hero_subheadline":"Deploy in minutes with managed VPS hosting. SSD storage, free SSL, and 24/7 expert support included.","acf_hero_image_url":"https://example.com/hero-image.jpg","acf_hero_cta_text":"Start Free Trial","acf_hero_cta_url":"https://example.com/signup","acf_hero_cta_style":"primary"}} /-->
```

## Example — With InnerBlocks (modern)

```html
<!-- wp:acf/hero {"name":"acf/hero","data":{"acf_hero_image_url":"https://example.com/hero-image.jpg"},"mode":"preview"} -->
<!-- wp:heading {"level":1} -->
<h1>Build Faster WordPress Sites</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Deploy in minutes with managed VPS hosting. SSD storage, free SSL, and 24/7 expert support included.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link" href="https://example.com/signup">Start Free Trial</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:acf/hero -->
```
