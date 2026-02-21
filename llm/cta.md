# ACF Call to Action Block — LLM Prompt

Create a call-to-action (CTA) block with heading, description, and button. This block supports InnerBlocks for modern WordPress usage, with fallback to ACF fields.

## Block Info

- **Block Name:** `acf/cta`
- **Description:** A customizable call-to-action block with heading, description, and button using core blocks.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_cta_heading` | Heading | text | Main CTA headline |
| `acf_cta_heading_tag` | Heading Tag | select | h1, h2, h3, h4, h5, h6, p, span |
| `acf_cta_description` | Description | wysiwyg | Supporting text/description |
| `acf_cta_button_text` | Button Text | text | CTA button label |
| `acf_cta_button_url` | Button URL | url | Where the button links |
| `acf_cta_button_style` | Button Style | select | primary, secondary, outline |
| `acf_cta_background_color` | Background Color | color_picker | Block background |
| `acf_cta_text_color` | Text Color | color_picker | Block text color |
| `acf_cta_class` | Custom Class | text | Optional CSS class |
| `acf_cta_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_` — this block uses a different convention)
- This block supports InnerBlocks for modern usage; ACF fields serve as legacy fallback
- Button style determines appearance: `primary` (filled), `secondary` (outline), `outline` (text-only)
- Color pickers are optional — omit for default theme colors

## Instructions

1. Write a compelling, action-oriented heading
2. Add a brief supporting description
3. Set button text and URL with a clear call to action
4. Choose appropriate button style and colors
5. Output the block as a WordPress block comment

## Example — Legacy ACF fields

```html
<!-- wp:acf/cta {"name":"acf/cta","data":{"acf_cta_heading":"Start Your Free Trial Today","acf_cta_heading_tag":"h2","acf_cta_description":"Join 10,000+ businesses already using our platform. No credit card required. Cancel anytime.","acf_cta_button_text":"Get Started Free","acf_cta_button_url":"https://example.com/signup","acf_cta_button_style":"primary","acf_cta_background_color":"#1e40af","acf_cta_text_color":"#ffffff"}} /-->
```

## Example — With InnerBlocks (modern)

```html
<!-- wp:acf/cta {"name":"acf/cta","data":{},"mode":"preview"} -->
<!-- wp:heading {"level":2} -->
<h2>Ready to Scale Your Business?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Our managed VPS hosting gives you the power and flexibility to grow without limits.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link" href="https://example.com/signup">Start Now</a></div>
<!-- /wp:button -->
<!-- /wp:buttons -->
<!-- /wp:acf/cta -->
```
