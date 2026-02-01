# Opinion Box Block — LLM Instructions

Use `<!-- wp:acf/opinion-box -->` with a JSON `data` attribute. Fields use the `ob_` prefix. This is an InnerBlocks block — the opinion content goes between the opening and closing tags.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `ob_avatar` | image (array) | No | Author avatar from media library. Return format is `array` |
| `ob_avatar_url` | URL string | No | Direct avatar image URL. Alternative to media library |
| `ob_name` | text | No | Default `"Author Name"` |
| `ob_designation` | text | No | Default `"Title or Designation"` |
| `ob_citation` | text | No | Citation or source reference |

## Notes

- Use `ob_avatar` (attachment) OR `ob_avatar_url` (direct URL), not both
- The opinion/quote content is placed as InnerBlocks between the block tags
- This block is for editorial opinions, expert quotes, or attributed commentary

## Example

```html
<!-- wp:acf/opinion-box {"name":"acf/opinion-box","data":{"ob_avatar_url":"https://example.com/images/author.jpg","ob_name":"Gaurav Tiwari","ob_designation":"WordPress Developer","ob_citation":"Based on 3 years of usage"}} -->
<!-- wp:paragraph -->
<p>ScalaHosting offers the best balance of performance and price in the managed VPS market. Their SPanel control panel is a genuine cPanel alternative that saves you money without sacrificing functionality.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/opinion-box -->
```
