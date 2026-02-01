# Post Display Block — LLM Instructions

Use `<!-- wp:acf/post-display -->` with a JSON `data` attribute. Fields use the `pd_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `pd_selected_posts` | relationship (array) | Yes | Array of post IDs. Post types: post, page, deal, snippet, tool |
| `pd_layout` | select | No | `"text_links"` (default), `"thumbnail"`, or `"grid"` |
| `pd_columns` | select | No | `"2"` (default) or `"3"`. Only when `pd_layout` is `"grid"` |
| `pd_show_excerpt` | `"1"` or `"0"` | No | Show excerpt. Hidden when layout is `"text_links"` |
| `pd_show_date` | `"1"` or `"0"` | No | Show post date |
| `pd_show_author` | `"1"` or `"0"` | No | Show author. Hidden when layout is `"text_links"` |
| `pd_title_tag` | select | No | `"h2"`, `"h3"` (default), `"h4"`, `"h5"`, `"h6"`, `"p"`, `"span"`. Hidden for `"text_links"` |
| `pd_custom_class` | text | No | Custom CSS class |
| `pd_show_read_more` | `"1"` or `"0"` | No | Show read more link |
| `pd_read_more_text` | text | No | Default `"Read More"`. Only when `pd_show_read_more` is `"1"` |

## Notes

- `pd_selected_posts` requires WordPress post IDs that already exist in the database
- Not practical for fully LLM-generated content unless post IDs are known
- Return format is `object` (ACF returns full post objects internally)

## Example

```html
<!-- wp:acf/post-display {"name":"acf/post-display","data":{"pd_selected_posts":["101","205","310"],"pd_layout":"grid","pd_columns":"3","pd_show_excerpt":"1","pd_show_date":"1","pd_show_author":"0","pd_title_tag":"h3","pd_show_read_more":"1","pd_read_more_text":"Read More"}} /-->
```
