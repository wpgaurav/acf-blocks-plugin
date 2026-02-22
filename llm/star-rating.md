# ACF Star Rating Block — LLM Prompt

Create an interactive star rating widget that collects visitor ratings and displays the aggregate score. Supports Schema.org structured data.

## Block Info

- **Block Name:** `acf/star-rating`
- **Description:** Collects visitor star ratings and displays the aggregate score with CreativeWork schema.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_md_sr_heading` | Heading | text | Rating section heading |
| `field_md_sr_description` | Description | textarea | Supporting text below heading |
| `field_md_sr_button_label` | Button Label | text | Submit button text |
| `field_md_sr_thank_you` | Thank You Message | text | Message shown after rating submission |
| `field_md_sr_initial_count` | Initial Count | number | Starting number of ratings |
| `field_md_sr_initial_rating` | Initial Rating | number | Starting average rating (0–5) |
| `field_md_sr_enable_schema` | Enable Schema | true_false | `"1"` for Schema.org AggregateRating |
| `field_md_sr_schema_type` | Schema Type | select | CreativeWork, Article, BlogPosting, WebPage, HowTo, Recipe, SoftwareApplication, Product |
| `field_md_sr_schema_name` | Schema Name | text | Name used in schema markup |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Initial count and rating seed the aggregate display before any visitor ratings
- Rating submission persists in localStorage to prevent duplicate votes
- Schema type determines the JSON-LD `@type` value
- Schema name overrides the page title in structured data

## Instructions

1. Set a heading and description for the rating section
2. Configure the submit button label and thank you message
3. Set initial rating count and average (for social proof seeding)
4. Enable schema markup and choose the appropriate type
5. Set the schema name (product name, article title, etc.)
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/star-rating {"name":"acf/star-rating","data":{"field_md_sr_heading":"Rate This Article","field_md_sr_description":"How helpful was this guide? Your rating helps us improve.","field_md_sr_button_label":"Submit Rating","field_md_sr_thank_you":"Thanks for your rating!","field_md_sr_initial_count":"47","field_md_sr_initial_rating":"4.6","field_md_sr_enable_schema":"1","field_md_sr_schema_type":"Article","field_md_sr_schema_name":"Complete Guide to WordPress Hosting"}} /-->
```

## Example — Product rating

```html
<!-- wp:acf/star-rating {"name":"acf/star-rating","data":{"field_md_sr_heading":"Rate ScalaHosting","field_md_sr_description":"Share your experience with ScalaHosting's managed VPS hosting.","field_md_sr_button_label":"Rate Now","field_md_sr_thank_you":"Thank you for rating ScalaHosting!","field_md_sr_initial_count":"128","field_md_sr_initial_rating":"4.5","field_md_sr_enable_schema":"1","field_md_sr_schema_type":"Product","field_md_sr_schema_name":"ScalaHosting Managed VPS"}} /-->
```
