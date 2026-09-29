# ACF Product Box Block — LLM Prompt

Convert a product URL to an ACF Product Box block. Fetch the page to extract product details.

## Block Info

- **Block Name:** `acf/product-box`
- **Description:** Amazon-style product listing with image, pricing, features, ratings, and multiple CTA buttons.
- **Styles:** Default, Top Image (`is-style-top-image`), No Image (`is-style-no-image`)

## Layout

Two-row design:

1. **Top row (grid):** Image (left, 260–300px) + label, rank + title + score ring, rating, verdict, spec chips, features (right)
2. **Bottom row (full-width):** Description, then price block (pricing, price note, "You save", price-checked date) beside the CTA buttons, then perks and disclosure — separated from top by a subtle `border-top`

The bottom section only renders if pricing, description, or buttons exist.

## Image Sizing

The server always sends an uncropped core size; cropping is CSS-only.

- **Default style:** `medium_large` (768px wide). Show full image (`contain`, default) caps it at 360px tall; Fill & crop (`cover`) fills a square frame
- **Top Image style:** `large` (1024px wide). `field_pb_image_ratio` `auto` keeps the image's own shape (max 420px tall); `16-9`, `4-3`, `1-1` set a fixed frame, which `contain` fits inside and `cover` fills
- Fallback to `medium` when the requested size doesn't exist
- **Same-domain URL:** Attachment ID looked up via `attachment_url_to_postid()`, served at appropriate size
- **External URL:** Passed through unchanged (no size manipulation)
- Subdomain matching supported (e.g. `cdn.example.com` matches `example.com`)

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
| `field_pb_title_rel` | Title & Image Link Rel | text | e.g. "nofollow sponsored" when the title URL is an affiliate link |
| `field_pb_title_tag` | Title Heading Level | select | `p` (default), `h2`, `h3`, `h4`, `h5`, `h6` |
| `field_pb_label` | Highlight Label | text | Pill above title, e.g. "Best Overall". Adds accent border |
| `field_pb_rank` | Rank | number | 1–99, shown as "#1" beside the title. Omit to hide |
| `field_pb_score` | Review Score | number | Editor score shown as a ring, e.g. `9.2`. Omit to hide |
| `field_pb_score_max` | Score Out Of | select | `10` (default), `5`, `100` |
| `field_pb_score_label` | Score Label | text | Defaults to "Our score" |
| `field_pb_verdict` | Verdict | text | One-line reason it wins, shown above features |
| `field_pb_verdict_label` | Verdict Label | text | Defaults to "Why it wins" |
| `field_pb_specs` | Spec Chips | repeater | Max 8 short facts shown as pills |
| — `field_pb_spec_text` | Spec | text | e.g. `49"`, `144Hz`, `5120×1440` |
| `field_pb_rating` | Rating | number | 0–5, step 0.5 |
| `field_pb_rating_count` | Rating Count | text | e.g. "1,234 ratings" |
| `field_pb_features` | Features | repeater | Product bullet features |
| — `field_pb_feature_text` | Feature | text | Feature bullet text |
| `field_pb_original_price` | Original Price | text | e.g. "$99.99" |
| `field_pb_discount_percent` | Discount | text | e.g. "-15%" |
| `field_pb_current_price` | Current Price | text | e.g. "$84.99" |
| `field_pb_price_note` | Price Note | text | e.g. "Free shipping" |
| `field_pb_show_savings` | Show 'You Save' | true_false | `"1"` computes "You save $X" from original and current price |
| `field_pb_price_checked` | Price Checked On | date_picker | `Ymd`, e.g. `"20260929"`. Shown as "Price checked 29 Sep 2026" |
| `field_pb_perks` | Perks | repeater | Max 6 icon + text items |
| — `field_pb_perk_icon` | Icon | select | `check`, `truck`, `return`, `shield`, `lock`, `tag`, `clock`, `gift` |
| — `field_pb_perk_text` | Text | text | e.g. "Free shipping", "30-day returns" |
| `field_pb_description` | Description | wysiwyg | Short product description |
| `field_pb_buttons` | CTA Buttons | repeater | Max 4 buttons |
| — `field_pb_cta_text` | Button Text | text | Button label |
| — `field_pb_cta_url` | Button URL | url | Destination URL |
| — `field_pb_cta_style` | Button Style | select | primary, secondary, amazon, amazon-yellow, custom |
| — `field_pb_cta_icon` | Button Icon | select | none, cart, amazon, external, check |
| — `field_pb_cta_class` | CSS Class | text | e.g. "md-icon-external", "md-icon-download" |
| — `field_pb_cta_rel` | Rel Attribute | text | e.g. "nofollow noopener sponsored" |
| `field_pb_cta_emphasis` | Emphasize First Button | true_false | `"1"`: first button larger and accent-filled, the rest become outlines |
| `field_pb_btn_arrow` | Arrow on First Button | true_false | `"1"` adds an arrow that nudges on hover |
| `field_pb_btn_shine` | Shine on First Button | button_group | `off` (default), `hover`, `repeat` |
| `field_pb_new_tab` | Open Links in New Tab | true_false | `"1"` or `"0"` (default). Adds `noopener` automatically |
| `field_pb_disclosure` | Disclosure | text | Small print under buttons |
| `field_pb_box_style` | Box Style | button_group | `standard` (default) or `spotlight` (gradient border, tint, glow) |
| `field_pb_accent_color` | Accent Color | color_picker | Hex, e.g. `#ea580c`. Drives label, rank, score, verdict, Spotlight border, primary buttons |
| `field_pb_image_fit` | Image Fit | button_group | `contain` (default, whole image) or `cover` (fill & crop) |
| `field_pb_image_ratio` | Top Image Frame | select | Top Image only: `auto` (default), `16-9`, `4-3`, `1-1` |

