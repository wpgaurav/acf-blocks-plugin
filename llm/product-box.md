# ACF Product Box Block — LLM Prompt

Convert a product URL to an ACF Product Box block. Fetch the page to extract product details.

## Block Info

- **Block Name:** `acf/product-box`
- **Description:** Amazon-style product listing with image, pricing, features, ratings, and multiple CTA buttons.
- **Styles:** Default, No Image (`is-style-no-image`)

## Extraction Rules

1. **Title**: Clean product name from page title/h1 (remove brand clutter, ASIN, excessive keywords)
2. **Image**: Best product image from og:image or main product image
3. **Features**: Extract key specs/features from `<li>` items or product bullets (improve wording if needed)
4. **Prices**: original, discount %, current price
5. **Badge**: "SAVE X%" with color "#22c55e", or "FREE" for free products
6. **Description**: Short one-line summary of the product

## Button Rules

Always add multiple buttons where applicable:

**Amazon.com button:**
- URL format: `https://www.amazon.com/dp/ASIN/?tag=gtorg0f-20`
- Style: `"amazon"`, Icon: `"cart"`
- Text: "Check Price on Amazon"

**Amazon.in button:**
- URL format: `https://www.amazon.in/s?k=Product+Name+Keywords&tag=gaurtiwa-21`
- Style: `"primary"`, Icon: `"none"`, Class: `"md-icon-external"`
- Text: "Check on Amazon.in"

**External (non-Amazon) products:**
- No affiliate tags
- Use best image from the page
- Style: `"primary"`, Icon: `"none"`, Class: `"md-icon-external"`

All buttons get `field_pb_cta_rel`: `"nofollow noopener sponsored"`

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pb_image` | Product Image | image (array) | Empty string `""` when using URL |
| `field_pb_image_url` | Image URL | url | Direct URL (takes priority) |
| `field_pb_badge_text` | Badge Text | text | e.g. "SAVE 10%", "BEST SELLER" |
| `field_pb_badge_color` | Badge Color | color_picker | Default: `#22c55e` |
| `field_pb_title` | Product Title | text | Clean product name |
| `field_pb_title_url` | Title Link | url | Product page URL |
| `field_pb_rating` | Rating | number | 0–5, step 0.5 |
| `field_pb_rating_count` | Rating Count | text | e.g. "1,234 ratings" |
| `field_pb_features` | Features | repeater | Product bullet features |
| — `field_pb_feature_text` | Feature | text | Required sub-field |
| `field_pb_original_price` | Original Price | text | e.g. "$99.99" |
| `field_pb_discount_percent` | Discount | text | e.g. "-15%" |
| `field_pb_current_price` | Current Price | text | e.g. "$84.99" |
| `field_pb_price_note` | Price Note | text | e.g. "Free shipping" |
| `field_pb_description` | Description | wysiwyg | Short product description |
| `field_pb_buttons` | CTA Buttons | repeater | Max 4 buttons |
| — `field_pb_cta_text` | Button Text | text | Required |
| — `field_pb_cta_url` | Button URL | url | Required |
| — `field_pb_cta_style` | Button Style | select | primary, secondary, amazon, custom |
| — `field_pb_cta_icon` | Button Icon | select | none, cart, amazon, external, check |
| — `field_pb_cta_class` | CSS Class | text | e.g. "md-icon-external", "md-icon-download" |
| — `field_pb_cta_rel` | Rel Attribute | text | e.g. "nofollow noopener sponsored" |

## Field Rules

- All keys use `field_` prefix (`field_pb_title`, NOT `pb_title`)
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Repeaters use nested `row-N` objects: `{"row-0":{"field_pb_feature_text":"..."}}`
- `field_pb_image`: empty string `""` when using `field_pb_image_url` directly
- `field_pb_cta_icon`: `"none"`, `"cart"`, `"external"`, or `"check"`
- `field_pb_cta_class`: CSS icon class (e.g. `"md-icon-download"`, `"md-icon-external"`). When using custom class, set `field_pb_cta_icon` to `"none"`
- Optional fields can be omitted entirely

## Instructions

1. Fetch the product page URL to extract details
2. Clean and improve the product title and description
3. Extract features, pricing, and image
4. Build buttons with proper affiliate tags and styles
5. Output block comment markup

## Example — Default (with image)

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"field_pb_image":"","field_pb_image_url":"https://example.com/image.jpg","field_pb_badge_text":"SAVE 15%","field_pb_badge_color":"#22c55e","field_pb_title":"Product Title","field_pb_title_url":"https://example.com","field_pb_rating":"4.5","field_pb_rating_count":"1,234 ratings","field_pb_features":{"row-0":{"field_pb_feature_text":"Feature one"},"row-1":{"field_pb_feature_text":"Feature two"}},"field_pb_original_price":"$99.99","field_pb_discount_percent":"-15%","field_pb_current_price":"$84.99","field_pb_price_note":"Free shipping","field_pb_description":"Short description.","field_pb_buttons":{"row-0":{"field_pb_cta_text":"Check Price on Amazon","field_pb_cta_url":"https://www.amazon.com/dp/ASIN/?tag=gtorg0f-20","field_pb_cta_style":"amazon","field_pb_cta_icon":"cart","field_pb_cta_class":"","field_pb_cta_rel":"nofollow noopener sponsored"},"row-1":{"field_pb_cta_text":"Check on Amazon.in","field_pb_cta_url":"https://www.amazon.in/s?k=Product+Name&tag=gaurtiwa-21","field_pb_cta_style":"primary","field_pb_cta_icon":"none","field_pb_cta_class":"md-icon-external","field_pb_cta_rel":"nofollow noopener sponsored"}}},"mode":"preview"} /-->
```

## Example — No Image variation

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"field_pb_badge_text":"FREE","field_pb_badge_color":"#22c55e","field_pb_title":"Product Title","field_pb_title_url":"https://example.com","field_pb_rating":"4.0","field_pb_rating_count":"500 ratings","field_pb_features":{"row-0":{"field_pb_feature_text":"Feature one"}},"field_pb_current_price":"Free","field_pb_description":"Short description.","field_pb_buttons":{"row-0":{"field_pb_cta_text":"Get It Free","field_pb_cta_url":"https://example.com","field_pb_cta_style":"primary","field_pb_cta_icon":"external","field_pb_cta_class":"","field_pb_cta_rel":"nofollow noopener sponsored"}}},"className":"is-style-no-image","mode":"preview"} /-->
```
