# Star Rating Block — LLM Instructions

Use `<!-- wp:acf/star-rating -->` with a JSON `data` attribute. Fields use the `md_sr_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `md_sr_heading` | text | No | Heading above the rating widget |
| `md_sr_description` | text | No | Description text |
| `md_sr_button_label` | text | No | Default `"Submit Rating"` |
| `md_sr_thank_you` | text | No | Default `"Thanks for rating!"`. Success message |
| `md_sr_initial_count` | number string | No | Default `"0"`. Pre-existing rating count |
| `md_sr_initial_rating` | number string | No | Default `"0"`. `"0"` to `"5"`, step `0.1`. Pre-existing average rating |
| `md_sr_enable_schema` | `"1"` or `"0"` | No | Default `"1"`. Enable AggregateRating schema |
| `md_sr_schema_type` | select | No | Default `"CreativeWork"`. Only when schema enabled. See types below |
| `md_sr_schema_name` | text | No | Name for schema markup. Only when schema enabled |

### Schema Types

`CreativeWork`, `Article`, `BlogPosting`, `WebPage`, `HowTo`, `Recipe`, `SoftwareApplication`, `Product`

## Common Mistakes

1. **md_sr_initial_rating** — Step is `0.1`. Values like `4.3` or `4.7` are valid.
2. **md_sr_schema_type** — Must exactly match one of the 8 allowed values. Case-sensitive.

## Example

```html
<!-- wp:acf/star-rating {"name":"acf/star-rating","data":{"md_sr_heading":"Rate This Article","md_sr_description":"How helpful was this guide?","md_sr_button_label":"Submit Rating","md_sr_thank_you":"Thanks for your feedback!","md_sr_initial_count":"47","md_sr_initial_rating":"4.6","md_sr_enable_schema":"1","md_sr_schema_type":"Article","md_sr_schema_name":"Best Managed VPS Hosting Guide"}} /-->
```