## Field Rules

- All keys use `field_` prefix (`field_pb_title`, NOT `pb_title`)
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Repeaters use nested `row-N` objects: `{"row-0":{"field_pb_feature_text":"..."}}`
- `field_pb_image`: empty string `""` when using `field_pb_image_url` directly
- `field_pb_cta_icon`: `"none"`, `"cart"`, `"external"`, or `"check"`
- `field_pb_cta_class`: CSS icon class (e.g. `"md-icon-download"`, `"md-icon-external"`). When using custom class, set `field_pb_cta_icon` to `"none"`
- Optional fields can be omitted entirely
- `field_pb_title_tag`: defaults to `"p"` if omitted. Set to `"h2"` through `"h6"` for heading tags
- `field_pb_title_rel`: set to `"nofollow sponsored"` whenever `field_pb_title_url` is an affiliate link
- `field_pb_image_fit` / `field_pb_image_ratio`: omit to show the whole image. For a grid of Top Image boxes, set the same `field_pb_image_ratio` on each
- Every highlight field is optional. Omit what you don't have; never invent a score, rank or verdict
- `field_pb_rank`: set on "best X" listicles, in list order
- `field_pb_price_checked`: set to today's date (`Ymd`) whenever prices are shown, especially Amazon prices
- `field_pb_show_savings`: only works when both prices use the same currency symbol; it hides itself otherwise
- `field_pb_box_style` `"spotlight"`: use on one box per article (the top pick), not every box
- For Amazon buttons that should stand out, use `field_pb_cta_style` `"amazon-yellow"` with `field_pb_cta_emphasis` `"1"`
- Block outputs `data-acf-block="product-box"` attribute (used by TOC filtering)

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

## Example — Top pick with highlights

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"field_pb_image":"","field_pb_image_url":"https://example.com/image.jpg","field_pb_label":"Best Overall","field_pb_rank":"1","field_pb_score":"9.2","field_pb_score_max":"10","field_pb_verdict":"The widest screen you can buy without a multi-monitor mess.","field_pb_specs":{"row-0":{"field_pb_spec_text":"49\""},"row-1":{"field_pb_spec_text":"144Hz"}},"field_pb_badge_text":"SAVE 6%","field_pb_badge_color":"#22c55e","field_pb_title":"Product Title","field_pb_title_url":"https://www.amazon.com/dp/ASIN/?tag=gtorg0f-20","field_pb_title_rel":"nofollow sponsored","field_pb_rating":"4.5","field_pb_rating_count":"1,234 ratings","field_pb_features":{"row-0":{"field_pb_feature_text":"Feature one"}},"field_pb_original_price":"$988.15","field_pb_discount_percent":"-6%","field_pb_current_price":"$927.58","field_pb_show_savings":"1","field_pb_price_checked":"20260929","field_pb_perks":{"row-0":{"field_pb_perk_icon":"truck","field_pb_perk_text":"Free shipping"},"row-1":{"field_pb_perk_icon":"return","field_pb_perk_text":"30-day returns"}},"field_pb_buttons":{"row-0":{"field_pb_cta_text":"Check Price on Amazon","field_pb_cta_url":"https://www.amazon.com/dp/ASIN/?tag=gtorg0f-20","field_pb_cta_style":"amazon-yellow","field_pb_cta_icon":"none","field_pb_cta_class":"","field_pb_cta_rel":"nofollow noopener sponsored"}},"field_pb_cta_emphasis":"1","field_pb_btn_arrow":"1","field_pb_btn_shine":"repeat","field_pb_box_style":"spotlight","field_pb_disclosure":"As an Amazon Associate I earn from qualifying purchases."},"mode":"preview"} /-->
```

## Example — Top Image variation

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"field_pb_image":"","field_pb_image_url":"https://example.com/wide-image.jpg","field_pb_badge_text":"BEST SELLER","field_pb_badge_color":"#22c55e","field_pb_title":"Product Title","field_pb_title_url":"https://example.com","field_pb_rating":"4.5","field_pb_rating_count":"2,500 ratings","field_pb_features":{"row-0":{"field_pb_feature_text":"Feature one"},"row-1":{"field_pb_feature_text":"Feature two"}},"field_pb_original_price":"$149.99","field_pb_discount_percent":"-20%","field_pb_current_price":"$119.99","field_pb_description":"Short description.","field_pb_buttons":{"row-0":{"field_pb_cta_text":"Check Price on Amazon","field_pb_cta_url":"https://www.amazon.com/dp/ASIN/?tag=gtorg0f-20","field_pb_cta_style":"amazon","field_pb_cta_icon":"cart","field_pb_cta_class":"","field_pb_cta_rel":"nofollow noopener sponsored"}}},"className":"is-style-top-image","mode":"preview"} /-->
```

## Example — No Image variation

```html
<!-- wp:acf/product-box {"name":"acf/product-box","data":{"field_pb_badge_text":"FREE","field_pb_badge_color":"#22c55e","field_pb_title":"Product Title","field_pb_title_url":"https://example.com","field_pb_rating":"4.0","field_pb_rating_count":"500 ratings","field_pb_features":{"row-0":{"field_pb_feature_text":"Feature one"}},"field_pb_current_price":"Free","field_pb_description":"Short description.","field_pb_buttons":{"row-0":{"field_pb_cta_text":"Get It Free","field_pb_cta_url":"https://example.com","field_pb_cta_style":"primary","field_pb_cta_icon":"external","field_pb_cta_class":"","field_pb_cta_rel":"nofollow noopener sponsored"}}},"className":"is-style-no-image","mode":"preview"} /-->
```
