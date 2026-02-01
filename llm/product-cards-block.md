# Product Cards Block — LLM Instructions

Use `<!-- wp:acf/product_cards -->` (note the underscore) with a JSON `data` attribute. Fields use the `pc_block_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pc_block_title` | text | No | Default `"Product Name"` |
| `pc_block_title_color` | color string | No | Default `"#FFFFFF"`. Title text color |
| `pc_block_title_bg_color` | color string | No | Default `"#007bff"`. Title background color |
| `pc_block_product_image` | image (URL) | No | Product image. Return format is `url` |
| `pc_block_description` | text | No | Default `"This product is cool..."`. Plain text (textarea) |
| `pc_block_root_class` | text | No | Custom CSS class for the card |
| `pc_block_button_text` | text | No | Default `"Learn more"`. Primary button text |
| `pc_block_button_url` | URL string | No | Primary button link |
| `pc_block_button_rel` | text | No | Rel attribute for primary button |
| `pc_block_text_link` | text | No | Secondary text link label |
| `pc_block_text_link_url` | URL string | No | Secondary text link URL |
| `pc_block_text_link_rel` | text | No | Rel attribute for text link |

## Notes

- Block name uses an **underscore**: `acf/product_cards` (not a hyphen)
- `pc_block_product_image` return format is `url` (not array or ID)
- This is a simple card — no repeaters

## Example

```html
<!-- wp:acf/product_cards {"name":"acf/product_cards","data":{"pc_block_title":"ScalaHosting","pc_block_title_color":"#FFFFFF","pc_block_title_bg_color":"#4a1d96","pc_block_product_image":"https://example.com/images/scalahosting-logo.png","pc_block_description":"Managed VPS hosting with proprietary SPanel control panel and SShield real-time security. Starting at $14.95/month.","pc_block_button_text":"Visit ScalaHosting","pc_block_button_url":"https://example.com/go/scalahosting/","pc_block_button_rel":"nofollow sponsored","pc_block_text_link":"Read Review","pc_block_text_link_url":"https://example.com/scalahosting-review/","pc_block_text_link_rel":""}} /-->
```
