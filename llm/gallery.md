# ACF Gallery Block — LLM Prompt

Create an image gallery block with multiple layout options and optional lightbox support.

## Block Info

- **Block Name:** `acf/gallery`
- **Description:** A responsive gallery block with multiple layout options.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_gallery_images` | Images | gallery (array) | WordPress media library gallery field |
| `acf_gallery_layout` | Layout | select | `grid`, `masonry`, `carousel` |
| `acf_gallery_columns` | Columns | select | 2, 3, 4, or 5 |
| `acf_gallery_gap` | Gap Size | select | `small`, `medium`, `large` |
| `acf_gallery_enable_lightbox` | Enable Lightbox | true_false | `"1"` for click-to-enlarge |
| `acf_gallery_class` | Custom Class | text | Optional CSS class |
| `acf_gallery_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Gallery field stores an array of WordPress media library image IDs
- First 4 images load eagerly; remaining images lazy-load
- Layout options: `grid` (uniform), `masonry` (varied heights), `carousel` (scrollable)
- Lightbox uses click-to-expand functionality when enabled

## Instructions

1. Select images from the WordPress media library
2. Choose the layout type (grid, masonry, or carousel)
3. Set the number of columns
4. Choose gap size between images
5. Enable lightbox for image viewing
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/gallery {"name":"acf/gallery","data":{"acf_gallery_images":["1001","1002","1003","1004","1005","1006"],"acf_gallery_layout":"grid","acf_gallery_columns":"3","acf_gallery_gap":"medium","acf_gallery_enable_lightbox":"1"}} /-->
```

## Example — Masonry layout

```html
<!-- wp:acf/gallery {"name":"acf/gallery","data":{"acf_gallery_images":["1001","1002","1003","1004","1005","1006","1007","1008"],"acf_gallery_layout":"masonry","acf_gallery_columns":"4","acf_gallery_gap":"small","acf_gallery_enable_lightbox":"1"}} /-->
```

## Note

The gallery field requires WordPress media library image IDs. When generating block markup programmatically, you'll need to know the attachment IDs of the images you want to include.
