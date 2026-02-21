# ACF Product Cards Block — LLM Prompt

Create a product card block with title header, image, description, and dual call-to-action (button + text link).

## Block Info

- **Block Name:** `acf/product-cards`
- **Description:** A customizable product card block.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pc_block_title` | Card Title | text | Header/badge title |
| `field_pc_block_title_color` | Title Text Color | color_picker | Header text color |
| `field_pc_block_title_bg_color` | Title Background | color_picker | Header background color |
| `field_pc_block_product_image` | Product Image | image (url) | Product image |
| `field_pc_block_description` | Description | textarea | Product description text |
| `field_pc_block_root_class` | Root Class | text | Custom CSS class for the card |
| `field_pc_block_button_text` | Primary Button Text | text | Main CTA button label |
| `field_pc_block_button_url` | Primary Button URL | url | Main CTA link |
| `field_pc_block_button_rel` | Primary Button Rel | text | e.g. "nofollow sponsored" |
| `field_pc_block_text_link` | Secondary Link Text | text | Text link label |
| `field_pc_block_text_link_url` | Secondary Link URL | url | Text link URL |
| `field_pc_block_text_link_rel` | Secondary Link Rel | text | e.g. "nofollow" |

## Field Rules

- All keys use `field_` prefix
- Image field returns URL format (not array)
- Card has two CTA zones: a primary button and a secondary text link
- Title serves as a colored header/badge at the top of the card
- Color pickers customize the title header appearance

## Instructions

1. Set the card title and choose header colors
2. Add a product image
3. Write a concise product description
4. Configure primary button (main CTA) with text, URL, and rel
5. Configure secondary text link for additional action (e.g. "Read Review")
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/product-cards {"name":"acf/product-cards","data":{"field_pc_block_title":"Editor's Choice","field_pc_block_title_color":"#ffffff","field_pc_block_title_bg_color":"#2563eb","field_pc_block_product_image":"1057243","field_pc_block_description":"ScalaHosting offers managed VPS hosting with their proprietary SPanel. Best for WordPress users who need dedicated resources at shared hosting prices.","field_pc_block_button_text":"Visit ScalaHosting","field_pc_block_button_url":"https://gauravtiwari.org/go/scalahosting/","field_pc_block_button_rel":"nofollow sponsored","field_pc_block_text_link":"Read Full Review","field_pc_block_text_link_url":"https://gauravtiwari.org/scalahosting-review/","field_pc_block_text_link_rel":""}} /-->
```

## Example — Simple card without secondary link

```html
<!-- wp:acf/product-cards {"name":"acf/product-cards","data":{"field_pc_block_title":"Best Value","field_pc_block_title_color":"#ffffff","field_pc_block_title_bg_color":"#16a34a","field_pc_block_product_image":"1057250","field_pc_block_description":"Affordable cloud hosting with enterprise-grade features. Perfect for startups and small businesses.","field_pc_block_button_text":"Get Started","field_pc_block_button_url":"https://example.com/signup","field_pc_block_button_rel":"nofollow sponsored"}} /-->
```
