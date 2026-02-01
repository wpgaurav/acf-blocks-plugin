# Gallery Block — LLM Instructions

Use `<!-- wp:acf/gallery -->` with a JSON `data` attribute. Fields use the `acf_gallery_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_gallery_images` | gallery (array) | Yes | Array of image attachment IDs |
| `acf_gallery_layout` | select | No | `"grid"` (default), `"masonry"`, or `"carousel"` |
| `acf_gallery_columns` | select | No | `"2"`, `"3"` (default), `"4"`, or `"5"` |
| `acf_gallery_gap` | select | No | `"small"`, `"medium"` (default), or `"large"` |
| `acf_gallery_enable_lightbox` | `"1"` or `"0"` | No | Default `"1"`. Opens images in lightbox on click |
| `acf_gallery_class` | text | No | Custom CSS class |
| `acf_gallery_inline` | text | No | Inline CSS styles |

## Notes

- `acf_gallery_images` uses WordPress attachment IDs (return format is `array`)
- This block requires images to already exist in the WordPress media library
- Not practical for LLM-generated content unless attachment IDs are known

## Example

```html
<!-- wp:acf/gallery {"name":"acf/gallery","data":{"acf_gallery_images":["1001","1002","1003","1004","1005","1006"],"acf_gallery_layout":"grid","acf_gallery_columns":"3","acf_gallery_gap":"medium","acf_gallery_enable_lightbox":"1"}} /-->
```
